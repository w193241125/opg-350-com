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
                抽奖配置
                <small>Version 1.0</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 活动设置</a></li>
                <li class="active">活动配置</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                {{--新增--}}
                <div class="col-xs-4">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="portlet light bordered formBox" id="lottery_three">
                            <div class="portlet-title">
                                <div class="caption font-green">
                                    <i class="icon-pin font-green"></i>
                                    <span class="caption-subject bold uppercase">添加新活动</span>
                                </div>
                                <div class="actions">
                                    <a class="btn btn-circle btn-icon-only btn-default close-link">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body form">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <strong>信息填写出错!</strong>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </div>
                                @endif
                                <form role="form" id="lottery_three_Form">
                                    {{ csrf_field() }}
                                    <div class="form-body">
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('activity_name')) { echo "has-error"; } ?> ">
                                            <input type="text" class="form-control" id="form_activity_name" name="activity_name" value="{{ old('activity_name') }}">
                                            <label for="form_activity_name"><span class="imp">*&nbsp;</span>活动名称</label>
                                            <span class="help-block form_activity_name">活动名称</span>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('activity_title')) { echo "has-error"; } ?> ">
                                            <input type="text" class="form-control" id="form_activity_title" name="activity_title" value="{{ old('activity_title') }}">
                                            <label for="form_activity_title"><span class="imp">*&nbsp;</span><span id="activitytitle">活动标题</span></label>
                                            <span class="help-block form_activity_title">活动标题</span>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('pre_num')) { echo "has-error"; } ?> ">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" name="activity_time" class="form-control pull-right" id="activity_date">
                                                <span class="help-block form_activity_time">活动时间</span>
                                            </div>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('activity_server')) { echo "has-error"; } ?>">
                                            <input type="text" class="form-control" id="form_activity_server" name="activity_server" value="{{ old('activity_server') }}">
                                            <label for="form_activity_server"><span class="imp">*&nbsp;</span>活动区服</label>
                                            <span class="help-block">活动区服</span>
                                        </div>

                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('activity_desc')) { echo "has-error"; } ?>">
                                            <input type="text" class="form-control" id="activity_descs" readonly>
                                            <textarea name="activity_desc" id="activity_desc" cols="30" rows="10"></textarea>
                                            <label for="activity_desc"><span class="imp">*&nbsp;</span><span id="total">活动说明</span></label>
                                            <span class="help-block">活动情况说明</span>
                                        </div>
                                        <div class="form-group">
                                            <span class="font_style">活动开关：</span><br>
                                            <input type="radio" id="activity_status" name="activity_status" class="flat-red activity_status" value="1" checked>
                                            <label for="activity_status" class="font_style">开</label>
                                            <input type="radio" id="activity_statuss" name="activity_status" class="flat-red activity_status" value="2">
                                            <label for="activity_statuss" class="font_style">关</label>
                                        </div>
                                    </div>
                                    <div class="form-actions noborder">
                                        <button type="submit" class="btn green lottery_three" >添加用户</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $('.lottery_three').on('click',function () {
                        console.log('lottery_three')
                        var _item = $(this);
                        var _form = $('#lottery_three_Form');
                        console.log(_form.serializeArray());
                        $.ajax({
                            url: '/operator/activity_add',
                            type:'post',
                            dataType: 'json',
                            data:_form.serializeArray(),
                            headers : {
                                'X-CSRF-TOKEN': $("input[name='_token']").val()
                            },
                            beforeSend : function(){
                                _item.attr('disabled','true');
                            },
                            success:function (response) {
                                sweetAlert(response.message);
                            }
                        }).fail(function(response) {
                            if(response.status == 422){
                                var data = $.parseJSON(response.responseText);
                                var layerStr = "";
                                for(var i in data.errors){
                                    layerStr += data.errors[i]+" ";
                                }
                                sweetAlert('错误', layerStr);
                            }
                        }).always(function () {
                            _item.removeAttr('disabled');
                        });
                    });
                </script>

                {{--修改--}}
                <div class="col-xs-4">
                    <div class="box">
                            <div class="portlet light bordered formBox" id="lottery">
                                <div class="portlet-title">
                                    <div class="caption font-green">
                                        <i class="icon-pin font-green"></i>
                                        <span class="caption-subject bold uppercase">活动配置更改</span>
                                        {{--<button type="submit" class="btn red flush" >一键清空</button>--}}
                                        {{--<button type="submit" class="btn green setpool" >生成奖池</button>--}}
                                    </div>
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
                                                                $.ajax({
                                                                    url: '/system/oneKeyFlush/',
                                                                    type: 'get',
                                                                    dataType: 'json',
                                                                    beforeSend: function () {
                                                                        _item.attr('disabled', 'true');
                                                                    },
                                                                    success: function (response) {
                                                                        sweetAlert(response.message);
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


                                        $('.setpool').on('click',function () {
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
                                                            $.ajax({
                                                                url: '/system/setPool/',
                                                                type: 'get',
                                                                dataType: 'json',
                                                                beforeSend: function () {
                                                                    _item.attr('disabled', 'true');
                                                                },
                                                                success: function (response) {
                                                                    sweetAlert(response.message);
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
                                    <div class="actions">
                                        <a class="btn btn-circle btn-icon-only btn-default close-link">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="portlet-body form">
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <strong>信息填写出错!</strong>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </div>
                                    @endif
                                    <form role="form" id="lottery_one_Form">
                                        {{ csrf_field() }}
                                        <div class="form-body">
                                            <div class="row ">
                                                <div class="col-md-6">
                                                    <div class="form-group form-md-line-input form-md-floating-label ">
                                                        <select class="form-control edited " id="form_parent_menu_1" name="activity">
                                                            <option value="0" >--选择活动-</option>
                                                            @foreach($activity as $v)
                                                                <option value="{{$v['id']}}" @if(old('activity')) selected="selected" @endif>{{$v['activity_name']}}</option>
                                                            @endforeach
                                                        </select>
                                                        <label for="form_parent_menu_1"><span class="imp">*&nbsp;</span>活动设置</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                $('#form_parent_menu_1').change(function () {
                                                    var id = $('#form_parent_menu_1').val();
                                                    var url = "{{ route('ajaxGetActivity') }}";
                                                    //重新获取下拉列表
                                                    $.getJSON(
                                                        url,
                                                        {activity_id:id},
                                                        function (data) {
                                                            if (data != []) {
                                                                $("#activitytitles").html("");
                                                                $("#activitytitles").html('活动标题：'+ data[0].activity_title);
                                                                $("#form_activity_titles").attr("value","");
                                                                $("#form_activity_titles").addClass('edited');
                                                                $("#form_activity_titles").val(data[0].activity_title);

                                                                $("#activitynames").html("");
                                                                $("#activitynames").html('活动名称：'+ data[0].activity_name);
                                                                $("#form_activity_names").attr("value","");
                                                                $("#form_activity_names").addClass('edited');
                                                                $("#form_activity_names").val(data[0].activity_name);

                                                                $('#reservations').val(data[0].activity_time);

                                                                $("#activityservers").html("");
                                                                $("#activityservers").html('活动区服：'+ data[0].activity_server);
                                                                $("#form_activity_servers").attr("value","");
                                                                $("#form_activity_servers").addClass('edited');
                                                                $("#form_activity_servers").attr("value",data[0].activity_server);

                                                                $("#activitydescss").html("");
                                                                $("#activitydescss").html(data[0].activity_desc);
                                                                $("#form_activity_descss").attr("value","");
                                                                $("#form_activity_descss").addClass('edited');
                                                                $("#form_activity_descss").attr("value",data[0].activity_desc);

                                                                if (data[0].activity_status == 1){

                                                                    $('#activity_status_2').attr("checked",false);
                                                                    $('#activity_status_1').attr("checked",true);
                                                                } else{
                                                                    $('#activity_status_1').attr("checked",false);
                                                                    $('#activity_status_2').attr("checked",true);
                                                                }
                                                            }
                                                        }
                                                    );
                                                });
                                            </script>
                                            <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('activity_name')) { echo "has-error"; } ?> ">
                                                <input type="text" class="form-control" id="form_activity_names" name="activity_name" value="{{ old('activity_name') }}">
                                                <label for="form_activity_name"><span class="imp">*&nbsp;</span><span id="activitynames">活动名称</span></label>
                                                <span class="help-block form_activity_name">活动名称</span>
                                            </div>
                                            <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('activity_title')) { echo "has-error"; } ?> ">
                                                <input type="text" class="form-control" id="form_activity_titles" name="activity_title" value="{{ old('activity_title') }}">
                                                <label for="form_activity_title"><span class="imp">*&nbsp;</span><span id="activitytitles">活动标题</span></label>
                                                <span class="help-block form_activity_title">活动标题</span>
                                            </div>

                                            <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('pre_num')) { echo "has-error"; } ?> ">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" name="activity_time" class="form-control pull-right" id="reservations">
                                                    <span class="help-block form_activity_time">活动时间</span>
                                                </div>
                                            </div>

                                            <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('activity_server')) { echo "has-error"; } ?> ">
                                                <input type="text" class="form-control" id="form_activity_servers" name="activity_server" value="{{ old('activity_server') }}">
                                                <label for="form_activity_server"><span class="imp">*&nbsp;</span><span id="activityservers">活动区服</span></label>
                                                <span class="help-block form_activity_server">活动区服</span>
                                            </div>
                                            <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('activity_desc')) { echo "has-error"; } ?>">
                                                <input type="text" class="form-control" id="activity_descs" readonly>
                                                <textarea name="activity_desc" id="activitydescss" cols="30" rows="10"></textarea>
                                                <label for="activity_desc"><span class="imp">*&nbsp;</span><span id="total">活动说明</span></label>
                                                <span class="help-block">活动情况说明</span>
                                            </div>
                                            <div class="form-group">
                                                <span class="font_style">活动开关：</span><br>
                                                <input type="radio" id="activity_status_1" name="activity_status" class="flat-red activity_status" value="1" checked>
                                                <label for="activity_status" class="font_style">开</label>
                                                <input type="radio" id="activity_status_2" name="activity_status" class="flat-red activity_status" value="2">
                                                <label for="activity_statuss" class="font_style">关</label>
                                            </div>
                                        </div>
                                        <div class="form-actions noborder">
                                            <button type="submit" class="btn green lottery_one" >修改配置</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <script>
                    $('.lottery_one').on('click',function () {
                        console.log('lottery_one')
                        var _item = $(this);
                        var _form = $('#lottery_one_Form');
                        console.log(_form.serializeArray());
                        $.ajax({
                            url: '/operator/activity_upd',
                            type:'post',
                            dataType: 'json',
                            data:_form.serializeArray(),
                            headers : {
                                'X-CSRF-TOKEN': $("input[name='_token']").val()
                            },
                            beforeSend : function(){
                                _item.attr('disabled','true');
                            },
                            success:function (response) {
                                sweetAlert(response.message);
                                location.reload()
                            }
                        }).fail(function(response) {
                            if(response.status == 422){
                                var data = $.parseJSON(response.responseText);
                                var layerStr = "";
                                for(var i in data.errors){
                                    layerStr += data.errors[i]+" ";
                                }
                                sweetAlert('错误', layerStr);
                            }
                        }).always(function () {
                            _item.removeAttr('disabled');
                        });
                    });
                </script>


                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection


{{--尾部前端资源--}}
@section('script')
    <!-- BEGIN THEME GLOBAL SCRIPTS 这个js控制 添加菜单 的 label 上移与下移 -->
    <script src="{{asset('assets/admin/layouts/scripts/app.min.js')}}" type="text/javascript"></script>
    <script src="/AdminLTE/bower_components/moment/min/moment.min.js"></script>
    <script src="/AdminLTE/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script>
        $(function () {
            //Date range picker
            $('#reservations').daterangepicker({
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
            $('#activity_date').daterangepicker({
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
    </script>
@endsection