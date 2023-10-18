<?php namespace Summer\Dingtalk\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableCreateSummerDingtalkTemplates extends Migration
{
    public function up()
    {
        Schema::create('summer_dingtalk_templates', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('code'); // 代码
            $table->string('title')->nullable();// 标题
            $table->text('description')->nullable(); // 描述
            $table->text('content_html')->nullable();
            $table->text('content_text')->nullable();
            $table->text('content_md')->nullable();
            $table->string('message_url')->nullable(); //链接信息的链接地址
            $table->text('action_card')->nullable();//卡片信息
            $table->text('button_type')->nullable();//卡片按钮排列方式
            $table->string('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('summer_dingtalk_templates');
    }
}
