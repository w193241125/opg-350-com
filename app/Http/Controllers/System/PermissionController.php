<?php

namespace App\Http\Controllers\System;

use App\Http\Requests\PermissionPost;
use App\Models\MyPermission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission = MyPermission::all();
        $assign = [
            'all_permission'=>$permission,
        ];
        return view('system.permission',$assign);
    }

    /**
     * 展示新建权限页面。
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = Role::all();
        $assign = [
            'role'=>$role,
        ];
        return view('system.permission_add',$assign);
    }

    /**
     * 执行新增权限操作。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionPost $permissionPost, MyPermission $myPermission)
    {
        $data = [];
        $data['name'] = $permissionPost->name;
        $data['pm_display_name'] = $permissionPost->pm_display_name;
        $data['pm_description'] = $permissionPost->pm_description;
        $data['pm_type'] = $permissionPost->pm_type;
        $responseData = $myPermission->createPermission($data);
        return response()->json($responseData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * 展示权限更新页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //获取当前权限
        $pm_info = MyPermission::find($id);
        $assign = [
            'pm_info'=>$pm_info,
        ];
        return view('system.permission_edit',$assign);
    }

    /**
     * 执行权限更新操作。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionPost $permissionPost, MyPermission $myPermission)
    {
        $data = $permissionPost->except(['_token','_method']);
        unset($data['id']);

        $map = ['id'=>$permissionPost->id];
        $responseData = $myPermission->updateData($map,$data);
        return response()->json($responseData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * AJAX 检查是否存在用户名
     *
     */
    public function ajaxCheckPermission(Request $request)
    {
        if ($request->type == 'create'){
            return MyPermission::select('id')->where(['name'=>$request->name])->get();
        }elseif($request->type == 'edit'){
            $res = MyPermission::select('id')->where(['name'=>$request->name])->get()->toarray();
            if ($res && $res[0]['id']!=$request->id){
                return 1;
            }else{
                return array(); //根据 js 的判断返回空数组
            }
        }

    }
}
