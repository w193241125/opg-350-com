@extends('layouts.app')
@inject('rolePermissions', 'App\Presenters\rolePermissionsPresenter')

{{--顶部前端资源--}}
@section('styles')
    <style>
        .imp{color: red}
        .font_style{color: #999;font-size: 18px;}
    </style>
    <!-- 引入添加菜单的样式 -->
    <link href="{{asset('assets/admin/layouts/css/components-md.min.css')}}" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{asset('assets/admin/layouts/css/plugins-md.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- 引入添加菜单的样式结束 -->
@endsection

{{--页面内容--}}
@section('contents')
    <div class="content-wrapper" style="min-height: 960px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                角色管理
                <small>Version 1.0</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 系统设置</a></li>
                <li class="active">角色管理</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light portlet-fit portlet-form bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-layers font-green"></i>
                                <span class="caption-subject font-green sbold uppercase">编辑用户组</span>
                            </div>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="remove" id="backto_role_list"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <!-- BEGIN FORM-->
                            <form action="{{ url("system/role/$role->id") }}" id="editForm" class="form-horizontal" method="post" >
                                {{ method_field('PUT') }}
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $role->id }}">
                                <div class="form-body">
                                    <div class="form-group form-md-line-input @if($errors->has('name')) has-error @endif">
                                        <label class="col-md-3 control-label" for="name">角色标识</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" placeholder="" name="name" id="name" value="{{ $role->name }}"  readonly="readonly">
                                            <div class="form-control-focus"> </div>
                                            <span class="help-block">{{ $errors->has('name') ? $errors->first('name') : '角色的唯一英文标识' }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group form-md-line-input @if($errors->has('role_display_name')) has-error @endif">
                                        <label class="col-md-3 control-label" for="role_display_name">角色名</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" placeholder="" name="role_display_name" id="role_display_name" value="{{ $role->role_display_name }}">
                                            <div class="form-control-focus"> </div>
                                            <span class="help-block">{{ $errors->has('role_display_name') ? $errors->first('role_display_name') : '用于角色的显示，标识的别名' }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group form-md-line-input @if($errors->has('role_description')) has-error @endif">
                                        <label class="col-md-3 control-label" for="role_description">角色描述</label>
                                        <div class="col-md-6">
                                            <textarea class="form-control" name="role_description" id="role_description" rows="3" style="margin-top: 0px; margin-bottom: 0px; height: 100px;">{{ $role->role_description }}</textarea>
                                            <div class="form-control-focus"> </div>
                                            <span class="help-block">{{ $errors->has('role_description') ? $errors->first('role_description') : '角色的功能介绍信息' }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group form-md-checkboxes">
                                        <div class="col-md-offset-1 col-md-10">
                                            @if(!empty($permissions))
                                                <div class="portlet light portlet-fit bordered">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="icon-settings font-red"></i>
                                                            <span class="caption-subject font-red sbold uppercase">用户组权限</span>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="table-scrollable table-scrollable-borderless">
                                                            <table class="table table-hover table-light">
                                                                <thead>
                                                                <tr class="uppercase">
                                                                    <th class="col-md-1 text-center"> 模块 </th>
                                                                    <th class="col-md-11 text-center"> 权限 </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                {!! $rolePermissions->getPermissions($permissions, $role->permissions) !!}
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-5 col-md-7">
                                            <input type="submit" class="btn green editButton" value="更新角色">
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection

{{--尾部前端资源--}}
@section('script')
    <script src="{{ asset('assets/admin/role/scripts/role.js') }}" type="text/javascript"></script>
    <script>
        $('#backto_role_list').on('click',function(){
            window.location.href='/system/role';
        });
    </script>
@endsection