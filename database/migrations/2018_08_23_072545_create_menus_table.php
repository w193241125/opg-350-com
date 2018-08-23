<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->comment('1:项目 2:栏目 3:菜单');
            $table->Integer('cid')->comment('项目id');
            $table->Integer('pid')->comment('栏目id');
            $table->string('name', 20)->comment('名称');
            $table->string('permission_name', 50)->comment('权限控制名称，只能是英文');
            $table->tinyInteger('order_id')->comment('排序id');
            $table->tinyInteger('state')->comment('状态，1:正常，2:隐藏');
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
        Schema::dropIfExists('menus');
    }
}
