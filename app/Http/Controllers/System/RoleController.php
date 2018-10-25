<?php

namespace App\Http\Controllers\System;

use App\Http\Requests\RolePost;
use App\Models\MyPermission;
use App\Models\MyRole;
use App\Presenters\rolePermissionsPresenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = Role::all();
        $assign = [
            'all_role'=>$role,
        ];
        return view('system.role',$assign);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = Role::all();
        $assign = [
            'role'=>$role,
        ];
        return view('system.role_add',$assign);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RolePost $rolePost, MyRole $myRole)
    {
        $data = [];
        $data['name'] = $rolePost->name;
        $data['role_display_name'] = $rolePost->role_display_name;
        $data['role_description'] = $rolePost->role_description;
        $responseData = $myRole->createRole($data);
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
        $role_info = Role::findById($id);
        $rolePerms = new rolePermissionsPresenter();
        $perms = $rolePerms->groupPermissions($role_info->permissions);

        $role = [
            'role' => $role_info,
            'perms' =>$perms
        ];
        return response()->json($role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //获取当前角色
        $role_info = Role::findOrFail($id);
        $permissions = MyPermission::where([])
            ->orderBy('name','desc')
            ->get();
        $assign = [
            'role'=>$role_info,
            'permissions'=>$permissions,
        ];
        return view('system.role_edit',$assign);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RolePost $rolePost, MyRole $myRole)
    {
        $data = $rolePost->except(['_token','_method']);
        unset($data['id']);

        $responseData = $myRole->updateRole($rolePost->id,$data);

//        return redirect('system/role')->with(['msg'=>$responseData]);
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
    public function ajaxCheckRole(Request $request)
    {
        if ($request->type == 'create'){
            return MyRole::select('id')->where(['name'=>$request->name])->get();
        }elseif($request->type == 'edit'){
            $res = MyRole::select('id')->where(['name'=>$request->name])->get()->toarray();
            if ($res && $res[0]['id']!=$request->id){
                return 1;
            }else{
                return array(); //根据 js 的判断返回空数组
            }
        }

    }
}
