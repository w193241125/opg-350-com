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
        'name', 'username', 'password','trueName'
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


}
