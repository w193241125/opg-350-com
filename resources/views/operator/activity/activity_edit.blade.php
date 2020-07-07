<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel">
        编辑活动信息
    </h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <label for="server">区服</label>
        <input type="text" class="form-control" id="activity_server" value="{{$data->activity_server}}">
    </div>


    <div class="form-group">
        <label>活动名</label>
        <select class="form-control" name="activity_type">
            <option value="pay_back" @if($data->activity_name == 'pay_back') selected @endif >充值返利</option>
            <option value="cost_back" @if($data->activity_name == 'cost_back') selected @endif>消费返利</option>
            <option value="recharge" @if($data->activity_name == 'recharge') selected @endif>充值排行榜</option>
            <option value="consume" @if($data->activity_name == 'consume') selected @endif>消费排行榜</option>
            <option value="login_gift" @if($data->activity_name == 'login_gift') selected @endif>每日登录礼包</option>
            <option value="pay_gift" @if($data->activity_name == 'pay_gift') selected @endif>每日充值礼包</option>
            {{--            <option value="lottery">抽奖</option>--}}
        </select>
    </div>

    <div class="form-group form-md-floating-label">
        <label>游戏名</label>
        <select class="form-control" name="game_name">
            <option value="xlczg_zf" @if($data->game_name == 'xlczg_zf') selected @endif >老后台龙城专服</option>
            <option value="xlczg_xzf" @if($data->game_name == 'xlczg_xzf') selected @endif>新龙城专服</option>
            <option value="xlczg_hf" @if($data->game_name == 'xlczg_hf') selected @endif>龙城混服</option>
        </select>
        <span class="help-block form_activity_name">游戏名,直接选取。</span>
    </div>

    <div class="form-group">
        <input type="text" class="form-control" id="form_sid" name="sid" value="{{$data->sid}}">
        <label for="form_sid"><span class="imp">*</span>新后台映射区服id</label>
    </div>

    <div class="form-group">
        <input type="text" class="form-control" id="form_server_id" name="sid" value="{{$data->server_id}}">
        <label for="form_server_id"><span class="imp">*</span>研发区服id</label>
    </div>

    <div class="form-group">
        <label for="activity_title">活动标题</label>
        <input type="text" class="form-control" id="activity_title" value="{{$data->activity_title}}">
    </div>

    <div class="form-group">
        <label for="game_name">游戏标识</label>
        <input type="text" class="form-control" id="game_name" value="{{$data->game_name}}">
    </div>

    <div class="form-group form-md-line-input form-md-floating-label ">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="activity_time" class="form-control pull-right" id="reservations" value="{{$data->activity_time}}">
            <span class="help-block form_activity_time">活动时间</span>
        </div>
    </div>

    <div class="form-group">
        <label for="role_name">其它参数/累充金额/消费</label>
        <input type="text" class="form-control" id="activity_ext" value="{{$data->activity_ext}}">
    </div>

    <div class="form-group">
        <label for="activity_desc">活动说明</label><br>
        <textarea name="activity_desc" id="activity_desc" cols="30" rows="10">{{$data->activity_desc}}</textarea>
    </div>

    <div class="form-group">
        <span class="font_style">活动开关：</span><br>
        <input type="radio" id="activity_status_1" name="activity_status" class="flat-red activity_status" value="1"
               {{$data->activity_status == 1 ? 'checked' : ''}}>
        <label for="activity_status" class="font_style">开</label>
        <input type="radio" id="activity_status_2" name="activity_status" class="flat-red activity_status" value="2"
                {{$data->activity_status == 2 ? 'checked' : ''}}>
        <label for="activity_statuss" class="font_style">关</label>
    </div>

    <input type="hidden" class="form-control" id="upd_id" name="total" value="{{$data->id}}">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" id="close_mod" data-dismiss="modal">暂不修改
    </button>
    <button type="button" class="btn btn-primary" id="bind_btn" onclick="upd_activity();">
        更新
    </button>
</div>

<script>


    $(function () {
        //Date range pickers
        $('#reservations').daterangepicker({
            "locale": {
                format: 'YYYY-MM-DD',
                separator: '~',
                applyLabel: "应用",
                cancelLabel: "取消",
                resetLabel: "重置",
                daysOfWeek: ["日", "一", "二", "三", "四", "五", "六"],
                monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            },
            "startDate": moment(),
            "endDate": moment()
        });

    });

    $(document).ready(function () {
        $('#reservations').val({{$data->activity_time}})
    });
</script>
