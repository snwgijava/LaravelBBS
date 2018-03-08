<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *权限验证
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *表单验证规则
     * @return array
     */
    public function rules()
    {
        /**
        name.required —— 验证的字段必须存在于输入数据中，而不是空。
        name.between —— 验证的字段的大小必须在给定的 min 和 max 之间。
        name.regex —— 验证的字段必须与给定的正则表达式匹配。
        name.unique —— 验证的字段在给定的数据库表中必须是唯一的。
        email.required —— 同上
        email.email —— 验证的字段必须符合 e-mail 地址格式。
        introduction.max —— 验证中的字段必须小于或等于 value。
         */
        return [
            'name' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name,' . Auth::id(),
            'email' => 'required|email',
            'introduction' => 'max:80',
            'avatar' => 'mimes:jpeg,bmp,png,gif|dimensions:min_width=200,min_height=200',
        ];
    }

    //表单验证错误提示
    public function messages()
    {
        return [
            'avatar.mimes' =>'头像必须是 jpeg, bmp, png, gif 格式的图片',
            'avatar.dimensions' => '图片的清晰度不够，宽和高需要 200px 以上',
            'name.unique' => '用户名已被占用，请重新填写',
            'name.regex' => '用户名只支持中英文、数字、横杆和下划线。',
            'name.between' => '用户名必须介于 3 - 25 个字符之间。',
            'name.required' => '用户名不能为空。',
        ];
    }
}
