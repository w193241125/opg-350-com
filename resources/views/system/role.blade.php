@extends('layouts.app')

@section('title', '角色管理')
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
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header add_role_html">
                            <a href="javascript:;" class="btn btn-xs btn-primary create_role"><i class="glyphicon glyphicon-plus"></i> 新增角色</a>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="role_info" class="table table-bordered table-striped table-hover dataTables">
                                <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>角色标识名(绑定路由名)</th>
                                    <th>角色展示名称</th>
                                    <th>描述</th>
                                    <th>创建时间</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($all_role as $u)
                                    <tr>
                                        <td>{{$u->id}}</td>
                                        <td>{{$u->name}}</td>
                                        <td>{{$u->role_display_name}}</td>
                                        <td>{{$u->role_description}}</td>
                                        <td>{{$u->created_at}}</td>
                                        <td>{{$u->updated_at}}</td>
                                        <td>
                                            <a  href="/system/role/{{$u->id}}/edit" class="btn btn-xs btn-primary editrole"><i class="glyphicon glyphicon-edit"></i> 编辑</a>
                                            <a href="javascript:;" onclick="getRoleInfo({{ $u->id }})" class="btn blue mt-ladda-btn ladda-button btn-circle btn-outline"><i class="fa fa-eye"></i>查看</a>
                                        </td>

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
    <div class="modal fade draggable-modal" id="role_infos" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">角色名</h4>
                </div>
                <div class="modal-body">
                    <div class="table-scrollable">
                        <table class="table table-hover table-light">
                            <tbody>
                            <tr>
                                <td class="text-right col-sm-3"><strong> 角色名 </strong></td>
                                <td class="col-sm-8" id="role_display_name">  </td>
                            </tr>
                            <tr>
                                <td class="text-right col-sm-3"><strong> 角色标识 </strong></td>
                                <td class="col-sm-8" id="role_name">  </td>
                            </tr>
                            <tr>
                                <td class="text-right col-sm-3"><strong> 角色描述 </strong></td>
                                <td class="col-sm-8" id="role_description">  </td>
                            </tr>
                            <tr>
                                <td class="text-right col-sm-3"><strong> 添加时间 </strong></td>
                                <td class="col-sm-8" id="role_created_at">  </td>
                            </tr>
                            <tr>
                                <td class="text-right col-sm-3"><strong> 修改时间 </strong></td>
                                <td class="col-sm-8" id="role_updated_at">  </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-scrollable">
                        <table class="table table-hover table-light">
                            <thead>
                            <tr>
                                <th class="col-sm-2 text-center"> 模块 </th>
                                <th class="col-sm-9 text-center"> 权限 </th>
                            </tr>
                            </thead>
                            <tbody id="role_perms">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">关闭</button>
                    <a type="button" class="btn green" id="role_edit" href="">编辑</a>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection


{{--尾部前端资源--}}
@section('script')
    <script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/role/scripts/role.js') }}" type="text/javascript"></script>
    <!-- BEGIN THEME GLOBAL SCRIPTS 这个js控制 添加菜单 的 label 上移与下移 -->
    <script src="{{asset('assets/admin/layouts/scripts/app.min.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $('#role_info').DataTable()
        });

        $('#role_info').DataTable({
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
            "destroy": true,
//            scrollX: true,
            scrollCollapse: true,
            bPaginate: true,
            bLengthChange: true,
            "bAutoWidth": true,
            "aaSorting": [],
            responsive: true
        });
    </script>

    <script type="text/javascript">
        var delete_url = "{{ url("system/role") }}";
        var info_url = "{{ url("system/role") }}";
        /*alert()弹窗*/
        jQuery(document).ready(function() {
            SweetAlert.init();
        });

        function getRoleInfo(id) {
            var edit_url = info_url+'/'+id+'/edit';
            var html = '';
            var settings = {
                type: "GET",
                url: info_url+'/'+id,
                dataType:"json",
                success: function(data) {
                    var perms = data.perms;
                    var role = data.role;
                    $('.modal-title').text(role.role_display_name);
                    $('#role_display_name').text(role.role_display_name);
                    $('#role_name').text(role.name);
                    $('#role_description').text(role.role_description);
                    $('#role_created_at').text(role.created_at);
                    $('#role_updated_at').text(role.updated_at);
                    console.log(perms);
                    $.each(perms, function (index_1, perm_group) {
                        html += '<tr><td class="text-center"><strong> '+ index_1 +' </strong></td><td>';
                        $.each(perm_group, function (index_2, perm) {
                            html += '<div class="col-md-4">'+
                                perm.pm_display_name+
                                '</div>';
                        });
                        html += '</td></tr>';
                    });
                    $('#role_perms').text('');
                    $('#role_perms').append(html);
                    $('#role_edit').attr('href', edit_url);
                    $('#role_infos').modal();
                },
                error: function (HttpRequest) {
                    if (HttpRequest.responseJSON.error == "no_permissions") {
                        sweetAlert({
                            title:"您没有此权限",
                            text:"请联系管理员",
                            type:"error"
                        });
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            };
            $.ajax(settings);
        }

    </script>
@endsection