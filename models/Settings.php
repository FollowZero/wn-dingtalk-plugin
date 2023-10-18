<?php namespace Summer\Dingtalk\Models;

use Model;

/**
 * Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'dingtalk_settings';
    public $settingsFields = 'fields.yaml';

}
