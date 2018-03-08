<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UsersController extends Controller
{

    public function __construct(){

        //middleware 方法，该方法接收两个参数，第一个为中间件的名称，第二个为要进行过滤的动作。我们通过 except 方法来设定 指定动作 不使用 Auth 中间件进行过滤
        $this->middleware('auth', ['except' => ['show']]);
    }

    //个人资料展示
    public function show(User $user){
        return view('users.show',compact('user'));
    }

    //编辑资料页面跳转
    public function edit(User $user){
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    //编辑资料保存
    //UserRequest是表单请求验证（FormRequest） 是 Laravel 框架提供的用户表单数据验证方案，此方案相比手工调用 validator 来说，能处理更为复杂的验证逻辑，更加适用于大型程序
    public function update(UserRequest $request,User $user,ImageUploadHandler $uploader)
    {

        //authorize 方法接收两个参数，第一个为授权策略的名称，第二个为进行授权验证的数据
        $this->authorize('update', $user);
        $data = $request->all();

        //保存头像
        if ($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 362);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功!');

    }
}
