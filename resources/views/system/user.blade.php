@extends('layouts.app')

@section('title', '用户管理')
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
                用户管理
                <small>Version 1.0</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 系统设置</a></li>
                <li class="active">用户管理</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header add_user_html">
                            <a href="javascript:;" class="btn btn-xs btn-primary create_user"><i class="glyphicon glyphicon-plus"></i> 新增用户</a>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="user_info" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>用户名</th>
                                    <th>真实姓名</th>
                                    <th>角色</th>
                                    <th>性别</th>
                                    <th>部门</th>
                                    <th>职位</th>
                                    <th>登录次数</th>
                                    <th>上次登录时间</th>
                                    <th>上次登录IP</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($user_list as $u)
                                <tr>
                                    <td>{{$u->uid}}</td>
                                    <td>{{$u->username}}</td>
                                    <td>{{$u->trueName}}</td>
                                    <td>
                                        @foreach($u->roles as $r)
                                            <span class="label label-success">{{$r->role_display_name}}</span>
                                        @endforeach
                                    </td>
                                    <td>{{$u->sex}}</td>
                                    <td>{{$u->dept['dept_name']}}</td>
                                    <td>{{$u->position['position_name']}}</td>
                                    <td>{{$u->loginTimes}}</td>
                                    <td>{{$u->lastLoginTime}}</td>
                                    <td>{{$u->lastLoginIP}}</td>
                                    <td>{{$u->state?'启用':'禁用'}}</td>
                                    <td><a href="javascript:;" data-href="/system/user/{{$u->uid}}/edit" class="btn btn-xs btn-primary edituser"><i class="glyphicon glyphicon-edit"></i> 编辑</a></td>
                                </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection


{{--尾部前端资源--}}
@section('script')
    <script src="{{ asset('assets/admin/user/scripts/user.js') }}" type="text/javascript"></script>
    <!-- BEGIN THEME GLOBAL SCRIPTS 这个js控制 添加菜单 的 label 上移与下移 -->
    <script src="{{asset('assets/admin/layouts/scripts/app.min.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $('#user_info').DataTable()
        });

        $('#user_info').DataTable({
            language: {
                "sProcessing": "处理中...",
                "sLengthMenu": "显示 _MENU_ 项结果",
                "sZeroRecords": "没有匹配结果",
                "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
                "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
                "sInfoPostFix": "",
                "sSearch": "搜索:",
                "sUrl": "",
                "sEmptyTable": "表中数据为空",
                "sLoadingRecords": "载入中...",
                "sInfoThousands": ",",
                "oPaginate": {
                    "sFirst": "首页",
                    "sPrevious": "上页",
                    "sNext": "下页",
                    "sLast": "末页"
                },
                "oAria": {
                    "sSortAscending": ": 以升序排列此列",
                    "sSortDescending": ": 以降序排列此列"
                }
            },
            "buttons": [ //这个是她带给你的一些功能按键...
                {
                    "extend": "copy",
                    "className": "btn-sm"
                },
                {
                    "extend": "csv",
                    "className": "btn-sm"
                },
                {
                    "extend": "excel",
                    "className": "btn-sm"
                },
                {
                    "extend": "pdfHtml5",
                    "className": "btn-sm"
                },
                {
                    "extend": "print",
                    "className": "btn-sm"
                },
                {
                    "extend": "pdf",
                    "className": "btn-sm"
                }
            ],
        });
    </script>
@endsection