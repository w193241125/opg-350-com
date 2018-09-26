<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dept extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'dept';

    /**
     * 获得此部门的用户
     */
    public function user()
    {
        return $this->hasMany('App\Models\User');
    }

    /**
     * 获得此部门的职位
     */
    public function position()
    {
        return $this->hasMany('App\Models\Position');
    }

}
