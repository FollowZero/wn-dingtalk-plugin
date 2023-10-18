<?php namespace Summer\Dingtalk;

use Event;

use Summer\Dingtalk\Models\Settings;
use Summer\Dingtalk\Classes\Robot;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => '钉钉管理',
                'description' => '钉钉管理-钉钉机器人',
                'category'    => 'Summer',
                'icon'        => 'wn-icon-dashcube',
                'class'       => 'Summer\Dingtalk\Models\Settings',
                'order'       => 600,
            ]
        ];
    }

    public function register()
    {

        Event::listen('summer.dingtalk.robot.groupMessagesSend', function ($msg) {
            $Robot=new Robot();
            $Robot->groupMessagesSend($msg);
        });
    }
}
