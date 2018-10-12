<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //若要在 非默认的数据库 创建表，则使用 以下创建
        // Schema::connection('库名')->create('users', function (Blueprint $table) {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('uid')->comment('用户id，自增主键');
            $table->string('trueName')->comment('真实姓名');
            $table->string('sex')->comment('性别');
            $table->tinyInteger('position_id')->comment('职务表id');
            $table->tinyInteger('dept_id')->comment('部门表id');
            $table->string('username')->unique()->comment('用户名，唯一');
            $table->string('password')->comment('密码');
            $table->tinyInteger('gid')->comment('分组id'); //分组id，2018年9月17日 觉得没用，暂时留着作为预留字段。
            $table->tinyInteger('state')->default(1)->comment('用户状态：1启用，0禁用');
            $table->integer('loginTimes')->comment('登录次数'); // 登录次数
            $table->dateTime('lastLoginTime')->comment('上次登录时间'); // 上次登录时间
            $table->string('lastLoginIP', 16)->comment('上次登录IP'); // 上次登录IP
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
