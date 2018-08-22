<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * 运行数据填充。
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            0 =>[
                'uid' => 1,
                'trueName' => '刘冠生',
                'sex' => '男',
                'position' => 1,
                'dept' => 1,
                'username' => 'jishubu',
                'password' => '123456',
                'gid' => 1,
                'loginTimes' => 0,
                'lastLoginTime' => '2017-7-16 07:35:12',
                'lastLoginIP' => '192.168.0.0.1',
                'created_at' => '2017-7-16 07:35:12',
                'updated_at' => '2016-7-16 07:35:12',
            ],
        ]);
    }
}
