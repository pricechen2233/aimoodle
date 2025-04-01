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
部署Moodle4.5.0+，并安装插件后，将该文件替换原来的forum插件，然后重启Moodle即可。

## 功能
1. 增加一个robot按钮，点击后可以针对该帖子及其父帖子进行机器人回复，实现机器人助教的功能。
2. 用户可以修改插件配置，使用不同的模型进行运行。

## 注意
1.该插件调用模型API KEY，需要单独配置/etc/apache2/sites-enabled/moodle.conf文件（moodle.conf文件），添加以下内容：
```
<VirtualHost *:80>
    ServerAdmin admin@example.com
    DocumentRoot /var/www/html/moodle
    ServerName example.com
    ServerAlias www.example.com
    SetEnv apikey "xxxxxx"
    SetEnv agentkey "xxxxxx"

···················

</VirtualHost>
```
2.在插件配置中按提示配置模型API url，以及相关信息。
3.该版本目前支持两种机器人基础模型，即GPT4o-mini和GPT3.5-turbo，支持ragflow的api调用，后续将持续引入更多模型，并优化机器人性能。


## 联系方式
如有问题，请联系：drchenforwork@163.com
