@extends('layouts.app')

@section('title', '失败订单扫描')
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
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <form action="{{route('pay.queryFailedOrderPost')}}" method="post" class="search-form">
                                {{csrf_field()}}
                                <div class="form-group  col-xs-12 col-sm-6 col-md-3 col-lg-2">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="date" class="form-control pull-right" id="reservation">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-2">
                                    <input type="text" name="user_name" class="form-control" placeholder="账号">
                                </div>

                                <button type="submit" class="btn btn-primary">提交</button>
                            </form>
                            <table id="order_info" class="table table-bordered table-striped table-hover" width="100%">
                                <thead>
                                <tr>
                                    <th>充值帐号</th>
                                    <th>充值方式</th>
                                    <th>订单号</th>
                                    <th>充值游戏</th>
                                    <th>充值服</th>
                                    <th>充值金额</th>
                                    <th>游戏币</th>
                                    <th>充值时间</th>
                                    <th>充值IP</th>
                                    <th>支付</th>
                                    <th>游戏币发放状态</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($failed_list))
                                    @foreach($failed_list as $u)
                                        <tr>
                                            <td>{{$u['user_name']}}</td>
                                            <td>{{$u['pay_channel']}}</td>
                                            <td>{{$u['orderid']}}</td>
                                            <td>{{$games_arr[$u['game_id']]['name']}}</td>
                                            <td>{{$u['server_id']}}服</td>
                                            <td>{{$u['money']}}</td>
                                            <td>{{$u['pay_gold']}}</td>
                                            <td>{{$u['pay_date']}}</td>
                                            <td>{{$u['user_ip']}}</td>
                                            <td>{!! $u['succ']==1?'<span class="label label-success">成功</span>':'<span class="label label-danger">失败</span>' !!}</td>
                                            <td>{!! '<span class="label label-danger">'.$u['pay_result'].'|'.$u['return_msg'].'('.$u['back_result'].')</span>' !!}</td>
                                            <td>
                                                <a href="javascript:;"
                                                   act='bf'
                                                   user_name='{{$u['user_name']}}'
                                                   plat_id='1'
                                                   game_id='{{$u['game_id']}}'
                                                   server_id='{{$u['server_id']}}'
                                                   money='{{$u['money']}}'
                                                   pay_gold='{{$u['pay_gold']}}'
                                                   orderid='{{$u['orderid']}}'
                                                   succ='{{$u['succ']}}'
                                                   game_byname='{{$u['game_byname']}}'
                                                   sign="{{$u['sign']}}"
                                                   class="btn btn-xs btn-primary bf"><i class="glyphicon glyphicon-edit"></i>补发</a>
                                            </td>
                                        </tr>
                                    @endforeach
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
    <script>
        $(document).ready(function(){
//            SweetAlert.init();

            $('#order_info').DataTable();
            
            $('.bf').on('click',function () {
                var _item = $(this);
                swal({
                        title: "确定补发吗？",
                        text: "请谨慎操作！",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确定补发！",
                        closeOnConfirm: false
                    },
                    function(){
                       var act = _item.attr('act');
                       var user_name = _item.attr('user_name');
                       var plat_id = _item.attr('plat_id');
                       var game_id = _item.attr('game_id');
                       var server_id = _item.attr('server_id');
                       var money = _item.attr('money');
                       var pay_gold = _item.attr('pay_gold');
                       var orderid = _item.attr('orderid');
                       var succ = _item.attr('succ');
                       var game_byname = _item.attr('game_byname');
                       var sign = _item.attr('sign');
//                        触发补发ajax
                        $.ajax({
                            url:'/operator/bf',
                            type:'post',
                            data:{act:act,user_name:user_name,plat_id:plat_id,game_id:game_id,server_id:server_id,money:money,pay_gold:pay_gold,orderid:orderid,succ:succ,game_byname:game_byname,sign:sign},
                            headers : {
                                'X-CSRF-TOKEN': $("input[name='_token']").val()
                            },
                            success:function (res) {
                                console.log(res);
                                if(res.status==200){
                                    swal("补发成功！", res.message, "success");
                                }else{
                                    swal("补发！", res.message, "error");
                                }
                            },
                            error: function (xhr,errorText,errorType) {
                                var result =$.parseJSON(xhr.responseText);
                                console.log(result);
                                if (result.error == "no_permissions") {
                                    sweetAlert({
                                        title:"您没有此权限",
                                        text:"请联系管理员",
                                        type:"error"
                                    });
                                    return false;
                                } else {
                                    sweetAlert({
                                        title:"未知错误",
                                        text:"请联系管理员",
                                        type:"error"
                                    });
                                    return false;
                                }
                            }
                        });

                    });
            })
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
//            scrollX: true,
            scrollCollapse: true,
            bPaginate: true,
            info: false,//不显示每页多少项
            searching:false, //不显示搜索框
            paging: false,// 禁止分页
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
                "startDate": moment(),
                "endDate": moment()
            });
        });
        var filters = {!! json_encode($filters) !!};
        $(document).ready(function () {
            SelectForGame.init($('.select-down'));
            if (filters.date != null) {
                $('.search-form input[name=date]').val(filters.date);
            }
            $('.search-form input[name=user_name]').val(filters.user_name);
        });
    </script>
@endsection