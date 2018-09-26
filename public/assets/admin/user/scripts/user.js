/**
 * user 动态调用
 * Created by ADKi on 2017/4/1 0001.
 */
var user = function () {

    var user_select = {
        headerBox:'.add_user_html',
        createUser:'.create_user',
        close:'.close-link',
        createForm:'#createBox',
        box:'.box',
        editbox:'#editForm',
        middleBox:'.box-body',
        createButton:'.createButton',
        addButton:'.createButton',
    };
    var userInit = function () {
        $(user_select.headerBox).on('click', user_select.createUser, function () {
            $.ajax({
                type: 'GET',
                url:'/system/user/create',
                dataType:'html',
                success:function (htmlData) {
                    $(user_select.middleBox).hide();
                    $(user_select.createUser).hide();
                    $(user_select.headerBox).append(htmlData);
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
                            title:"未知错误",
                            text:"请联系管理员",
                            type:"error"
                        });
                        return false;
                    }
                }
            });
        });

        // 关闭表单
        $(user_select.headerBox).on('click', user_select.close, function () {
            $('.formBox').remove();
            $(user_select.createUser).show();
            $(user_select.middleBox).show();
            $(user_select.headerBox).show();
            if ($('.create_user').is(':hidden')) {
                $('.create_user').show();
            }
        });

        // 提交创建用户
        $(user_select.headerBox).on('click','.createButton',function () {
            var _item = $(this);
            var _form = $('#createForm');
            $.ajax({
                url:'/system/user',
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
                        window.location.href = '/system/user';
                    }, 1000);
                }
            }).fail(function(response) {
                if(response.status == 422){
                    var data = $.parseJSON(response.responseText);
                    var layerStr = "";
                    for(var i in data.errors){
                        layerStr += data.errors[i]+" ";
                    }
                    sweetAlert('错误', layerStr);
                }else{
                    sweetAlert('未知错误', '请重试');
                }
            }).always(function () {
                _item.removeAttr('disabled');
            });
        });

        /*
        * 修改表单
        * */
        $('#user_info').on('click', '.edituser', function () {
            var _item = $(this);
            $.ajax({
                url:_item.attr('data-href'),
                dataType:'html',
                success:function (htmlData) {
                    var box = $(user_select.box);
                    if (box.is(':visible')) {
                        $('.create_user').hide();
                        $(user_select.middleBox).hide();
                    }else{
                        var _createForm = $('.formBox');
                        // 创建表单存在的情况下
                        if (_createForm.length > 0) {
                            _createForm.remove();
                        }
                    }
                    $(user_select.headerBox).append(htmlData);
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
                            title:"未知错误",
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
        $(user_select.headerBox).on('click','.editButton',function () {
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
                        window.location.href = '/system/user';
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
        init : userInit
    }
}();

$(function () {
    user.init();
});