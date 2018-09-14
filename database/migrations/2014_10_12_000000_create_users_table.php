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
            $table->increments('uid');  //用户id，自增主键
            $table->string('trueName'); //真实姓名
            $table->string('sex');  //性别
            $table->tinyInteger('position_id'); //职务
            $table->tinyInteger('dept_id'); // 部门
            $table->string('username')->unique(); //用户名，唯一
            $table->string('password'); //密码
            $table->tinyInteger('gid'); //分组id，
            $table->integer('loginTimes'); // 登录次数
            $table->dateTime('lastLoginTime'); // 上次登录时间
            $table->string('lastLoginIP', 16); // 上次登录IP
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
