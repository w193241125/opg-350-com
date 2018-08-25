<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;


class MyPermission extends Permission
{
    /**
     * 创建用户权限
     * @param object $request
     * @return bool
     * */
    public function createPermission($request)
    {
        $this->name = $request->name;
        $this->display_name = $request->display_name;
        $this->description  = $request->description;
        $this->uri  = $request->uri;
        $this->save();
        return $this->id;
    }
}
