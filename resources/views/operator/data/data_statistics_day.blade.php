@extends('layouts.app')

@section('title', '数据按日统计')
{{--顶部前端资源--}}
@section('styles')
    <style>
        .imp{color: red}
        .font_style{color: #999;font-size: 18px;}
        .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td{
            border: 1px solid #dddddd;
        }
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
                运营管理
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
                        <div class="box-body">
                            <table id="order_info" class="table table-bordered table-striped" width="100%">
                                <thead>
                                <tr>
                                    <th rowspan="2" width="80" style="text-align: center; vertical-align:middle;">日期</th>
                                    <th colspan="8"style="text-align: center; vertical-align:middle;">总用户</th>
                                    <th colspan="8"style="text-align: center; vertical-align:middle;">新用户</th>
                                    <th colspan="7"style="text-align: center; vertical-align:middle;">老用户</th>
                                </tr>
                                <tr>
                                    <th>用户数</th>
                                    <th>次日活跃率</th>
                                    <th>收入</th>
                                    <th>环比增长</th>
                                    <th>分成</th>
                                    <th>付费人数</th>
                                    <th>ARPU</th>
                                    <th>付费率</th>

                                    <th>用户数</th>
                                    <th>次日留存率</th>
                                    <th>收入</th>
                                    <th>分成</th>
                                    <th>付费人数</th>
                                    <th>ARPU</th>
                                    <th>付费率</th>
                                    <th>注册ARPU</th>

                                    <th>用户数</th>
                                    <th>次日流失率</th>
                                    <th>收入</th>
                                    <th>分成</th>
                                    <th>付费人数</th>
                                    <th>ARPU</th>
                                    <th>付费率</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $key=>$v)
                                        <tr class="sum_day_data" @if(intval($key)%2 == 0)bgcolor="#EAFDFF"@endif>
                                        <th height="25" width="80">{{$v->tdate}}</th>
                                        <th>{{$v->reg}}</th>
                                        <th>{{sprintf("%.2f",$v->all_remain*100)}}%</th>
                                        <th>{{(int)$v->pay}}</th>
                                        <th>@if(!empty($v->huan_pay_rate)) {{sprintf("%.2f",$v->huan_pay_rate*100)}}@else - @endif</th>
                                        <th>{{(int)$v->amount}}</th>
                                        <th>{{$v->pay_u}}</th>
                                        <th>{{$v->arpu}}</th>
                                        <th>{{sprintf("%.2f",$v->pay_rate*100)}}%</th>

                                        <th>{{$v->reg_total}}</th>
                                        <th>{{sprintf("%.2f",$v->reg_remain*100)}}%</th>
                                        <th>{{(int)$v->pay_total}}</th>
                                        <th>{{(int)$v->pay_amount}}</th>
                                        <th>{{$v->pay_num}}</th>
                                        <th>{{$v->reg_arpu}}</th>
                                        <th>{{sprintf("%.2f",$v->reg_pay_rate*100)}}%</th>
                                        <th>{{$v->reg_arpus}}</th>

                                        <th>{{$v->old_login_total}}</th>
                                        <th>{{sprintf("%.2f",$v->old_off*100)}}%</th>
                                        <th>{{(int)$v->old_pay_total}}</th>
                                        <th>{{(int)$v->old_pay_amount}}</th>
                                        <th>{{$v->old_pay_num}}</th>
                                        <th>{{$v->old_arpu}}</th>
                                        <th>{{sprintf("%.2f",$v->old_pay_rate*100)}}%</th>
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
    <script>
        $(document).ready(function(){
            $('#order_info').DataTable();
        });

        $('#order_info').DataTable({
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
            scrollX: true,
            scrollCollapse: true,
            bPaginate: true,
            bLengthChange: true,
            "bAutoWidth": true,
            "aaSorting": [],
            responsive: true
        });
    </script>
@endsection