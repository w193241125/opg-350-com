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
        for ($i=0;$i<20;$i++){
            DB::table('users')->insert(
                [
                    'trueName' => 'PHP-开发工程师',
                    'sex' => '男',
                    'position_id' => random_int(1,13),
                    'dept_id' => random_int(1,7),
                    'username' => 'jishubu'.$i,
                    'password' => bcrypt('123456'),
                    'gid' => 1,
                    'loginTimes' => 0,
                    'lastLoginTime' => '2017-7-16 07:35:12',
                    'lastLoginIP' => '192.168.0.0.1',
                    'created_at' => '2017-7-16 07:35:12',
                    'updated_at' => '2016-7-16 07:35:12',
                ]
            );
        }

    }
}
