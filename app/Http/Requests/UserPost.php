<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(User $user)
    {
        if (request()->isMethod('POST')) {
            $result = $user->hasPermissionTo('user.store');
        } else {
            $result = $user->hasPermissionTo('user.update');
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules['username'] = 'required|min:4';
        $rules['dept_id'] = 'required|integer|min:1';
        $rules['position_id'] = 'required|integer|min:1';
        $rules['sex'] = 'required|integer';
        $rules['trueName'] = 'required';
        return $rules;
    }

    /**
     * 获取已定义验证规则的错误消息。
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username.required' => '请填写用户名',
            'username.min' => '用户名最少为4位',
            'dept_id.required' => '请选择部门',
            'dept_id.integer' => '非法部门参数',
            'dept_id.min' => '请选择部门',
            'position_id.required' => '请选择职位',
            'position_id.integer' => '非法职位参数',
            'position_id.min' => '请选择职位',
            'sex.required' => '请选择性别',
            'sex.integer' => '非法性别参数',
            'trueName.required' => '请填写真实姓名',
        ];
    }
}
