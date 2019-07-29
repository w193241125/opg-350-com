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
    <div class="form-group form-md-line-input form-md-floating-label">
        <label for="form_game_name">游戏缩写</label>
        <input type="text" class="form-control" id="form_game_name" name="game_name" value="{{$data->game_name }}">
        <span class="help-block form_game_name">游戏缩写,如xlczg，由技术部提供,请谨慎修改</span>
    </div>
    <div class="form-group form-md-line-input form-md-floating-label ">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="activity_time" class="form-control pull-right" id="reservations" value="{{$data->start_date}}~{{$data->end_date}}">
            <span class="help-block form_activity_time">活动时间</span>
        </div>
    </div>
    <div class="form-group">
        <label for="total">充值金额</label>
        <input type="text" class="form-control" id="upd_total" name="total" value="{{$data->total}}">
    </div>
    <input type="hidden" class="form-control" id="upd_id" name="total" value="{{$data->id}}">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" id="close_mod" data-dismiss="modal">暂不修改
    </button>
    <button type="button" class="btn btn-primary" id="bind_btn" onclick="upd_user();">
        更新
    </button>
</div>

<script>

</script>