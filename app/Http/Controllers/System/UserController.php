<?php

namespace App\Http\Controllers\System;

use App\Http\Requests\UserPost;
use App\Models\Dept;
use App\Models\MyPermission;
use App\Models\MyRole;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * 展示用户列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_list = User::with('dept')->get();

        $assign = [
            //预加载获取用户职位和部门和拥有的角色
            'user_list'=>$user_list->load(['dept','position','roles'])
        ];
        return view('system.user',$assign);
    }

    /**
     * 展示创建用户表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //获取部门
        $dept = Dept::all();
        //获取职务
        $position = Position::all();

        $assign = [
            'dept'=>$dept,
            'position'=>$position,
        ];
        return view('system.user_add',$assign);
    }

    /**
     * 执行新建用户操作
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserPost $userPost, User $user)
    {
        $data = [];
        $data['username'] = $userPost->username;
        $data['trueName'] = $userPost->trueName;
        $data['password'] = $userPost->password ?  bcrypt($userPost->password) : bcrypt('123456');
        $data['position_id'] = $userPost->position_id;
        $data['dept_id'] = $userPost->dept_id;
        $data['sex'] = $userPost->sex;
        $responseData = $user->createUser($data);
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
     * 展示编辑页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //获取部门
        $dept = Dept::all();
        //获取职务
        $position = Position::all();
        //获取当前用户
        $user_info = User::find($id);
        $user_pm = $user_info->getAllPermissions();
        $assign = [
            'dept'=>$dept,
            'position'=>$position,
            'user_info'=>$user_info,
            'user_permission'=>$user_pm,
        ];
        return view('system.user_edit',$assign);
    }

    /**
     * 更新用户信息
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserPost $userPost, User $user)
    {

        $data = $userPost->except(['_token','_method']);
        if (empty($data['password'])){
            unset($data['password']);
        }
        unset($data['id']);

        $map = ['uid'=>$userPost->id];
        $responseData = $user->updateData($map,$data);
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
     * AJAX获取职务
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetPosition(Request $request) {
        $position = false;
        if ($request->dept_id != 0) {
            $position = Position::where('dept_id','=',$request->dept_id)->get();
        }
        return $position;
    }
    
    /**
     * AJAX 检查是否存在用户名
     * 
     */
    public function ajaxCheckUsername(Request $request)
    {
        if ($request->type == 'create'){
            return User::where(['username'=>$request->username])->get();
        }elseif($request->type == 'edit'){
            $res = User::where(['username'=>$request->username])->get()->toarray();
            if ($res && $res[0]['uid']!=$request->id){
                return 1;
            }else{
                return array(); //根据 js 的判断返回空数组
            }
        }

    }

    public function getUserPermission($uid)
    {
        $user = User::find($uid);
        $user_permission = $user->getAllPermissions();
        $all_permission = MyPermission::all();
        $permisson_html = $this->getPermissionsHTML($all_permission,$user_permission);
        $assign = [
            'user_permission'=>$user_permission,
            'all_permission'=>$all_permission,
            'uid'=>$uid,
            'permisson_html'=>$permisson_html,
        ];
        return view('system.user_edit_permission',$assign);
    }

    /**
     * 将用户权限分组排列
     * @param array $permissions 权限
     * @param array $userPer 用户组
     * @return string
     * */
    public function getPermissionsHTML($permissions, $userPer = [])
    {
        $html = '';
        $permissions = $this->groupPermissionsByType($permissions);
        $userPer = $this->getUserPermissions($userPer);
        foreach ($permissions as $key => $permission) {
            $key = htmlspecialchars($key);
            $html .= "<tr><td><span class='label label-sm label-success'>".$key." </span></td><td>";
            if (is_array($permission)){
                foreach ($permission as $value) {
                    $display_name = htmlspecialchars($value['pm_display_name']);
                    $checked = in_array($value['id'], $userPer) ? 'checked' : '';
                    $html .= <<<Eof
                <div class="md-checkbox col-md-4">
                  <input type="checkbox" id="permission_{$value['id']}" class="md-check" value="{$value['name']}" name="permission[]" {$checked}>
                  <label for="permission_{$value['id']}">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span> {$display_name}
                  </label>
                </div>
Eof;
                }
            }else{
                $display_name = htmlspecialchars($permission->pm_display_name);
                $checked = in_array($permission->id, $userPer) ? 'checked' : '';
                $html .= <<<Eof
                <div class="md-checkbox col-md-4">
                  <input type="checkbox" id="permission_{$permission->id}" class="md-check" value="{$permission->name}" name="permission[]" {$checked}>
                  <label for="permission_{$permission->id}">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span> {$display_name}
                  </label>
                </div>
Eof;
            }


            $html .= '</td></tr>';
        }
        return $html;
    }

    /**
     * 权限组重新排序，按权限分组
     * @param array $permissions
     * @return array
     * */
    public function groupPermissions($permissions)
    {
        $array = [];
        $array = array_sorts(toArray($permissions),'pm_type');
        return $array;
    }

    /**
     * 获取当前用户组权限ID数组
     * @param object $userPer
     * @return array
     * */
    public function getUserPermissions($userPer)
    {
        $array = [];
        if (!empty($userPer)) {
            foreach ($userPer as $permission) {
                $array[] = $permission->id;
            }
        }
        return $array;
    }

    /**
     * 权限组重新排序，按权限分组
     * @param array $permissions
     * @return array
     * */
    public function groupPermissionsByType($permissions)
    {
        $arr_sort = [];
        $pm = json_decode(json_encode($permissions),true);
        foreach ($pm as $k=>$permission) {
            $arr_sort[$permission['pm_type']][]=$permission;
        }
        return $arr_sort;
    }

    public function updUserPermission(Request $request)
    {
        $uid = $request->input('uid');
        $permission_arr = $request->input('permission');
        $user = User::find($uid);
        $user_direct_permissions = $user->getDirectPermissions();
        //移除用户所有权限
        $result = $user->revokePermissionTo($user_direct_permissions);
        //增加用户权限
        $res = $user->givePermissionTo($permission_arr);
        if ($res){
            $code = 200;
        }else{
            $code = 300;
        }
        return [
            'status' => $code,
            'message' => $res ? '更新成功':'更新失败',
        ];
    }

    public function getUserRoles($uid)
    {
        $user = User::find($uid);
        $user_roles = $user->roles;
        $all_roles = Myrole::all();
        $role_html = $this->getRolesHTML(toArray($all_roles),$user_roles);
        $assign = [
            'user_permission'=>$user_roles,
            'all_permission'=>$all_roles,
            'uid'=>$uid,
            'role_html'=>$role_html,
        ];
        return view('system.user_edit_role',$assign);
    }

    public function getRolesHTML($roles, $userRole = [])
    {
        $userRole = $this->getUserPermissions($userRole);
        $html = '';
        $html .= "<tr><td><span class='label label-sm label-success'>角色</span></td><td>";
        foreach ($roles as $key => $role) {
            if (is_array($role)){
                    $display_name = htmlspecialchars($role['role_display_name']);
                    $checked = in_array($role['id'], $userRole) ? 'checked' : '';
                    $html .= <<<Eof
                <div class="md-checkbox col-md-4">
                  <input type="checkbox" id="role_{$role['id']}" class="md-check" value="{$role['name']}" name="role[]" {$checked}>
                  <label for="role_{$role['id']}">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span> {$display_name}
                  </label>
                </div>
Eof;
            }
        }
        $html .= '</td></tr>';
        return $html;
    }

    public function updUserRole(Request $request)
    {
        $uid = $request->input('uid');
        $role_arr = $request->input('role');
        $user = User::find($uid);
//        $user_roles = $user->roles;
        $user_roles = $user->getRoleNames();
        //移除用户所有角色
        foreach ($user_roles as $v) {
            $user->removeRole($v);
        }
        //增加用户角色
        $res = $user->assignRole($role_arr);
        if ($res){
            $code = 200;
        }else{
            $code = 300;
        }
        $ret =  [
            'status' => $code,
            'message' => $res ? '更新成功':'更新失败',
        ];
        return response()->json($ret);
    }

}
