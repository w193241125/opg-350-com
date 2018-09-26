<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'position';

    /**
     * 获得此职位的用户
     */
    public function user()
    {
        return $this->hasMany('App\Models\User');
    }

    /**
     * 获取此职位所属部门
     */
    public function dept()
    {
        return $this->belongsTo('App\Models\Dept');
    }

}
