# Moodle

<p align="center"><a href="https://moodle.org" target="_blank" title="Moodle Website">
  <img src="https://raw.githubusercontent.com/moodle/moodle/main/.github/moodlelogo.svg" alt="The Moodle Logo">
</a></p>

[Moodle][1] is the World's Open Source Learning Platform, widely used around the world by countless universities, schools, companies, and all manner of organisations and individuals.

Moodle is designed to allow educators, administrators and learners to create personalised learning environments with a single robust, secure and integrated system.

## Documentation

- Read our [User documentation][3]
- Discover our [developer documentation][5]
- Take a look at our [demo site][4]

## Community

[moodle.org][1] is the central hub for the Moodle Community, with spaces for educators, administrators and developers to meet and work together.

You may also be interested in:

- attending a [Moodle Moot][6]
- our regular series of [developer meetings][7]
- the [Moodle User Association][8]

## Installation and hosting

Moodle is Free, and Open Source software. You can easily [download Moodle][9] and run it on your own web server, however you may prefer to work with one of our experienced [Moodle Partners][10].

Moodle also offers hosting through both [MoodleCloud][11], and our [partner network][10].

## License

Moodle is provided freely as open source software, under version 3 of the GNU General Public License. For more information on our license see

[1]: https://moodle.org
[2]: https://moodle.com
[3]: https://docs.moodle.org/
[4]: https://sandbox.moodledemo.net/
[5]: https://moodledev.io
[6]: https://moodle.com/events/mootglobal/
[7]: https://moodledev.io/general/community/meetings
[8]: https://moodleassociation.org/
[9]: https://download.moodle.org
[10]: https://moodle.com/partners
[11]: https://moodle.com/cloud
[12]: https://moodledev.io/general/license

# 本项目特色
## 使用方法
部署Moodle4.5.0+，并安装插件后，将该文件中的mod/forum替换原来的forum插件，然后重启Moodle即可。也可以直接clone本项目使用。

## 功能
1. 增加一个robot按钮，点击后可以针对该帖子及其父帖子进行机器人回复，实现机器人助教的功能。
2. 用户可以修改插件配置，使用不同的模型进行运行。

## 注意
1.该插件调用模型API KEY，需要在插件配置页面填写，请注意获取相关API KEY参数。
2.在插件配置中按提示配置模型API url，以及相关信息。
3.该版本目前支持两种机器人基础模型，即GPT4o-mini和GPT3.5-turbo，支持ragflow的api调用，后续将持续引入更多模型，并优化机器人性能。
4.最新更新了task方法，确保在Moodle的admin/cli/cron.php周期性运作的同时，程序可以异步处理用户的机器人请求，并在机器人回复后，告知请求的用户，已经robot已经回复。
5.修改了mod/forum/classes/local/vaults/robot.php文件，使得可以在插件设置页面填写自己的API KEY。
特别说明：本轮修改在插件设置页面填写API KEY，尽管这样未必安全，但是本能能力有限没有选择，因为主要的robot task程序是在cli模式下工作的，似乎无法通过getenv函数读取apikey这是个问题，希望后面可以解决，这样可以保护用户的api key.


## 联系方式
如有问题，请联系开发者chen, drchenforwork@163.com
If you have any question or suggestion, please connect to the developer Chen, by email drchenforwork@163.com. Thank you.
