<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Login block
 *
 * @package   block_login
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_login extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_login');
    }

    function applicable_formats() {
        return array('site' => true);
    }

    function get_content() {
        global $USER, $CFG, $SESSION, $OUTPUT;
        require_once($CFG->libdir . '/authlib.php');

        $wwwroot = '';
        $signup = '';

        if ($this->content !== NULL) {
            return $this->content;
        }

        $wwwroot = $CFG->wwwroot;

        if (signup_is_enabled()) {
            $signup = $wwwroot . '/login/signup.php';
        }
        // TODO: now that we have multiauth it is hard to find out if there is a way to change password
        $forgot = $wwwroot . '/login/forgot_password.php';


        $username = get_moodle_cookie();

        $this->content = new stdClass();
        $this->content->footer = '';
        $this->content->text = '';

        if (!isloggedin() or isguestuser()) {   // Show the block
            if (empty($CFG->authloginviaemail)) {
                $strusername = get_string('username');
            } else {
                $strusername = get_string('usernameemail');
            }

            $this->content->text .= "\n".'<form class="loginform" id="login" method="post" action="'.get_login_url().'">';

            $this->content->text .= '<div class="mb-3">';
            $this->content->text .= '<label for="login_username">'.$strusername.'</label>';
            $this->content->text .= '<input type="text" name="username" id="login_username" ';
            $this->content->text .= ' class="form-control" value="'.s($username).'" autocomplete="username"/></div>';

            $this->content->text .= '<div class="mb-3"><label for="login_password">'.get_string('password').'</label>';

            $this->content->text .= '<input type="password" name="password" id="login_password" ';
            $this->content->text .= ' class="form-control" value="" autocomplete="current-password"/>';
            $this->content->text .= '</div>';

            // ReCaptcha.
            if (login_captcha_enabled()) {
                require_once($CFG->libdir . '/recaptchalib_v2.php');
                $this->content->text .= '<div class="mb-3">';
                $this->content->text .= recaptcha_get_challenge_html(RECAPTCHA_API_URL, $CFG->recaptchapublickey,
                    current_language(), true);
                $this->content->text .= '</div>';
            }

            $this->content->text .= '<div class="mb-3">';
            $this->content->text .= '<input type="submit" class="btn btn-primary w-100" value="'.get_string('login').'" />';
            $this->content->text .= '</div>';
            $this->content->text .= '<input type="hidden" name="logintoken" value="'.s(\core\session\manager::get_login_token()).'" />';

            $this->content->text .= "</form>\n";

            if (!empty($signup)) {
                $this->content->text .= '<div><a href="'.$signup.'">'.get_string('startsignup').'</a></div>';
            }
            if (!empty($forgot)) {
                $this->content->text .= '<div><a href="'.$forgot.'">'.get_string('forgotaccount').'</a></div>';
            }

            $authsequence = get_enabled_auth_plugins(); // Get all auths, in sequence.
            $potentialidps = array();
            foreach ($authsequence as $authname) {
                $authplugin = get_auth_plugin($authname);
                $potentialidps = array_merge($potentialidps, $authplugin->loginpage_idp_list($this->page->url->out(false)));
            }

            if (!empty($potentialidps)) {
                $this->content->text .= '<div class="potentialidps">';
                $this->content->text .= '<h6>' . get_string('potentialidps', 'auth') . '</h6>';
                $this->content->text .= '<div class="potentialidplist">';
                foreach ($potentialidps as $idp) {
                    $this->content->text .= '<div class="potentialidp">';
                    $this->content->text .= '<a class="btn btn-secondary w-100" ';
                    $this->content->text .= 'href="' . $idp['url']->out() . '" title="' . s($idp['name']) . '">';
                    if (!empty($idp['iconurl'])) {
                        $this->content->text .= '<img src="' . s($idp['iconurl']) . '" width="24" height="24" class="me-1"/>';
                    }
                    $this->content->text .= s($idp['name']) . '</a></div>';
                }
                $this->content->text .= '</div>';
                $this->content->text .= '</div>';
            }
        }

        return $this->content;
    }
}
