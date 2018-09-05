<?php

namespace App\Http\Controllers;

use App\Models\MyPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    protected $user;

    public function __construct()
    {
        //获取登录用户
        $this->middleware(function ($request, $next) {
            $this->user = $request->user();

            return $next($request);
        });
    }

    /**
     * 添加角色
     * @param string $name  角色名
     * @return boolean
     */
    public function addRole($name)
    {
        $user = Auth::user();
        if(!Role::create(['name'=>$name])){
            return false;
        }
        return true;
    }


}
