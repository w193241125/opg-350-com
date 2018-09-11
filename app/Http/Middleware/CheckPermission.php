<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class CheckPermission
{
    /**
     * 根据路由名称查询路由绑定的权限
     * 使用 Permission 门面查询用户是否具有权限
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $uri = Route::currentRouteName();
        $pm_info = Permission::where(['name' => $uri])->first();
        //如果查不到路由名对应的权限直接放行
        if (empty($pm_info)) {
            return $next($request);
        }
        //检查是否有权限
        if(!$request->user()->can($uri)){
            //ajax请求直接返回json
            if(Request::ajax()){
                return response()->json(["error" => "no_permissions"], 422);
            }
            abort(403,'无权访问！');
            //返回session('error');到原页面
            return back()->withInput()->withError('no_permissions');
        }
        //根据路由名称查询权限
        return $next($request);
    }
}
