# 使用方法
部署Moodle4.5.0+，并安装插件后，将该文件替换原来的forum插件，然后重启Moodle即可。

# 功能
1. 增加一个robot按钮，点击后可以针对该帖子及其父帖子进行机器人回复，实现机器人助教的功能。
2. 用户可以修改插件配置，使用不同的模型进行运行。

# 注意
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
如果直接调用openai的模型，则填入apikey内容；如果调用ragflow的api，则填入agentkey内容。
2.在插件配置中填写API url，以及其他需要填写的配置内容。
3.该版本目前支持两种机器人基础模型，即GPT4o-mini和GPT3.5-turbo，支持基于ragflow的agent通过API调用，后续将持续引入更多模型，并优化机器人性能。


# 联系方式
如有问题，请联系：drchenforwork@163.com