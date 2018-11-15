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

    <!-- daterange picker -->
    <link rel="stylesheet" href="/AdminLTE/bower_components/bootstrap-daterangepicker/daterangepicker.css">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="/AdminLTE/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">


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
                        <div class="box-body table-responsive">
                            <form action="{{route('data.incomeBABGPost')}}" method="post" class="search-form">
                                {{csrf_field()}}
                                <div class="form-group  col-xs-12 col-sm-6 col-md-3 col-lg-2">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="date"  class="form-control pull-right" id="reservation">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-2">
                                    <select name="moneytype" class="form-control" id="moneytype">
                                        <option value="1">充值面额</option>
                                        <option value="2">充值净额</option>
                                    </select>
                                </div>
                                <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-2">
                                    <select name="plant_id" class="form-control" id="plant_id">
                                        <option value="1">350</option>
                                        <option value="0">全部</option>
                                    </select>
                                </div>
                                <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-2">
                                    <div class="select-down" id="selectForGame">
                                        <div class="trangle" ></div>
                                        <span class="title top-title" title="">
                                            <input type="text" name="" placeholder="请选择游戏" class="search-area">
                                        </span>
                                        <ul class="first-con">
                                            @foreach($game_sort_list as $s)
                                                <li>
            <span class="title first-span" >
                <i class="plus">+</i>
                <label>
                    <input type="checkbox" value="" name="" class='first-checked'>
                    <span>{{$s['game_sort_name']}}</span>
                </label>
            </span>
                                                    <ul class="second-con">
                                                        @if( $s['game_type'] == 2)
                                                            <li>
                    <span class="title">
                        <i class="plus">+</i>
                        <label>
                            <input type="checkbox" value="" name="" >
                            <span>H5</span>
                        </label>
                    </span>
                                                                <ul class="third-con">
                                                                    @foreach($game_list as $key=>$v)
                                                                        @if($v['os']  == 4 && $v['sort_id'] == $s['sort_id'] )
                                                                            <li>
                            <span class="title">
                                <i class="plus">+</i>
                                <label>
                                <input type="checkbox" value="{{$v['id'] }}" name="game_id[]" class="last-title" @if(in_array($v['id'],$filters['game_id'])) checked="checked" @endif/>
                                <span>{{$v['letter'] }}:{{$v['name'] }}_{{$v['id'] }}</span>
                                </label>
                            </span>
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </li>
                                                        @endif
                                                        <li>
                    <span class="title">
                        <i class="plus">+</i>
                        <label>
                            <input type="checkbox" value="" name="" >
                            <span>IOS</span>
                        </label>
                    </span>
                                                            <ul class="third-con">
                                                                @foreach($game_list as $key=>$v)
                                                                    @if($v['os']  == 2 && $v['sort_id']  == $s['sort_id'] )
                                                                        <li>
                            <span class="title">
                                <i class="plus">+</i>
                                <label>
                                    <input type="checkbox" value="{{$v['id'] }}" name="game_id[]" class="last-title"  @if(in_array($v['id'],$filters['game_id'])) checked="checked" @endif>
                                    <span>{{$v['letter'] }}:{{$v['name'] }}_{{$v['id'] }}</span>
                                </label>
                            </span>
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                        <li>
                    <span class="title">
                        <i class="plus">+</i>
                        <label>
                            <input type="checkbox" value="" name="" >
                            <span>安卓</span>
                        </label>
                    </span>
                                                            <ul class="third-con">
                                                                @foreach($game_list as $key=>$v)
                                                                    @if($v['os'] == 3 && $v['sort_id'] == $s['sort_id'])
                                                                        <li>
                            <span class="title">
                                <i class="plus">+</i>
                                <label>
                                    <input type="checkbox" value="{{$v['id'] }}" name="game_id[]" class="last-title"  @if(in_array($v['id'],$filters['game_id'])) checked="checked" @endif />
                                    <span>{{$v['letter'] }}:{{$v['name'] }}_{{$v['id'] }}</span>
                                </label>
                            </span>
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <ul class="search-result"></ul>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">提交</button>
                            </form>
                            <br>
                            <table id="order_info" class="table table-bordered table-striped" width="100%">
                                <thead>
                                <tr>
                                    <th width="100">游戏</th>
                                    <th width="60"><b style="color:#F00">统计</b></th>
                                    @foreach($payChannel as $k=>$v)
                                        <th width="60">{{$v['remark']}}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @if($result)
                                    @foreach($result as $key=>$p)
                                        <tr>
                                        <td height="25">{{$game_list[$key]['name']}}</td>
                                        @foreach($p as $pp)
                                        <td>{{$pp}}</td>
                                        @endforeach
                                        </tr>
                                    @endforeach

                                    <tr >
                                        <td><b style="color:#F00">合计</b></td>
                                        @foreach($channelTotal as $ct)
                                        <td><b style="color:#F00">{{$ct}}</b></td>
                                        @endforeach
                                    </tr>
                                @else
                                    <tr><td colspan="9">暂无</td></tr>
                                @endif
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
    <!-- date-range-picker -->
    <script src="/AdminLTE/bower_components/moment/min/moment.min.js"></script>
    <script src="/AdminLTE/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/select/select.js" type="text/javascript"></script>
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
//            scrollX: true, //去掉这一项 box-body 的 div 中需要加上 table-response 类。
            scrollCollapse: true,
            bPaginate: true,
            bLengthChange: true,
            "bAutoWidth": true,
            "aaSorting": [],
            responsive: true
        });

        $(function () {
            //Date range picker
            $('#reservation').daterangepicker({
                "locale": {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                    applyLabel: "应用",
                    cancelLabel: "取消",
                    resetLabel: "重置",
                    daysOfWeek: ["日", "一", "二", "三", "四", "五", "六"],
                    monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                },
                "startDate": moment().subtract(7, 'days'),
                "endDate": moment().subtract(1, 'days')
            });
        });
        var filters = {!! json_encode($filters) !!};
        $(document).ready(function () {
            SelectForGame.init($('.select-down'));
            if(filters.date != null ){
                $('.search-form input[name=date]').val(filters.date);
            }
            if (filters.plant_id != null ) {
                $('#plant_id').val(filters.plant_id);
            }
            if(filters.moneytype != null ){
                $('#moneytype').val(filters.moneytype);
            }
        });
    </script>
@endsection