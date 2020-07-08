@extends('layouts.app')

@section('title', '奖励管理')
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
                充值奖励配置
                <small>Version 1.0</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 活动设置</a></li>
                <li class="active">充值奖励配置</li>
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
                                    <span class="caption-subject bold uppercase">添加新充值奖励</span>
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
                                        <div class="row ">
                                            <div class="col-md-6">
                                                <div class="form-group form-md-line-input form-md-floating-label ">
                                                    <select class="form-control edited " id="form_parent_menu_2" name="activity_name">
                                                        <option value="0" >--选择活动-</option>
                                                        @foreach($activity as $v)
                                                            <option value="{{$v['activity_name']}}/!/{{$v['game_name']}}" @if(old('activity')) selected="selected" @endif>{{$v['game_name']}}:{{$v['activity_title']}}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="form_parent_menu_1"><span class="imp">*&nbsp;</span>活动设置</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('money')) { echo "has-error"; } ?> ">
                                            <input type="text" class="form-control" id="form_money" name="money" value="{{ old('money') }}">
                                            <label for="form_money"><span class="imp">*&nbsp;</span><span id="activitytitle">累充金额</span></label>
                                            <span class="help-block form_money">累充金额</span>
                                        </div>

                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('award')) { echo "has-error"; } ?> ">
                                            <input type="text" class="form-control" id="form_awards" name="award" value="{{ old('award') }}">
                                            <label for="form_award"><span class="imp">*&nbsp;</span><span id="activityaward">活动奖品：</span></label>
                                            <span class="help-block form_award">活动奖品</span>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('award_ext')) { echo "has-error"; } ?> ">
                                            <input type="text" class="form-control" id="form_award_exts" name="award_ext" value="{{ old('award_ext') }}">
                                            <label for="form_award_ext"><span class="imp">*&nbsp;</span><span id="activityaward_ext">其它说明：</span></label>
                                            <span class="help-block form_award_ext">其它说明</span>
                                        </div>
                                    </div>
                                    <div class="form-actions noborder">
                                        <button type="submit" class="btn green lottery_three" >添加奖励</button>
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
                            url: '/operator/award_add',
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
                                        <span class="caption-subject bold uppercase">活动奖励更改</span>
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
                                        <?php $ac = [
                                            'pay_back'=>'充值返利',
                                            'cost_back'=>'消费返利',
                                            'recharge'=>'充值排行榜',
                                            'consume'=>'消费排行榜',
                                            'login_gift'=>'每日登录礼包',
                                            'pay_gift'=>'每日充值礼包',
                                            'guestbook'=>'留言/祝福墙',
                                            'pay_back_box'=>'充值返宝箱(最大)',
                                        ];
                                        $gn = [
                                            'xlczg_zf'=>'老后台龙城专服',
                                            'xlczg_xzf'=>'新龙城专服',
                                            'xlczg_hf'=>'龙城混服',
                                        ];
                                        ?>
                                    <form role="form" id="lottery_one_Form" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="form-body">
                                            <div class="row ">
                                                <div class="col-md-6">
                                                    <div class="form-group form-md-line-input form-md-floating-label ">
                                                        <select class="form-control edited " id="form_parent_menu_1" name="activity">
                                                            <option value="0" >--选择活动-</option>
                                                            @foreach($activity as $v)
                                                                <option value="{{$v['activity_name']}}/!/{{$v['game_name']}}" @if(old('activity')) selected="selected" @endif>{{$gn[$v['game_name']] OR $v['game_name']}}：{{$v['activity_title']}}</option>
                                                            @endforeach
                                                        </select>
                                                        <label for="form_parent_menu_1"><span class="imp">*&nbsp;</span>活动设置</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputFile">礼包文件上传</label>
                                                <input type="file" id="csv_file">

                                                <p class="help-block">选择csv，xls，xlsx文件</p>

                                            </div>
                                        </div>
                                        <div class="form-actions noborder">
                                            <a type="submit" class="btn green lottery_ones" >上传礼包</a>
                                        </div>
                                    </form>
                                        <p class="help-block" style="color:red">文件内容说明：</p>
                                        <span style="color:red">请在excel中按以下格式编辑(不需要表头，不需要表头，不需要表头)，</span>
                                        <div class="bs-example" data-example-id="contextual-table">
                                            <table class="table table-bordered tbext">
                                                <thead>
                                                <tr >
                                                    <td >A列: 礼包码</td>
                                                    <td >B列: 第几天的礼包(只有一个礼包的填1)</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>bcife4121c5464ae</td>
                                                    <td>1</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                </div>

                            </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <script>
                    $('.lottery_ones').on('click',function () {
                        var _item = $(this);
                        var activity = $('#form_parent_menu_1').val();
                        var files = $('#csv_file').prop('files');
                        var data = new FormData();
                        data.append('csv_file', files[0]);
                        data.append('activity', activity);
                        console.log(data)
                        $.ajax({
                            url: '/operator/gift_bag_upload',
                            type:'post',
                            data:data,
                            cache: false,
                            contentType: false,
                            processData: false,
                            headers : {
                                'X-CSRF-TOKEN': $("input[name='_token']").val()
                            },
                            beforeSend : function(){
                                _item.attr('disabled','true');
                            },
                            success:function (response) {
                                console.log(response)
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

                {{--上传excel--}}
                <div class="col-xs-4">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="portlet light bordered formBox" id="lottery_three">
                            <div class="portlet-title">
                                <div class="caption font-green">
                                    <i class="icon-pin font-green"></i>
                                    <span class="caption-subject bold uppercase">添加新充值奖励</span>
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
                                <form role="form" id="award_bulk_add">
                                    {{ csrf_field() }}
                                    <div class="form-body">
                                        <div class="form-group">
                                            <textarea name="award_bulk_add" id="" cols="30" rows="10"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-actions noborder">
                                        <button type="submit" class="btn green award_bulk_add" >批量添加</button>
                                    </div>
                                </form>
                            </div>
                            <span style="color:red">请在excel中按以下格式编辑后复制(整行复制)到框中(不需要表头)，</span>
                            <br>
                            <span style="color:red"> A列为游戏缩写，B列为活动名，C列为累充金额，D列为奖品，E列为其它说明，如累充门槛</span>
                            <br>
                            <span style="color:red">奖品只能都在一个单元格</span>
                            <div class="bs-example" data-example-id="contextual-table">
                                <table class="table table-bordered tbext">
                                    <thead>
                                    <tr >
                                        <td >A列: xlczg</td>
                                        <td >B列: activity1</td>
                                        <td >C列: 第一名</td>
                                        <td >D列: 热血神剑*1</td>
                                        <td >E列: 25000</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $('.award_bulk_add').on('click',function () {
                        console.log('lottery_three')
                        var _item = $(this);
                        var _form = $('#award_bulk_add');
                        console.log(_form.serializeArray());
                        $.ajax({
                            url: '/operator/award_bulk_add',
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
