<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel">
        编辑用户充值信息
    </h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <label for="server">区服</label>
        <input type="text" class="form-control" id="activity_server" value="{{$data->activity_server}}">
    </div>

    <div class="form-group">
        <label for="role_name">活动标题</label>
        <input type="text" class="form-control" id="activity_title" value="{{$data->activity_title}}">
    </div>

    <div class="form-group">
        <label for="role_name">游戏标识</label>
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
                {{$data->activity_status == 0 ? 'checked' : ''}}>
        <label for="activity_statuss" class="font_style">关</label>
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
    function upd_user(){
        var id = $('#upd_id').val()
        var activity_title = $('#activity_title').val()
        var activity_time = $('#reservations').val()
        var activity_status = $('#activity_status').prop('checked');
        var activity_desc = $('#activity_desc').val();
        var game_name = $('#game_name').val();
        var activity_ext = $('#activity_ext').val()
        $.ajax({
            'type' : 'POST',
            'url' :  '/operator/activity_upd',
            data: { activity: id,
                    activity_title:activity_title,
                    activity_time:activity_time,
                    activity_status:activity_status,
                    activity_desc:activity_desc,
                    game_name:game_name,
                    activity_ext:activity_ext
            },
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success : function (res) {
                if(res.status==200){
                    $('#close_mod').trigger("click");
                    swal("更新成功！", res.message, "success");
                    location.reload()
                }else{
                    swal("更新！", res.message, "error");
                }
            }
        });
    }

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