@can('menu.add')
<div class="portlet light bordered formBox" id="createBox">
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
        <form role="form" id="createForm">
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row ">
                    <div class="col-md-6">
                        <div class="form-group form-md-line-input form-md-floating-label ">
                            <select class="form-control edited " id="form_parent_menu_1" name="dept_id">
                                <option value="0" >--选择部门--</option>
                                @if(!empty($dept))
                                    @foreach($dept as $d)
                                        <option value="{{ $d->id }}" @if(old('dept_id')) selected="selected" @endif>{{ $d->dept_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <label for="form_parent_menu_1"><span class="imp">*&nbsp;</span>部门</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-md-line-input form-md-floating-label ">
                            <select class="form-control edited " id="form_parent_menu_2" name="position_id">
                                <option value="0" selected>选择职务</option>
                            </select>
                            <label for="form_parent_menu_1"><span class="imp">*&nbsp;</span>职务</label>
                        </div>
                    </div>
                </div>
                <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('username')) { echo "has-error"; } ?> ">
                    <input type="text" class="form-control" id="form_username" name="username" value="{{ old('username') }}">
                    <label for="form_username"><span class="imp">*&nbsp;</span>用户名</label>
                    <span class="help-block form_username">用户名</span>
                </div>
                <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('password')) { echo "has-error"; } ?>">
                    <input type="text" class="form-control" id="form_password" name="password" value="{{ old('password') }}">
                    <label for="form_password">密码，不填则默认为123456</label>
                    <span class="help-block">密码，不填则默认为123456</span>
                </div>
                <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('trueName')) { echo "has-error"; } ?>">
                    <input type="text" class="form-control" id="form_trueName" name="trueName" value="{{ old('trueName') }}">
                    <label for="form_trueName"><span class="imp">*&nbsp;</span>姓名</label>
                    <span class="help-block">请填写真实姓名</span>
                </div>
                <div class="form-group">
                    <span class="font_style">性别：</span>
                    <input type="radio" id="man" name="sex" class="flat-red" value="1" @if(old('sex', 0) == 1) checked="checked"@endif>
                    <label for="man" class="font_style">男</label>
                    <input type="radio" id="woman" name="sex" class="flat-red" value="2" @if(old('sex', 0) == 2) checked="checked"@endif>
                    <label for="woman" class="font_style">女</label>
                </div>
            </div>
            <div class="form-actions noborder">
                <button type="submit" class="btn green createButton" >创建用户</button>
            </div>
        </form>
    </div>

</div>
<script type="text/javascript">
    $('#form_parent_menu_1').change(function () {
        var id = $('#form_parent_menu_1').val();
        var url = "{{ route('UserController.ajaxGetPosition') }}";
        //先清空下拉列表
        $('#form_parent_menu_2').empty();
        $('#form_parent_menu_2').append("<option value='0' selected>--请选择职务--</option>");
        //重新获取下拉列表
        $.getJSON(
                url,
                {dept_id:id},
                function (data) {
                    if (data != []) {
                        var html = '';
                        $(data).each(function (index, element) {
                            html += "<option value='"+element.id+"' @if(old('position_id')) selected='selected' @endif >"+element.position_name+"</option>"
                        });
                        $('#form_parent_menu_2').append(html);
                    }
                }
        );
    });
    //单选框美化
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
    })

    //实时判断用户名是否存在
    $("#form_username").bind("input propertychange",function(event){
        username = $("#form_username").val();
        url =  "{{ route('UserController.ajaxCheckUsername') }}";
        $.getJSON(
            url,
            {username:username,type:'create'},
            function (data) {
                if (data.length != 0) {
                    console.log(data)
                    $("#form_username").parent('div').addClass('has-error');
                    $(".form_username").html('用户名已存在');
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
@endcan