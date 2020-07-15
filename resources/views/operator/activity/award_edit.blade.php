<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel">
        编辑用户充值信息
    </h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <label for="activity_name">活动名称</label>
        <input type="text" class="form-control" id="activity_names" value="{{$data->activity_name}}">
    </div>

    <div class="form-group">
        <label for="game_name">游戏标识</label>
        <input type="text" class="form-control" id="game_names" value="{{$data->game_name}}">
    </div>

    <div class="form-group">
        <label for="money">累充/累消金额</label>
        <input type="text" class="form-control" id="money" value="{{$data->money}}">
    </div>

    <div class="form-group">
        <label for="award">奖品</label><br>
        <textarea name="award" id="award" cols="30" rows="10">{{$data->award}}</textarea>
    </div>

    <div class="form-group">
        <label for="role_name">其它参数</label>
        <input type="text" class="form-control" id="award_ext" value="{{$data->award_ext}}">
    </div>

    <input type="hidden" class="form-control" id="upd_id" name="total" value="{{$data->id}}">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" id="close_mod" data-dismiss="modal">暂不修改
    </button>
    <button type="button" class="btn btn-primary" id="bind_btn" onclick="upd_award();">
        更新
    </button>
</div>
