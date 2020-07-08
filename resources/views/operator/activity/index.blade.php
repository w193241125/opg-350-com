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

                                        <div class="form-group form-md-floating-label">
                                            <label>活动名称</label>
                                            <select class="form-control" name="activity_name">
                                                <option value="pay_back" @if(old('activity_name') == 'pay_back') selected @endif >充值返利</option>
                                                <option value="cost_back" @if(old('activity_name') == 'cost_back') selected @endif>消费返利</option>
                                                <option value="recharge" @if(old('activity_name') == 'recharge') selected @endif>充值排行榜</option>
                                                <option value="consume" @if(old('activity_name') == 'consume') selected @endif>消费排行榜</option>
                                                <option value="login_gift" @if(old('activity_name') == 'login_gift') selected @endif>每日登录礼包</option>
                                                <option value="pay_gift" @if(old('activity_name') == 'pay_gift') selected @endif>每日充值礼包</option>
                                                <option value="guestbook" @if(old('activity_name') == 'guestbook') selected @endif>留言/祝福墙</option>
                                                <option value="pay_back_box" @if(old('activity_name') == 'pay_back_box') selected @endif>充值返宝箱(最大)</option>
                                                {{--            <option value="lottery">抽奖</option>--}}
                                            </select>
                                            <span class="help-block form_activity_name">活动名称,直接选取。</span>
                                        </div>

                                        <div class="form-group form-md-floating-label">
                                            <label>游戏名</label>
                                            <select class="form-control" name="game_name">
                                                <option value="xlczg_zf" @if(old('activity_name') == 'xlczg_zf') selected @endif >老后台龙城专服</option>
                                                <option value="xlczg_xzf" @if(old('activity_name') == 'xlczg_xzf') selected @endif>新龙城专服</option>
                                                <option value="xlczg_hf" @if(old('activity_name') == 'xlczg_hf') selected @endif>龙城混服</option>
                                            </select>
                                            <span class="help-block form_activity_name">活动名称,直接选取。</span>
                                        </div>

                                        <div class="form-group form-md-line-input form-md-floating-label
                @if ($errors->has('sid')) has-error @endif ">
                                            <input type="text" class="form-control" id="form_sid" name="sid" value="{{ old('sid') }}">
                                            <label for="form_sid"><span class="imp">*</span>新后台映射区服id</label>
                                            <span class="help-block">数字数字，如：120</span>
                                        </div>

                                        <div class="form-group form-md-line-input form-md-floating-label
                @if ($errors->has('sid')) has-error @endif ">
                                            <input type="text" class="form-control" id="form_server_id" name="server_id" value="{{ old('server_id') }}">
                                            <label for="form_server_id"><span class="imp">*</span>研发区服id</label>
                                            <span class="help-block">数字数字，如：46501</span>
                                        </div>

                                        <div class="form-group form-md-line-input form-md-floating-label
                @if ($errors->has('activity_title')) has-error @endif ">
                                            <input type="text" class="form-control" id="form_activity_title" name="activity_title" value="{{ old('activity_title') }}">
                                            <label for="form_activity_title"><span class="imp">*&nbsp;</span><span id="activitytitle">活动标题</span></label>
                                            <span class="help-block form_activity_title">活动标题</span>
                                        </div>

                                        <div class="form-group form-md-line-input form-md-floating-label
                @if ($errors->has('has_num')) has-error @endif ">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" name="activity_time" class="form-control pull-right" id="activity_date">
                                                <span class="help-block form_activity_time">活动时间</span>
                                            </div>
                                        </div>

                                        <div class="form-group form-md-line-input form-md-floating-label
                @if ($errors->has('activity_server')) has-error @endif ">
                                            <input type="text" class="form-control" id="form_activity_server" name="activity_server" value="{{ old('activity_server') }}">
                                            <label for="form_activity_server"><span class="imp">*&nbsp;</span>活动说明区服</label>
                                            <span class="help-block">活动区服，如：1区~至尊区</span>
                                        </div>

                                        <div class="form-group form-md-line-input form-md-floating-label
                @if ($errors->has('activity_ext')) has-error @endif ">
                                            <input type="text" class="form-control" id="form_activity_ext" name="activity_ext" value="{{ old('activity_ext') }}">
                                            <label for="form_activity_ext"><span class="imp">*&nbsp;</span>其它参数，如金额限制，等。</label>
                                            <span class="help-block">其它参数，如金额限制，等。</span>
                                        </div>


                                        <div class="form-group form-md-line-input form-md-floating-label
                @if ($errors->has('activity_desc')) has-error @endif ">
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
