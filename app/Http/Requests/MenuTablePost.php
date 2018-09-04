<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class MenuTablePost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(User $user)
    {
        if (request()->isMethod('POST')) {
            $result = $user->hasPermissionTo('menu.store');
        } else {
            $result = $user->hasPermissionTo('menu.update');
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
        $rules['name'] = 'required|max:50';
        $rules['icon'] = 'required|alpha_dash';
        if (request()->isMethod('POST')) {
            $rules['uri'] = 'required|unique:menus,uri';
        } else {
            $rules['uri'] = 'required|unique:menus,uri,'.$this->id;
            $rules['id'] = 'numeric|required';
        }
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
            'name.required' => '请填写用户名',
            'name.max' => '用户名最长不能超过50',
            'icon.required' => '请填写字体图标',
            'icon.alpha_dash' => '请填写正确的字体图标代码',
            'uri.required' => '请填写导航地址',
            'uri.unique' => '导航地址已存在',
        ];
    }
}
