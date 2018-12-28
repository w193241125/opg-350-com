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
    <link href="{{asset('x-edittable/bootstrap3-editable/css/bootstrap-editable.css')}}" rel="stylesheet" type="text/css" />
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
                <li><a href="#"><i class="fa fa-dashboard"></i> 系统设置</a></li>
                <li class="active">抽奖配置</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-4">
                    <div class="box">
                        <!-- /.box-header -->
                            {{--一等奖...--}}
                            <div class="portlet light bordered formBox" id="lottery">
                                <div class="portlet-title">
                                    <div class="caption font-green">
                                        <i class="icon-pin font-green"></i>
                                        <span class="caption-subject bold uppercase">抽奖配置</span>
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
                                    <form role="form" id="lottery_one_Form">
                                        {{ csrf_field() }}
                                        <div class="form-body">
                                            <div class="row ">
                                                <div class="col-md-6">
                                                    <div class="form-group form-md-line-input form-md-floating-label ">
                                                        <select class="form-control edited " id="form_parent_menu_1" name="turn">
                                                            <option value="0" >--抽奖等级--</option>
                                                            @foreach($turns as $v)
                                                                <option value="{{$v['turn_id']}}" @if(old('turn')) selected="selected" @endif>{{$v['turn_name']}}</option>
                                                            @endforeach
                                                        </select>
                                                        <label for="form_parent_menu_1"><span class="imp">*&nbsp;</span>当前抽奖等级设置</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                $('#form_parent_menu_1').change(function () {
                                                    var id = $('#form_parent_menu_1').val();
                                                    var url = "{{ route('ajaxGetTurns') }}";
                                                    //重新获取下拉列表
                                                    $.getJSON(
                                                        url,
                                                        {turn_id:id},
                                                        function (data) {
                                                            if (data != []) {
                                                                $("#total").html("");
                                                                $("#total").html('奖项人数：'+ data[0].turn_total_number);
                                                                $("#form_total").attr("value","");
                                                                $("#form_total").addClass('edited');
                                                                $("#form_total").val(data[0].turn_total_number);

                                                                $("#prenum").html("");
                                                                $("#prenum").html('每次中奖人数：'+ data[0].turn_pre_num);
                                                                $("#form_pre_num").attr("value","");
                                                                $("#form_pre_num").addClass('edited');
                                                                $("#form_pre_num").attr("value",data[0].turn_pre_num);
                                                            }
                                                        }
                                                    );
                                                });
                                            </script>
                                            <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('pre_num')) { echo "has-error"; } ?> ">
                                                <input type="text" class="form-control" id="form_pre_num" name="pre_num" value="{{ old('pre_num') }}">
                                                <label for="form_pre_num"><span class="imp">*&nbsp;</span><span id="prenum">每次中奖人数</span></label>
                                                <span class="help-block form_pre_num">每点击一次抽奖抽几个人</span>
                                            </div>
                                            <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('total')) { echo "has-error"; } ?>">
                                                <input type="text" class="form-control" id="form_total" name="total" value="{{ old('total') }}">
                                                <label for="form_total"><span class="imp">*&nbsp;</span><span id="total">奖项人数</span></label>
                                                <span class="help-block">总共抽多少人</span>
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
                            url: '/system/lotteryone/',
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
                {{--加抽--}}
                <div class="col-xs-4">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="portlet light bordered formBox" id="lottery_two">
                            <div class="portlet-title">
                                <div class="caption font-green">
                                    <i class="icon-pin font-green"></i>
                                    <span class="caption-subject bold uppercase">额外加抽</span>
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
                                <form role="form" id="lottery_two_Form">
                                    {{ csrf_field() }}
                                    <div class="form-body">
                                        <div class="row ">
                                            <div class="col-md-12">
                                                <div class="form-group form-md-line-input form-md-floating-label ">
                                                    <input type="text" class="form-control" id="form_mark" name="mark" value="{{ old('mark') }}">
                                                    <label for="form_mark"><span class="imp">*&nbsp;</span>加抽标识</label>
                                                    <span class="help-block form_mark">使用：4,5,6，以此类推...</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('pre_num')) { echo "has-error"; } ?> ">
                                            <input type="text" class="form-control" id="form_pre_nums" name="pre_nums" value="{{ old('pre_nums') }}">
                                            <label for="form_pre_nums"><span class="imp">*&nbsp;</span>每次中奖人数</label>
                                            <span class="help-block form_pre_nums">每点击一次抽奖抽几个人</span>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('totals')) { echo "has-error"; } ?>">
                                            <input type="text" class="form-control" id="form_totals" name="totals" value="{{ old('totals') }}">
                                            <label for="form_totals"><span class="imp">*&nbsp;</span>奖项人数</label>
                                            <span class="help-block">总共抽多少人</span>
                                        </div>
                                        <div class="form-group">
                                            <span class="font_style">参加人员：</span><br>
                                            <input type="radio" id="man" name="man" class="flat-red man" value="100" checked>
                                            <label for="man" class="font_style">所有人</label><br>
                                            <input type="radio" id="woman" name="man" class="flat-red man" value="200">
                                            <label for="woman" class="font_style">部分人</label><br>
                                            <input type="checkbox" id="all_1" name="peoples[]" class="flat-red" value="1" disabled="true">
                                            <label for="all_1" class="font_style">管理</label>
                                            <input type="checkbox" id="all_2" name="peoples[]" class="flat-red" value="2" disabled="true">
                                            <label for="all_2" class="font_style">大于6个月的</label>
                                            <input type="checkbox" id="all_3" name="peoples[]" class="flat-red" value="3" disabled="true">
                                            <label for="all_3" class="font_style">少于6个月的</label><br>
                                            <input type="checkbox" id="all_4" name="peoples[]" class="flat-red" value="4" disabled="true">
                                            <label for="all_4" class="font_style">嘉宾</label>
                                        </div>
                                        <script>
                                            $("input[type=radio]").on('click',function () {
                                                var man= $('input:radio:checked').val();
                                                if ( man == 100 ) {
                                                    $('#all_1').attr('disabled','true');
                                                    $('#all_2').attr('disabled','true');
                                                    $('#all_3').attr('disabled','true');
                                                    $('#all_4').attr('disabled','true');
                                                    $('input:checkbox').each(function() {
                                                        console.log(111)
                                                        $(this).attr('checked', false);
                                                    });
                                                }else{
                                                    $('#all_1').removeAttr("disabled");
                                                    $('#all_2').removeAttr("disabled");
                                                    $('#all_3').removeAttr("disabled");
                                                    $('#all_4').removeAttr("disabled");
                                                }
                                            });
                                        </script>
                                    </div>
                                    <div class="form-actions noborder">
                                        <button type="submit" class="btn green lottery_two" >确认加抽</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                </div>
                </div>
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
                                    $(".form_mark").html('使用：4,5,6，以此类推... 标识√');
                                    $(".form_mark").css('color','#36c6d3');
                                }
                            }
                        );
                    });
                    $('.lottery_two').on('click',function () {
                        console.log('lottery_two')
                        var _item = $(this);
                        var _form = $('#lottery_two_Form');
                        $.ajax({
                            url: '/system/lotterytwo/',
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
                                //更新选项
                                $.getJSON(
                                    '/system/ajaxGetTurn/',
                                    {},
                                    function (data) {
                                        if (data != []) {
                                            //先清空下拉列表
                                            $('#form_parent_menu_1').empty();
                                            $('#form_parent_menu_1').append("<option value='0' selected>--抽奖等级--</option>");
                                            console.log(data)
                                            var html = '';
                                            $(data).each(function (index, element) {
                                                html += "<option value='"+element.turn_id+"' @if(old('turn_id')) selected='selected' @endif >"+element.turn_name+"</option>"
                                            });
                                            $('#form_parent_menu_1').append(html);
                                        }
                                    }
                                );
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
                <?php if ($errors->has('username')) { echo "has-error"; } ?> ">
                                            <input type="text" class="form-control" id="form_username" name="username" value="{{ old('username') }}">
                                            <label for="form_username"><span class="imp">*&nbsp;</span>ID</label>
                                            <span class="help-block form_username">用户名序号</span>
                                        </div>
                                        <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('trueName')) { echo "has-error"; } ?>">
                                            <input type="text" class="form-control" id="form_trueName" name="trueName" value="{{ old('trueName') }}">
                                            <label for="form_trueName"><span class="imp">*&nbsp;</span>姓名</label>
                                            <span class="help-block">请填写真实姓名</span>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input form-md-floating-label ">
                                                <select class="form-control edited " id="form_parent_menu_7" name="level">
                                                    <option value="0" >--用户类型--</option>
                                                        <option value="1" >管理</option>
                                                        <option value="2" >工作6个月以上员工</option>
                                                        <option value="3" >工作6个月内员工</option>
                                                        <option value="4" >嘉宾</option>
                                                        <option value="5" >其它</option>
                                                </select>
                                                <label for="form_parent_menu_7"><span class="imp">*&nbsp;</span>用户标识设置</label>
                                            </div>
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
                            url: '/system/lotterythree/',
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
    <script type="text/javascript">
        //单选框美化
//        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
//            checkboxClass: 'icheckbox_flat-green',
//            radioClass   : 'iradio_flat-green'
//        })

    </script>
    <!-- BEGIN THEME GLOBAL SCRIPTS 这个js控制 添加菜单 的 label 上移与下移 -->
    <script src="{{asset('assets/admin/layouts/scripts/app.min.js')}}" type="text/javascript"></script>
@endsection