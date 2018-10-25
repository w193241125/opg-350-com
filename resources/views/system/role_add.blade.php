@can('role.add')
<div class="portlet light bordered formBox" id="createBox">
    <div class="portlet-title">
        <div class="caption font-green">
            <i class="icon-pin font-green"></i>
            <span class="caption-subject bold uppercase">添加角色</span>
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

                <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('name')) { echo "has-error"; } ?> ">
                    <input type="text" class="form-control" id="form_name" name="name" value="{{ old('name') }}">
                    <label for="form_name"><span class="imp">*&nbsp;</span>角色标识名(绑定路由名)</label>
                    <span class="help-block form_name">角色英文名</span>
                </div>
                <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('role_display_name')) { echo "has-error"; } ?> ">
                    <input type="text" class="form-control" id="form_role_display_name" name="role_display_name" value="{{ old('role_display_name') }}">
                    <label for="form_role_display_name"><span class="imp">*&nbsp;</span>角色展示名称</label>
                    <span class="help-block form_role_display_name">角色展示名称</span>
                </div>
                <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('role_description')) { echo "has-error"; } ?> ">
                    <input type="text" class="form-control" id="form_role_description" name="role_description" value="{{ old('role_description') }}">
                    <label for="form_role_description"><span class="imp">*&nbsp;</span>角色描述</label>
                    <span class="help-block form_role_description">角色功能的介绍</span>
                </div>
            </div>
            <div class="form-actions noborder">
                <button type="submit" class="btn green createButton" >创建角色</button>
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
    $("#form_name").bind("input propertychange",function(event){
        name = $("#form_name").val();
        url =  "{{ route('PermissionController.ajaxCheckPermission') }}";
        $.getJSON(
            url,
            {name:name,type:'create'},
            function (data) {
                if (data.length != 0) {
                    console.log(data)
                    $("#form_name").parent('div').addClass('has-error');
                    $(".form_name").html('角色已存在');
                    $(".form_name").css('color','red');
                }else{
                    $("#form_name").parent('div').removeClass('has-error');
                    $(".form_name").html('角色英文名');
                    $(".form_name").css('color','#36c6d3');
                }
            }
        );
    });
</script>
@endcan