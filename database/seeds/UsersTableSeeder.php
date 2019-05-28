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
        DB::table('users')->insert(
            [
                'trueName' => 'PHP-开发工程师',
                'sex' => '男',
                'position_id' => 1,
                'dept_id' => 1,
                'username' => 'larwas',
                'password' => bcrypt('123456'),
                'gid' => 1,
                'loginTimes' => 1,
                'lastLoginTime' => '2018-7-16 07:35:12',
                'lastLoginIP' => '192.168.0.1',
                'created_at' => '2018-7-16 07:35:12',
                'updated_at' => '2018-7-16 07:35:12',
            ]
        );
    }
}
