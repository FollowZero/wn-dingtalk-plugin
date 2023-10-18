<?php namespace Summer\Dingtalk\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Event;

use Summer\Dingtalk\Classes\Common;

class Welcome extends Controller
{
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Summer.dingtalk', 'main-menu-item-dingtalk', 'side-menu-item-welcome');
    }
    public function index()
    {
//        $msg['template_code']='summer.dingtalk::robot.text';

//        $msg['template_code']='summer.dingtalk::robot.md';
//        $msg['template_param']['nickname']='测试';
//        $msg['template_param']['name']='张三';
//        $msg['template_param']['contact']='15138948738';
//        $msg['template_param']['message']='你好，有人么';
//
//        $msg['template_code']='summer.dingtalk::robot.link';
//        $msg['template_param']['picUrl']='http://jianpu.summercms.com/themes/newlog/assets/img/jianpu-logo.png';
//
//        $msg['template_code']='summer.dingtalk::robot.photo';
//        $msg['template_param']['photoURL']='http://jianpu.summercms.com/themes/newlog/assets/img/jianpu-logo.png';
//
        $msg['template_code']='summer.dingtalk::robot.card';


        Event::fire('summer.dingtalk.robot.groupMessagesSend',[$msg]);
        $this->pageTitle="欢迎页";
    }
}
