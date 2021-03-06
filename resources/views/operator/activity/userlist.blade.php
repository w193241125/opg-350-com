@extends('layouts.app')

@section('title', '活动用户列表')
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
                <li class="active">活动用户列表</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body table-responsive">
                            <form action="{{route('activity.user_list')}}" method="post" class="search-form">
                                {{csrf_field()}}

                                <div class="form-group  col-xs-12 col-sm-6 col-md-3 col-lg-2">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="date" class="form-control pull-right" id="reservation" value="{{old('date')}}">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-2">
                                    <label>
                                    <input type="checkbox" name="only" id="kf">：只看客服
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="consume" id="consume" class="flat-red">：查看消费列表
                                    </label>
                                </div>
                                <div class="form-group">
                                <button type="submit" class="btn btn-primary">提交</button>
{{--                                <a href="javascript:void(0);" class="btn red flush" >一键清空</a>--}}
                                </div>
                            </form>
                            <script>
                                $('.flush').on('click',function () {
                                    swal({
                                        title: '请输入密码！',
                                        input: 'text',
                                        showCancelButton: true,
                                        confirmButtonText: 'Submit',
                                        showLoaderOnConfirm: true,
                                        preConfirm: function(text) {
                                            return new Promise(function(resolve, reject) {
                                                if (text !== 'swl123') {
                                                    swal({
                                                        type: 'error',
                                                        title: '错误！',
                                                        html: '密码错误'
                                                    });
                                                } else {
                                                    var _item = $(this);
                                                    var consume = $('#consume').prop('checked');
                                                    console.log(consume)
                                                    $.ajax({
                                                        url: '/operator/oneKeyFlush/',
                                                        type: 'post',
                                                        data:{consume:consume},
                                                        headers : {
                                                            'X-CSRF-TOKEN': $("input[name='_token']").val()
                                                        },
                                                        beforeSend: function () {
                                                            _item.attr('disabled', 'true');
                                                        },
                                                        success: function (response) {
                                                            sweetAlert(response.message);
                                                            // location.reload();
                                                        }
                                                    }).fail(function (response) {
                                                        if (response.status == 422) {
                                                            var data = $.parseJSON(response.responseText);
                                                            var layerStr = "";
                                                            for (var i in data.errors) {
                                                                layerStr += data.errors[i] + " ";
                                                            }
                                                            sweetAlert('错误', layerStr);
                                                        }
                                                    }).always(function () {
                                                        _item.removeAttr('disabled');
                                                    });
                                                }
                                            });
                                        },
                                        allowOutsideClick: false
                                    });
                                });
                            </script>
                            <div style="clear: both"></div>
                            <table id="order_info" class="table table-bordered table-striped" width="100%">
                                <thead>
                                <tr>
                                    <th >id</th>
                                    <th >区服</th>
                                    <th >角色名</th>
                                    <th >充值金额</th>
                                    <th >开始时间</th>
                                    <th >结束时间</th>
                                    <th width="15%">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($data)
                                    @foreach($data as $key=>$p)
                                        <tr>
                                        <td height="25">{{$p->id}}</td>
                                        <td>{{$p->server_name}}</td>
                                        <td>{{$p->role_name}}</td>
                                        <td>{{$p->total}}</td>
                                        <td>{{$p->start_date}}</td>
                                        <td>{{$p->end_date}}</td>
                                        <td class="center">
                                                <div>
                                                    <a href="javascript:;" user_id='{{$p->id}}' class="btn btn-warning btn-xs activity_del" ><i class="fa fa-edit">删除</i></a>
                                                    @if ($p->status == 2)
                                                        <a href="javascript:;" user_id='{{$p->id}}' class="btn btn-info btn-xs activity_edit"  onclick="myModal({{$p->id}})"><i class="fa fa-edit">编辑</i></a>
                                                    @endif
                                                </div>
                                        </td>
                                        </tr>

                                    @endforeach
                                @else
                                    <tr><td colspan="13">暂无</td></tr>
                                @endif
                                </tbody>
                            </table>
                            {{$data->appends(['consume' => $filters['consume'],'date'=>$filters['date'],'only'=>$filters['only']])->render()}}
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
            if (filters.date != null) {
                $('.search-form input[name=date]').val(filters.date);
            }
            if (filters.only != undefined){
                $('#kf').prop('checked',true);
            }
            if (filters.consume != undefined){
                $('#consume').prop('checked',true);
            }
        });

        //删除
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
                var consume = $('#consume').prop('checked');
                var user_id = _item.attr('user_id');
//                        触发补发ajax
                $.ajax({
                    url:'/operator/user_del',
                    type:'post',
                    data:{user_id:user_id,consume:consume},
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
        });


        function myModal(id){
            var id = id
            //先保存地址，加载时event.target可能会变化
            var loadURI= '/operator/user_edit/'+ id;
            if(!$("#myModal").attr('id')){    //防止重复添加
                //动态添加modal框容器
                var obj=$('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>');
                obj.appendTo($('body'));
                var obj1=$('<div class="modal-dialog">');
                obj1.appendTo($(obj));
                var obj2=$('<div class="modal-content">');
                obj2.appendTo($(obj1));
            }
            //显示时加载可以避免“百度地图”等定位图标错误的问题
            $("#myModal").on('show.bs.modal', function () {
                //动态加载链接地址
                $('#myModal .modal-content').load(loadURI);
            }).modal().off('show.bs.modal');    //立即注消事件，不然事件会累加形成闪屏
            //隐藏时清除数据，避免缓存等问题引起的时好时坏的问题
            $('#myModal').off('hidden.bs.modal').on("hidden.bs.modal", function() {
                $('#myModal .modal-content').html('');
                $(this).removeData("bs.modal");
            });
            //中断事件执行，避免a等元素点击后跳转到相应页面
            event.preventDefault();
        }

        function upd_user(){
            var id = $('#upd_id').val()
            var consume = $('#consume').prop('checked');
            var total = $('#upd_total').val()
            var game_name = $('#form_game_name').val()
            var activity_time = $('#reservations').val()
            $.ajax({
                'type' : 'POST',
                'url' :  '/operator/user_upd',
                data: {id: id,total:total,consume:consume,game_name:game_name,activity_time:activity_time},
                headers : {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function (res) {
                    if(res.status==200){
                        $('#close_mod').trigger("click");
                        swal("更新成功！", res.message, "success");
                        location.reload()
                    }else{
                        $('#close_mod').trigger("click");
                        swal("更新！", res.message, "error");
                    }
                }
            });
        }
    </script>
@endsection
