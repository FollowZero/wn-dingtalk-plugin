<?php namespace Summer\Dingtalk\Models;

use Model;
use Config;

/**
 * Model
 */
class DingtalkTemplateModel extends Model
{
    use \Winter\Storm\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'summer_dingtalk_templates';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var array Attribute names to encode and decode using JSON.
     */
    public $jsonable = ['action_card'];

    public function getTypeOptions(){
        return Config::get('summer.dingtalk::msgKey');
    }
    public $attachOne = [
        'pic' => 'System\Models\File', // 链接信息封面
        'photo' => 'System\Models\File', // 图片信息图片
    ];
}
