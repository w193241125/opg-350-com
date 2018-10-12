<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class MyPermission extends Permission
{
    protected $table = 'permissions';

    /**
     * 重写 permission 的 create 方法，添加了 display_name pm_description pm_type 三个字段
     * @param array $attributes
     * @return $this|Model
     */
    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        $permission = static::getPermissions()->filter(function ($permission) use ($attributes) {
            return $permission->name === $attributes['name'] && $permission->guard_name === $attributes['guard_name'];
        })->first();

        if ($permission) {
            throw PermissionAlreadyExists::create($attributes['name'], $attributes['guard_name'], $attributes['display_name'], $attributes['pm_description'], $attributes['pm_type']);
        }

        if (isNotLumen() && app()::VERSION < '5.4') {
            return parent::create($attributes);
        }

        return static::query()->create($attributes);
    }

    /**
     * 清除权限缓存
     * @return mixed
     */
    public static function clearCache()
    {
        $res = app()['cache']->forget('spatie.permission.cache');

        return $res;
    }

    /**
     * 通过权限名称删除权限
     * @param array $name 权限名称数组
     * @return integer 但会影响行数，0 或 正整数。
     */
    public static function delPmByName($name)
    {
        $id_arr = [];
        //删除角色表的权限
        $ids = self::wherein('name', $name)->get(['id'])->toarray();
        foreach ($ids as $id) {
            $id_arr[] = $id['id'];
        }
        if(!empty($id_arr)){
            //删除角色权限
            $role_res = DB::table('role_has_permissions')->whereIn('permission_id',$id_arr)->delete();
            //删除用户权限
            $model_res = DB::table('model_has_permissions')->whereIn('permission_id',$id_arr)->delete();
            //删除权限
            $res  = self::wherein('name', $name)->delete();
            //删除权限缓存
            self::clearCache();
            return $res;
        }
        return false;
    }

    /**
     * 通过权限名称修改权限名称
     * @param string $name 老权限名
     * @param string $new_name 新权限名字
     * @return integer 但会影响行数，0 或 正整数。
     */
    public static function updPmByName($name, $new_name)
    {
        $re = self::findByName($name);
        $res  = self::where('id','=', $re->id)->update(['name'=>$new_name]);
        return $res;
    }

    /**
     * 获取用户所有权限
     */
    public static function getAllPmStr(User $user)
    {
        $permissions = $user->getAllPermissions()->toarray();
        foreach ($permissions as $p) {
            $pm_arr[] = $p['name'];
        }
        return $pm_arr;
    }
}
