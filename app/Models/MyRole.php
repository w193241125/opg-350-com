<?php
/**
 * Created by PhpStorm.
 * User: ADKi
 * Date: 2016/11/14 0014
 * Time: 9:28
 * @author ADKi
 */
namespace App\Models;

use Spatie\Permission\Models\Permission;
use \Spatie\Permission\Models\Role;
use App\Models\Role_Has_Permission;

class MyRole extends Role
{
    protected $table = 'roles';
    protected $primaryKey = 'id';

    /**
     * 创建角色
     * @param array $request
     * @return array
     * */
    public function createRole($request)
    {
        // 处理data为空的情况
        if (empty($request)) {
            return false;
        }
        $this->name = $request['name'];
        $this->role_display_name = $request['role_display_name'];
        $this->role_description = $request['role_description'];

        $result =  $this->save();//true or false
        if ($result) {
            return [
                'status' => 200,
                'message' => $result ? '添加成功':'添加失败',
            ];
        }else{
            return [
                'status' => 300,
                'message' => '添加失败',
            ];
        }
//        if (is_array($request->permission)) {
//            $permissions = [];
//            foreach ($request->permission as $id) {
//                $permissions[] = Permission::findOrFail($id);
//            }
//            $this->attachPermissions($permissions);
//        }
    }


    /**
     * 删除用户组
     * @param int $id
     * */
    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        // Force Delete
        $role->users()->sync([]); // 同步清除角色下的用户关联
        $role->perms()->sync([]); // 同步清除角色下的权限关联

        $role->forceDelete(); // 删除角色
    }

    /**
     * 查询角色列表并分页
     * @param int $page 每页显示数据数量
     * @param array $condition 查询条件
     * */
    public function getRoleList($page, array $condition = [])
    {
        return $this->where($condition)->paginate($page);
    }

    /**
     * 更新用户组
     * @param int $id
     * @param object $request
     * */
    public function updateRole($id, $request)
    {
        $role = self::findById($id);
        $role->name = $request['name'];
        $role->role_display_name = $request['role_display_name'];
        $role->role_description = $request['role_description'];
        $result = $role->save();

        //清除以前的权限 todo
        $this->clearPermissions($id,$request['permission']);
        $res = $this->massAssignmentPermissions($id, $request['permission']);

        if ($result){
            $back = ['message'=>'更新成功','code'=>$res];
        }else{
            $back = ['message'=>'更新失败','code'=>$res];
        }

        return $back;
    }

    /**
     * 清除角色权限
     * @param $id
     * @return bool
     */
    public function clearPermissions($id)
    {
//        if ($id ==1 ) return false;
        Role_Has_Permission::where(['role_id'=>$id])->delete();
    }
    /**
     * 批量更新角色权限
     * @param $id
     * @param $permission
     * @return bool
     */
    public function massAssignmentPermissions($id, $permission)
    {
//        if ($id ==1 ) return false;
        foreach ($permission as $item) {
            $temp['role_id'] = $id;
            $temp['permission_id'] = $item;
            $perm[] = $temp;
        }
//        $result = Role_Has_Permission::where(['role_id'=>$id])->delete();
        $res = Role_Has_Permission::insert($perm);
    }
}