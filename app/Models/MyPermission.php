<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission;


class MyPermission extends Permission
{


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
    public function clearCache()
    {
        $res = app()['cache']->forget('spatie.permission.cache');

        return $res;
    }
}
