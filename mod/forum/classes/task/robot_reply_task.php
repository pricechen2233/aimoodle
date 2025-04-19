<?php

/**
 * Extract context vault preprocessor.
 *
 * @package    mod_forum
 * @copyright  2025 Price Chen <drchenforwork@163.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\task;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once('../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');
use mod_forum\local\container as container;
use mod_forum\local\vaults\robot as robot_process;
use mod_forum\subscriptions as subscriptions;
use stdClass;

class robot_reply_task extends \core\task\adhoc_task {
    public function execute() {
        $data = $this->get_custom_data();
        $user = \core_user::get_user($data->userid);
        
        // 初始化空会话
        \core\session\manager::init_empty_session(); 
        \core\session\manager::set_user($user);

        if (!CLI_SCRIPT && !defined('BEHAT_SITE_RUNNING')) {
            throw new \moodle_exception('taskexecutionerror', 'forum', '', 
                '本任务只能在CLI模式下执行');
        }

        // 在任务类中添加调试
        mtrace('当前用户：' . $data->userid . ' 执行环境：' . (CLI_SCRIPT ? 'CLI' : 'WEB'));

        // 获取实体
        $vaultfactory = container::get_vault_factory();
        $postvault = $vaultfactory->get_post_vault();
        $discussionvault = $vaultfactory->get_discussion_vault();
        $forumvault = $vaultfactory->get_forum_vault();

        $parententity = $postvault->get_from_id($data->parentid);
        $discussionentity = $discussionvault->get_from_id($data->discussionid);
        $forumentity = $forumvault->get_from_id($data->forumid);

        // 验证实体有效性
        if (!$parententity || !$discussionentity || !$forumentity) {
            error_log("Invalid entities found, aborting task.");
            return;
        }

        // 重新检查权限
        $managerfactory = container::get_manager_factory();
        $capabilitymanager = $managerfactory->get_capability_manager($forumentity);
        if (!$capabilitymanager->can_use_robot($user, $discussionentity, $parententity)) {
            error_log("User {$user->id} no longer has permission.");
            return;
        }

        $legacydatamapperfactory = container::get_legacy_data_mapper_factory();
        // 检查讨论状态
        $forum = $legacydatamapperfactory->get_forum_data_mapper()->to_legacy_object($forumentity);
        $discussion = $legacydatamapperfactory->get_discussion_data_mapper()->to_legacy_object($discussionentity);
        if (forum_discussion_is_locked($forum, $discussion)) {
            error_log("Discussion locked, aborting.");
            return;
        }

        // 执行机器人处理
        $myrobot = new robot_process($parententity, $discussionentity, $forumentity);
        $robotresponse = $myrobot->call_robot(
            $legacydatamapperfactory->get_post_data_mapper()->to_legacy_object($parententity),
            $discussion,
            $forum
        );

        // 构建帖子数据
        $post = new stdClass();
        $post->course = $data->courseid;
        $post->forum = $data->forumid;
        $post->discussion = $data->discussionid;
        $post->parent = $data->parentid;
        $post->subject = $data->subject;
        $post->userid = $data->userid;
        $post->parentpostauthor = $data->parentauthorid;
        $post->message = $robotresponse;
        $post->groupid = $data->groupid;

        // 处理标题格式
        $strre = 'Robot reply to me:';
        $pattern = '/Subject:([\s\S]*?)Message:/';
        if (!(substr($post->subject, 0, strlen($strre)) == $strre)) {
            if (preg_match($pattern, $post->message, $matches)) {
                $post->subject = trim($matches[1]);
            }
            $post->subject = $strre . ' ' . $post->subject;
        }

        // 提交帖子
        try {
            $newpostid = forum_add_new_post($post, $forum);
            
            // 获取完整的帖子对象
            $newpost = $post;

            $directurl = new \moodle_url('/mod/forum/discuss.php', ['d' => $discussion->id]);
            $directurl->set_anchor('p' . $newpost->id);
            
            // 获取相关上下文对象
            $cm = get_coursemodule_from_instance('forum', $forum->id, $forum->course);
            $modcontext = \context_module::instance($cm->id);

            // 获取接收用户对象
            $recipient = \core_user::get_user($data->userid);

            // 构造通知消息
            $message = new \core\message\message();
            $message->component = 'mod_forum';          // 组件必须为论坛模块
            $message->name = 'posts';                   // 通知类型标识符
            $message->userfrom = \core_user::get_noreply_user(); // 发件人为“不回复”用户
            $message->userto = $recipient->id;          // 指定目标用户
            $message->subject = get_string('newpostnotify', 'forum'); // 语言字符串

            // 纯文本内容（不含HTML标签）
            $message->fullmessage = format_string($newpost->message, true, ['context' => $modcontext]) 
            . "\n\n查看回复: " . $directurl->out(false);
            $message->fullmessageformat = FORMAT_PLAIN;

            // HTML内容（包含可点击链接）
            $message->fullmessagehtml = format_text($newpost->message, $newpost->messageformat, ['context' => $modcontext])
                . "<p><a href='{$directurl->out(false)}'>直接查看机器人回复</a></p>";

            $message->smallmessage = get_string('newpostsmall', 'forum', fullname($user)); // 简短消息
            $message->notification = 1;                 // 标记为通知（非私信）
            $message->contexturl = $directurl; // 讨论链接
            $message->contexturlname = format_string($discussion->name); // 上下文名称
            $message->courseid = $forum->course;        // 关联课程ID

            // 发送通知
            $messageid = message_send($message);
            mtrace("已发送通知到用户 {$data->userid},消息ID: {$messageid}");
        } catch (\Exception $e) {
            error_log("Error adding reply: " . $e->getMessage());
        }
    }
}
