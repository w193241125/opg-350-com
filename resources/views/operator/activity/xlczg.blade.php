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
                用户配置
                <small>Version 1.0</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 系统设置</a></li>
                <li class="active">用户配置</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <script>
                    //实时判断用户名是否存在
                    $("#form_mark").bind("input propertychange",function(event){
                        mark = $("#form_mark").val();
                        url =  "{{ route('ajaxGetMarkIfExist') }}";
                        $.getJSON(
                            url,
                            {mark:mark},
                            function (data) {
                                if (data.length != 0) {
                                    console.log(data)
                                    $("#form_mark").parent('div').addClass('has-error');
                                    $(".form_mark").html('标识已存在');
                                    $(".form_mark").css('color','red');
                                }else{
                                    $("#form_mark").parent('div').removeClass('has-error');
                                    $(".form_mark").html('使用：五等奖，以此类推... 标识√');
                                    $(".form_mark").css('color','#36c6d3');
                                }
                            }
                        );
                    });

                </script>
                {{--其它--}}
                <div class="col-xs-4">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="portlet light bordered formBox" id="lottery_three">
                            <div class="portlet-title">
                                <div class="caption font-green">
                                    <i class="icon-pin font-green"></i>
                                    <span class="caption-subject bold uppercase">添加用户</span>
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
                <?php if ($errors->has('server_name')) { echo "has-error"; } ?> ">
                                            <input type="text" class="form-control" id="form_server_name" name="server_name" value="{{ old('server_name') }}">
                                            <label for="form_server_name"><span class="imp">*&nbsp;</span>区服ID</label>
                                            <span class="help-block form_server_name">如：1服</span>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('role_id')) { echo "has-error"; } ?> ">
                                            <input type="text" class="form-control" id="form_role_id" name="role_id" value="{{ old('role_id') }}">
                                            <label for="form_role_id"><span class="imp">*&nbsp;</span>角色ID</label>
                                            <span class="help-block form_role_id">如：1</span>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('role_name')) { echo "has-error"; } ?>">
                                            <input type="text" class="form-control" id="form_role_name" name="role_name" value="{{ old('role_name') }}">
                                            <label for="form_role_name"><span class="imp">*&nbsp;</span>角色名</label>
                                            <span class="help-block">请填写角色名</span>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('total')) { echo "has-error"; } ?>">
                                            <input type="text" class="form-control" id="form_total" name="total" value="{{ old('total') }}">
                                            <label for="form_total"><span class="imp">*&nbsp;</span>充值金额</label>
                                            <span class="help-block">请填写充值金额</span>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('game_name')) { echo "has-error"; } ?> ">
                                            <input type="text" class="form-control" id="form_game_name" name="game_name" value="{{ old('game_name') }}">
                                            <label for="form_game_name"><span class="imp">*&nbsp;</span>游戏缩写</label>
                                            <span class="help-block form_game_name">游戏缩写,如xlczg，由技术部提供</span>
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <span class="font_style">充值/消费：</span><br>
                                        <input type="radio" id="type_1" name="type" class="flat-red type" value="recharge" checked>
                                        <label for="type_1" class="font_style">充值</label>
                                        <input type="radio" id="type_2" name="type" class="flat-red type" value="consume">
                                        <label for="type_2" class="font_style">消费</label>
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
                            url: '/operator/add_user',
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
                    //实时判断用户名是否存在
                    $("#form_username").bind("input propertychange",function(event){
                        uid = $("#form_username").val();
                        url =  "{{ route('ajaxcheckUid') }}";
                        $.getJSON(
                            url,
                            {uid:uid},
                            function (data) {
                                if (data.length != 0) {
                                    console.log(data)
                                    $("#form_username").parent('div').addClass('has-error');
                                    $(".form_username").html('用户ID已存在');
                                    $(".form_username").css('color','red');
                                }else{
                                    $("#form_username").parent('div').removeClass('has-error');
                                    $(".form_username").html('用户名');
                                    $(".form_username").css('color','#36c6d3');
                                }
                            }
                        );
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
@endsection