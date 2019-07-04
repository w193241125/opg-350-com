<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel">
        编辑用户充值信息
    </h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <label for="server">区服</label>
        <input type="text" class="form-control" id="server" value="{{$data->server_name}}" disabled="disabled">
    </div>
    <div class="form-group">
        <label for="role_name">角色名</label>
        <input type="text" class="form-control" id="role_name" value="{{$data->role_name}}" disabled="disabled">
    </div>
    <div class="form-group">
        <label for="total">充值金额</label>
        <input type="text" class="form-control" id="upd_total" name="total" value="{{$data->total}}">
    </div>
    <input type="hidden" class="form-control" id="upd_id" name="total" value="{{$data->id}}">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">暂不修改
    </button>
    <button type="button" class="btn btn-primary" id="bind_btn" onclick="upd_user();">
        更新
    </button>
</div>

<script>
    function upd_user(){
        var id = $('#upd_id').val()
        var consume = $('#consume').prop('checked');
        var total = $('#upd_total').val()
        $.ajax({
            'type' : 'POST',
            'url' :  '/operator/user_upd',
            data: {id: id,total:total,consume:consume},
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success : function (res) {
                if(res.status==200){
                    swal("更新成功！", res.message, "success");
                    location.reload()
                }else{
                    swal("更新！", res.message, "error");
                }
            }
        });
    }
</script>