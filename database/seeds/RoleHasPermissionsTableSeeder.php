<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleHasPermissions extends Seeder
{
    /**
     * 运行数据填充。
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_has_permissions')->insert(
            [
                'permission_id' => '1',
                'role_id' => '1',
            ]
        );
    }
}
