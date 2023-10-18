# wn-dingtalk-plugin

钉钉机器人

快速接入、使用方便、免费、实时的通知服务，支持多种消息类型

## 安装

由于包名的原因需要在项目根目录的 composer.json 文件中添加有自定义安装路径的代码
```
.
.
.
"extra": {
        "installer-paths": {
            "plugins/summer/{$name}/": ["vendor:summercms"]
        }
    }
.
.
.
```

```
composer require summercms/wn-dingtalk-plugin
```

```
php artisan winter:up
```
## 使用

### 配置应用凭证

钉钉开放平台》应用开发》钉钉应用》创建应用，点击应用名称查看应用凭证 AppKey 和 AppSecret

### 配置机器人代码和群ID

点击【应用功能】的【机器人与消息推送】，首次创建机器人并加入群聊。复制 RobotCode

点击【开发工具】【前端api】【会话】【根据corpid选择会话】（corpid是企业ID,点击头像右边下拉可复制）【发起调用】【钉钉扫码-链接成功】【选择群聊】获取 openConversationId

### 信息模版

模版信息支持多类型包括 文本类型，Markdown类型，链接类型，图片类型，卡片消息

信息内容支持变量替换，链接类型的封面图片和图片类型的图片可在调用的时候传参替换。

### 全局通过事件服务触发推送事件

```
$msg['template_code']='summer.dingtalk::robot.md'; // 模板标识代码
$msg['template_param']['nickname']='测试'; // 模板变量，替换模板中的{{nickname}}
$msg['template_param']['name']='张三';  // 模板变量，替换模板中的{{nickname}}
$msg['template_param']['contact']='15138948738'; // 模板变量，替换模板中的{{contact}}
$msg['template_param']['message']='你好，有人么';  // 模板变量，替换模板中的{{message}}

// picUrl 替换链接信息中的模版默认封面
$msg['template_param']['picUrl']='http://jianpu.summercms.com/themes/newlog/assets/img/jianpu-logo.png';
// photoURL 替换图片信息中的模板默认图片
$msg['template_param']['photoURL']='http://jianpu.summercms.com/themes/newlog/assets/img/jianpu-logo.png';

Event::fire('summer.dingtalk.robot.groupMessagesSend',[$msg]);

```
### 模板内容示例
```
### 有新的留言-{{nickname}}
##### 联系姓名:{{name}}
##### 联系方式:{{contact}}
##### 留言内容:{{message}}
```


