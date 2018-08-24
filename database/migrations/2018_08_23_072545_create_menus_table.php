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
            $table->Integer('parent_id')->default(0)->comment('父级id');
            $table->integer('order')->default(0)->comment('排序id，asc');
            $table->string('name', 50)->comment('名称');
            $table->string('uri', 50)->comment('路由名也是权限控制名称，只能是英文');
            $table->string('icon', 50)->default('fa-circle-o')->comment('字体图标');
            $table->tinyInteger('state')->default(1)->comment('状态，1:正常，2:隐藏');
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
