<?php

namespace App\Http\Controllers\System;

use App\Http\Requests\UserPost;
use App\Models\Dept;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * 展示用户列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_list = User::with('dept')->get();

        $assign = [
            //预加载获取用户职位和部门和拥有的角色
            'user_list'=>$user_list->load(['dept','position','roles'])
        ];
        return view('system.user',$assign);
    }

    /**
     * 展示创建用户表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //获取部门
        $dept = Dept::all();
        //获取职务
        $position = Position::all();

        $assign = [
            'dept'=>$dept,
            'position'=>$position,
        ];
        return view('system.user_add',$assign);
    }

    /**
     * 执行新建用户操作
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserPost $userPost, User $user)
    {
        $data = [];
        $data['username'] = $userPost->username;
        $data['trueName'] = $userPost->trueName;
        $data['password'] = $userPost->password?$userPost->password:bcrypt('123456');
        $data['position_id'] = $userPost->position_id;
        $data['dept_id'] = $userPost->dept_id;
        $data['sex'] = $userPost->sex;
        return $user->createUser($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * 展示编辑页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //获取部门
        $dept = Dept::all();
        //获取职务
        $position = Position::all();
        //获取当前用户
        $user_info = User::find($id);
        $assign = [
            'dept'=>$dept,
            'position'=>$position,
            'user_info'=>$user_info,
        ];
        return view('system.user_edit',$assign);
    }

    /**
     * 更新用户信息
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserPost $userPost, User $user)
    {

        $data = $userPost->except(['_token','_method']);
        if (empty($data['password'])){
            unset($data['password']);
        }
        unset($data['id']);

        $map = ['uid'=>$userPost->id];
        $responseData = $user->updateData($map,$data);
        return response()->json($responseData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * AJAX获取职务
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetPosition(Request $request) {
        $position = false;
        if ($request->dept_id != 0) {
            $position = Position::where('dept_id','=',$request->dept_id)->get();
        }
        return $position;
    }
    
    /**
     * AJAX 检查是否存在用户名
     * 
     */
    public function ajaxCheckUsername(Request $request)
    {
        if ($request->type == 'create'){
            return User::where(['username'=>$request->username])->get();
        }elseif($request->type == 'edit'){
            $res = User::where(['username'=>$request->username])->get()->toarray();
            if ($res && $res[0]['uid']!=$request->id){
                return 1;
            }else{
                return array(); //根据 js 的判断返回空数组
            }
        }

    }
}
