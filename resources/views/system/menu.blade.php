@extends('layouts.app')

@section('title', '菜单管理')
{{--顶部前端资源--}}
@section('styles')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('vendor/jquery-nestable/jquery.nestable.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/ladda/ladda-themeless.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- 引入添加菜单的样式 -->
    <link href="{{asset('assets/admin/layouts/css/components-md.min.css')}}" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{asset('assets/admin/layouts/css/plugins-md.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- 引入添加菜单的样式结束 -->
    <style type="text/css">
        .menu-option {
            float: right;
        }
        .menu-option i {
            margin: 0px 5px;
        }
    </style>
@endsection

{{--服务注入--}}
@inject('MenuPresenter', 'App\Presenters\MenuPresenter')

{{--页面内容--}}
@section('contents')
    <div class="content-wrapper" style="min-height: 960px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                菜单管理
                <small>Version 2.0</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Dashboard</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Main row -->
            <div class="row">
                <input type="hidden" id="nestable_list_1_output">
                <!-- Left col -->
                <div class="col-md-6">
                    <!-- MAP & BOX PANE -->
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">菜单列表</h3>
                            <div class="box-tools" id="saveOrder">
                                <button type="button" class="pull-right btn btn-default" >保存排序</button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="dd" id="nestable_list_1">
                            @if(!empty($all_menu))
                                {!! $MenuPresenter->menuOrderList($all_menu) !!}
                            @endif
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <div class="col-md-6 add_menu_html">
                    <div class="text-center middle-box" style="margin-top: 150px">
                        <h4 style="color: #555"> 在这里添加或者编辑菜单内容 </h4>
                        <button type="button" class="btn btn-success mt-ladda-btn ladda-button create_menu" data-style="expand-up">
                    <span class="ladda-label">
                        <i class="fa fa-plus"></i> 添加菜单
                    </span>
                            <span class="ladda-spinner"></span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection


{{--尾部前端资源--}}
@section('script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('vendor/jquery-nestable/jquery.nestable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/menu/scripts/ui-nestable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/ladda/spin.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/ladda/ladda.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/menu/scripts/ui-buttons.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/menu/scripts/menu.js') }}" type="text/javascript"></script>
    {{--sweetalert弹窗--}}
    {{--<script src="/sweetalert2/dist/sweetalert2.min.js"></script>--}}
    {{--<link rel="stylesheet" href="/sweetalert2/dist/sweetalert2.min.css">--}}
    {{--弹窗js--}}
    <script src="{{ asset('vendor/bootstrap-sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/layouts/scripts/sweetalert/sweetalert-ajax-delete.js') }}" type="text/javascript"></script>

    <!-- Include a polyfill for ES6 Promises (optional) for IE11 and Android browser -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>

    <!-- END PAGE LEVEL PLUGINS -->

    <!-- BEGIN THEME GLOBAL SCRIPTS 这个js控制添加菜单的label上移与下移 -->
    <script src="{{asset('assets/admin/layouts/scripts/app.min.js')}}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->

    <script type="text/javascript">
        jQuery(document).ready(function() {
            SweetAlert.init();
        });
        var cache_url = "{{ route('MenuController.saveMenuOrder') }}";
        //保存排序
        $('#saveOrder').on('click' ,function () {

            var menu = $('#nestable_list_1_output').val();
            var settings = {
                type: "POST",
                url: cache_url,
                data: {menu: menu},
                dataType:"json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    sweetAlert({
                        title:"保存成功",
                        type:"success"
                    });
                },
                error:function (xhr, errorText, errorType) {
                    if (xhr.responseJSON.error == 'no_permissions') {
                        sweetAlert({
                            title:'您没有此权限',
                            text:"请联系管理员",
                            type:"error"
                        });
                        return false;
                    } else {
                        sweetAlert({
                            title:'未知错误',
                            text:"请联系管理员",
                            type:"error"
                        });
                        return false;
                    }
                }
            };
            $.ajax(settings)
        });
    </script>
@endsection