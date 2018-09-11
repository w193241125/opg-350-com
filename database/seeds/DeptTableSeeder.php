<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Dept;

class DeptTableSeeder extends Seeder
{
    /**
     * 运行部门数据库填充
     *
     * @return void
     */
    public function run()
    {

        Dept::create(
            [
                'id' => 1,
                'dept_name' => '技术部',
                'dept' => '0',
            ]
        );
        Dept::create(
            [
                'id' => 2,
                'dept_name' => '市场部',
                'dept' => '0',
            ]
        );
        Dept::create(
            [
                'id' => 3,
                'dept_name' => '运营部',
                'dept' => '0',
            ]
        );
        Dept::create(
            [
                'id' => 4,
                'dept_name' => '财务部',
                'dept' => '0',
            ]
        );
        Dept::create(
            [
                'id' => 5,
                'dept_name' => '内服部',
                'dept' => '0',
            ]
        );
        Dept::create(
            [
                'id' => 6,
                'dept_name' => '客服部',
                'dept' => '0',
            ]
        );
        Dept::create(
            [
                'id' => 7,
                'dept_name' => '商务部',
                'dept' => '0',
            ]
        );
    }
}
