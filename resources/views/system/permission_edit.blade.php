
<div class="portlet light bordered formBox" id="editBox">
    <div class="portlet-title">
        <div class="caption font-green">
            <i class="icon-pin font-green"></i>
            <span class="caption-subject bold uppercase">编辑用户</span>
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
            <form role="form" method="post" id="editForm" action="{{ route("permission.update", $pm_info->id) }}" >
                <input type="hidden" name="_method" value="PUT">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ $pm_info->id }}">
                <div class="form-body">
                    <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('name')) { echo "has-error"; } ?> ">
                        <input type="text" class="form-control  @if($pm_info->name) edited @endif" id="form_name" name="name" value="{{ $pm_info->name }}">
                        <label for="form_name"><span class="imp">*&nbsp;</span>权限标识名(绑定路由名)</label>
                        <span class="help-block form_name">权限标识名(绑定路由名)</span>
                    </div>
                    <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('pm_display_name')) { echo "has-error"; } ?> ">
                        <input type="text" class="form-control  @if($pm_info->pm_display_name) edited @endif" id="form_pm_display_name" name="pm_display_name" value="{{ $pm_info->pm_display_name }}">
                        <label for="form_pm_display_name"><span class="imp">*&nbsp;</span>权限展示名称</label>
                        <span class="help-block form_pm_display_name">权限展示名称</span>
                    </div>
                    <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('pm_description')) { echo "has-error"; } ?> ">
                        <input type="text" class="form-control  @if($pm_info->pm_description) edited @endif" id="form_pm_description" name="pm_description" value="{{ $pm_info->pm_description }}">
                        <label for="form_pm_description"><span class="imp">*&nbsp;</span>权限描述</label>
                        <span class="help-block form_pm_description">权限功能的介绍</span>
                    </div>
                    <div class="form-group form-md-line-input form-md-floating-label
                <?php if ($errors->has('pm_type')) { echo "has-error"; } ?> ">
                        <input type="text" class="form-control  @if($pm_info->pm_type) edited @endif" id="form_pm_type" name="pm_type" value="{{ $pm_info->pm_type }}">
                        <label for="form_pm_type"><span class="imp">*&nbsp;</span>权限类型</label>
                        <span class="help-block form_pm_type">权限类型</span>
                    </div>

                </div>
                <div class="form-actions noborder">
                    <button type="submit" class="btn blue editButton" >更新用户</button>
                </div>
            </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var user_pid = "{{$pm_info->position_id}}";
        var id = $('#form_parent_menu_1').val();
        var url = "{{ route('UserController.ajaxGetPosition') }}";
        if (id>0){
            //重新获取下拉列表
            $.getJSON(
                url,
                {dept_id:id},
                function (data) {
                    if (data != []) {
                        var html = '';
                        $(data).each(function (index, element) {
                            if (user_pid != element.id){
                                html += "<option value='"+element.id+"'>"+element.position_name+"</option>"
                            }else{
                                html += "<option value='"+element.id+"'selected='selected'>"+element.position_name+"</option>"
                            }
                        });
                        $('#form_parent_menu_2').append(html);
                    }
                }
            );
        }
    });
    //单选框美化
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
    })

    //实时判断用户名是否存在
    $("#form_name").bind("input propertychange",function(event){
        console.log($("#form_name").val())
        name = $("#form_name").val();
        id = $("input[name='id']").val();
        console.log(id)
        url =  "{{ route('PermissionController.ajaxCheckPermission') }}";
        $.getJSON(
            url,
            {name:name,type:'edit',id:id},
            function (data) {
                if (data.length != 0) {
                    $("#form_name").parent('div').addClass('has-error');
                    $(".form_name").html('权限名已存在');
                    $(".form_name").css('color','red');
                }else{
                    $("#form_name").parent('div').removeClass('has-error');
                    $(".form_name").html('权限英文名');
                    $(".form_name").css('color','#36c6d3');
                }
            }
        );
    });
</script>