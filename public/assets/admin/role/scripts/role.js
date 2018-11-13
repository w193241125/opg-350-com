/**
 * role 动态调用
 * Created by ADKi on 2017/4/1 0001.
 */
var role = function () {

    var role_select = {
        headerBox:'.add_role_html',
        createRole:'.create_role',
        close:'.close-link',
        createForm:'#createBox',
        box:'.box',
        editbox:'#editForm',
        middleBox:'.box-body',
        createButton:'.createButton',
        addButton:'.createButton',
    };
    var roleInit = function () {
        $(role_select.headerBox).on('click', role_select.createRole, function () {
            $.ajax({
                type: 'GET',
                url:'/system/role/create',
                dataType:'html',
                success:function (htmlData) {
                    $(role_select.middleBox).hide();
                    $(role_select.createRole).hide();
                    $(role_select.headerBox).append(htmlData);
                },
                error: function (xhr,errorText,errorType) {
                    var result =$.parseJSON(xhr.responseText);
                    if (result.error == "no_permissions") {
                        sweetAlert({
                            title:"您没有此权限",
                            text:"请联系管理员",
                            type:"error"
                        });
                        return false;
                    } else {
                        sweetAlert({
                            title:"未知错误1",
                            text:"请联系管理员",
                            type:"error"
                        });
                        return false;
                    }
                }
            });
        });

        // 关闭表单
        $(role_select.headerBox).on('click', role_select.close, function () {
            $('.formBox').remove();
            $(role_select.createRole).show();
            $(role_select.middleBox).show();
            $(role_select.headerBox).show();
            if ($('.create_role').is(':hidden')) {
                $('.create_role').show();
            }
        });

        // 提交创建用户
        $(role_select.headerBox).on('click','.createButton',function () {
            var _item = $(this);
            var _form = $('#createForm');
            $.ajax({
                url:'/system/role',
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
                    setTimeout(function(){
                        window.location.href = '/system/role';
                    }, 1000);
                }
            }).fail(function(response) {
                console.log(response);
                if(response.status == 422){
                    var data = $.parseJSON(response.responseText);
                    var layerStr = "";
                    for(var i in data.errors){
                        layerStr += data.errors[i]+" ";
                    }
                    sweetAlert('错误', layerStr);
                }else{
                    sweetAlert('未知错误2', '请重试');
                }
            }).always(function () {
                _item.removeAttr('disabled');
            });
        });

        /*
        * 修改表单
        * */
        $('#role_info').on('click', '.editrole', function () {
            var _item = $(this);
            $.ajax({
                url:_item.attr('data-href'),
                dataType:'html',
                success:function (htmlData) {
                    var box = $(role_select.box);
                    if (box.is(':visible')) {
                        $('.create_role').hide();
                        $(role_select.middleBox).hide();
                    }else{
                        var _createForm = $('.formBox');
                        // 创建表单存在的情况下
                        if (_createForm.length > 0) {
                            _createForm.remove();
                        }
                    }
                    $(role_select.headerBox).append(htmlData);
                },
                error: function (xhr,errorText,errorType) {
                    var result =$.parseJSON(xhr.responseText);
                    if (result.error == "no_roles") {
                        sweetAlert({
                            title:"您没有此权限",
                            text:"请联系管理员",
                            type:"error"
                        });
                        return false;
                    } else {
                        sweetAlert({
                            title:"未知错误3",
                            text:"请联系管理员",
                            type:"error"
                        });
                        return false;
                    }
                }
            });
        });

        /*
        * 保存编辑
        * */
        $(role_select.editbox).on('click','.editButton',function () {
            console.log('edit')
            var _item = $(this);
            var _form = $('#editForm');

            $.ajax({
                url:_form.attr('action'),
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
                    console.log(response.message)
                    sweetAlert(response.message);
                    setTimeout(function(){
                        window.location.href = '/system/role';
                    }, 1000);
                }
            }).fail(function(response) {
                console.log(response.message)
                if(response.status == 422){
                    var data = $.parseJSON(response.responseText);
                    var layerStr = "";
                    for(var i in data.errors){
                        layerStr += data.errors[i]+" ";
                    }
                    sweetAlert('错误', layerStr);
                }
            }).always(function () {
                console.log('always')
                _item.removeAttr('disabled');
            });
        });
    };

    return {
        init : roleInit
    }
}();

$(function () {
    role.init();
});