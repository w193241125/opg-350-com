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
                                               class="btn btn-xs btn-primary bf"><i class="glyphicon glyphicon-edit"></i> 补发</a>
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
@endsection


{{--尾部前端资源--}}
@section('script')
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
                    var post_data = [];
                        post_data.act = _item.attr('act');
                        post_data.user_name = _item.attr('user_name');
                        post_data.plat_id = _item.attr('plat_id');
                        post_data.game_id = _item.attr('game_id');
                        post_data.server_id = _item.attr('server_id');
                        post_data.money = _item.attr('money');
                        post_data.pay_gold = _item.attr('pay_gold');
                        post_data.orderid = _item.attr('orderid');
                        post_data.succ = _item.attr('succ');
                        post_data.game_byname = _item.attr('game_byname');
                        post_data.sign = _item.attr('sign');
//                        触发补发ajax
                        $.ajax({
                            url:'/operator/bf',
                            type:'get',
                            data:post_data,
                            success:function (res) {
                                if(res.status==200){
                                    swal("补发！", "补发成功。", "success");
                                }else{
                                    swal("补发！", "补发失败。", "error");
                                }
                            },
                            error: function (xhr,errorText,errorType) {
                                var result =$.parseJSON(xhr.responseText);
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
            bLengthChange: true,
            "bAutoWidth": true,
            "aaSorting": [],
            responsive: true
        });
    </script>
@endsection