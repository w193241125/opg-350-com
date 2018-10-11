<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'password','trueName','sex','position_id','dept_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 设置主键为 uid
     */
    protected $primaryKey = 'uid';

    /**
     * 关联部门
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dept() {
        return $this->belongsTo('App\Models\Dept','dept_id');
    }

    /**
     * 关联职务
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function position() {
        return $this->belongsTo('App\Models\Position','position_id');
    }

    /** 创建新用户
     * @param $data
     * @return bool
     */
    public function createUser($data)
    {
        // 处理data为空的情况
        if (empty($data)) {
            return false;
        }
        //添加数据
        $result = $this->create($data);
        if ($result) {
            return [
                'status' => $result->id,
                'message' => $result ? '添加成功':'添加失败',
            ];
        }else{
            return [
                'status' => 300,
                'message' => '添加失败',
            ];
        }
    }

    /**
     * 修改数据
     *
     * @param  array $map  where条件
     * @param  array $data 需要修改的数据
     * @return bool        是否成功
     */
    public function updateData($map, $data)
    {
        $model = $this
            ->where($map)
            ->get();
        // 可能有查不到数据的情况
        if ($model->isEmpty()) {
            return false;
        }
        foreach ($model as $k => $v) {
            $result = $v->forceFill($data)->save();
        }
        if ($result) {
            return [
                'status' => $result,
                'message' => $result ? '更新成功':'更新失败',
            ];
        }else{
            return false;
        }
    }
}
