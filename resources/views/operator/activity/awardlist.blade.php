@extends('layouts.app')

@section('title', '奖励列表')
{{--顶部前端资源--}}
@section('styles')
    <style>
        .imp {
            color: red
        }

        .font_style {
            color: #999;
            font-size: 18px;
        }

        .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
            border: 1px solid #dddddd;
        }
    </style>
    <!-- 引入添加菜单的样式 -->
    <link href="{{asset('assets/admin/layouts/css/components-md.min.css')}}" rel="stylesheet" id="style_components"
          type="text/css"/>
    <link href="{{asset('assets/admin/layouts/css/plugins-md.min.css')}}" rel="stylesheet" type="text/css"/>
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
                <li><a href="#"><i class="fa fa-dashboard"></i> 活动</a></li>
                <li class="active">奖励列表</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body table-responsive">
                            <form action="{{route('activity.award_list')}}" method="post" class="search-form">
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
                                <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-2">
                                    <select name="activity_name" class="form-control" id="pay_channel">
                                        <option value="0">所有</option>
                                        <option value="1">支付宝</option>
                                        <option value="4">微信</option>
                                        <option value="3">苹果</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">提交</button>
                            </form>
                            <div style="clear: both"></div>
                            <table id="order_info" class="table table-bordered table-striped" width="100%">
                                <thead>
                                <tr>
                                    <th >id</th>
                                    <th >活动</th>
                                    <th >累充金额</th>
                                    <th >奖品</th>
                                    <th width="5%">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($data)
                                    @foreach($data as $key=>$p)
                                        <tr>
                                        <td height="25">{{$p->id}}</td>
                                        <td>{{$p->activity_name}}</td>
                                        <td>{{$p->money}}</td>
                                        <td>{{$p->award}}</td>
                                        <td class="center">
                                                <div><a href="javascript:;" award_id='{{$p->id}}' class="btn btn-warning btn-xs activity_del" >
                                                        <i class="fa fa-edit">删除</i>
                                                    </a></div>
                                        </td>
                                        </tr>

                                    @endforeach
                                @else
                                    <tr><td colspan="13">暂无</td></tr>
                                @endif
                                </tbody>
                            </table>
                            {{ $data->links() }}
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
        $(document).ready(function () {
            $('#order_info').DataTable();
        });

        $('#order_info').DataTable({
            language: {
                "sProcessing": "处理中...",
                "sLengthMenu": "显示 _MENU_ 项结果",
                "sZeroRecords": "没有匹配结果",
                "sInfo": "显示第 _START_ 至 _END_ 条结果，共 _TOTAL_ 条",
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
            buttons: [ //这个是她带给你的一些功能按键...
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
            destroy: true,
//            scrollX: true, //去掉这一项 box-body 的 div 中需要加上 table-response 类。
            scrollCollapse: true,
            bPaginate: false,
            info: false,//不显示每页多少项
            searching:false, //不显示搜索框
            bAutoWidth: true,
            aaSorting: [],
            responsive: true,
            paging: false // 禁止分页
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
            $('.search-form input[name=user_name]').val(filters.user_name);
        });
        //补发申请
        $('.activity_del').on('click',function () {
            var _item = $(this);
            swal.fire({
                title: "确定删除吗？",
                text: "请谨慎操作！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '确定删除！'
            }).then(function (isConfirm) {
                if(isConfirm.dismiss == 'cancel'){
                    swal(
                        '已取消！',
                        '你的数据没有被删除:)',
                        'error'
                    );
                    return;
                }
                var award_id = _item.attr('award_id');
//                        触发补发ajax
                $.ajax({
                    url:'/operator/award_del',
                    type:'post',
                    data:{award_id:award_id},
                    headers : {
                        'X-CSRF-TOKEN': $("input[name='_token']").val()
                    },
                    beforeSend: function(data){console.log(data);},
                    success:function (res) {
                        console.log(res);
                        if(res.status==200){
                            swal("删除成功！", res.message, "success");
                            location.reload()
                        }else{
                            swal("删除！", res.message, "error");
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
    </script>
@endsection