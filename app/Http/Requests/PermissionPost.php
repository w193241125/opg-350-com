<?php

namespace App\Http\Requests;

use App\Models\MyPermission;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PermissionPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::user();
        if (request()->isMethod('POST')) {
            $result = $user->hasPermissionTo('permission.store');
        } else {
            $result = $user->hasPermissionTo('permission.update');
        }
        return $result;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (request()->isMethod('POST')){
            $rules['name'] = 'required|unique:permissions,name';
        } else {
            $rules['name'] = 'required|unique:permissions,name,'.$this->id;
            $rules['id'] = 'numeric|required';
        }
        $rules['pm_display_name'] = 'required';
        $rules['pm_description'] = 'required';
        $rules['pm_type'] = 'required';
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
            'name.required' => '请填写权限标识',
            'name.unique' => '权限标识已存在',
            'pm_display_name.required' => '必须填写权限名',
            'pm_description.required' => '请填写权限描述',
            'pm_type.required' => '请填写权限类型名称',
        ];
    }
}
