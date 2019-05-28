
var nametxt = $('.slot');
var phonetxt = $('.name');
var pcount = xinm.length-1;//参加人数
var runing = true;
var trigger = true;
var num = 0;	//获奖id
var turn_id;	//当前轮数
var Lotterynumber = 0; //设置单次抽奖人数
var Lotterynumber_global = 0;	//全局抽奖人数

$(function () {
	nametxt.css('background-image','url('+xinm[0]+')');
	phonetxt.html(phone[0]);
    //ajax获取配置信息
    $.post(
        "http://lucky.350.com/api/get_config.php",
        {_token: "cwDAtlwdE3WBmZVv"},
        function (data) {
        	if(data.code == 1){
        		turn_id = data.data.turn_id;
                Lotterynumber = Lotterynumber_global = data.data.turn_pre_num;
			}else{
        		alert("获取配置信息失败！");
			}
        },
        'json'
    );
});

// 开始停止
function start() {
    //清空获奖列表
    $('.luck-user-list').html('');
    //ajax获取配置信息
    $.post(
        "http://lucky.350.com/api/get_config.php",
        {_token: "cwDAtlwdE3WBmZVv"},
        function (data) {
            if(data.code == 1){
                turn_id = data.data.turn_id;
                Lotterynumber = Lotterynumber_global = data.data.turn_pre_num;
                if (runing) {
                    runing = false;
                    $('#start').text('停止');
                    startNum()
                } else {
                    $('#start').text('自动抽取中('+ Lotterynumber+')');
                    zd();
                }
            }else{
                alert("获取配置信息失败！");
            }
        },
        'json'
    );
}
// 开始抽奖
function startLuck() {
	runing = false;
	$('#btntxt').removeClass('start').addClass('stop');
	startNum()
}
// 循环参加名单
function startNum() {
	num = Math.floor(Math.random() * pcount);
	nametxt.css('background-image','url('+xinm[num]+')');
	phonetxt.html(num + '-' + phone[num]);
	t = setTimeout(startNum, 100);
}
// 停止跳动
function stop() {
	pcount = xinm.length-1;
	clearInterval(t);
	t = 0;
    nametxt.css('background-image','url('+xinm[0]+')');
    phonetxt.html(phone[0]);
}

// 打印中奖人
function zd() {
	if (trigger) {
		trigger = false;
		var i = 0;
		if (pcount >= Lotterynumber) {
			stopTime = window.setInterval(function () {
				if (runing) {
					runing = false;
					$('#btntxt').removeClass('start').addClass('stop');
					startNum();
				} else {
					runing = true;
					$('#btntxt').removeClass('stop').addClass('start');
					stop();

					i++;
					Lotterynumber--;

					$('#start').text('自动抽取中('+ Lotterynumber+')');
                    //ajax获取抽奖结果
                    $.post(
                        "http://lucky.350.com/api/get_prize.php",
                        {turn_id: turn_id, _token: "cwDAtlwdE3WBmZVv"},
                        function (data) {
                            num = data.data;
                            console.log('res1:'+num);
                            if(data.data){
                                nametxt.css('background-image','url('+xinm[num]+')');
                                phonetxt.html(num+'-'+phone[num]);
                                //打印中奖者名单
                                $('.luck-user-list').prepend("<li><div class='portrait' style='background-image:url("+xinm[num]+")'></div><div class='luckuserName'>"+num+'-'+phone[num]+"</div></li>");
                                $('.modality-list ul').append("<li><div class='luck-img' style='background-image:url("+xinm[num]+")'></div><p>" + num + '-'+phone[num]+"</p></li>");
							}else{
                                alert('当前奖项已抽完');
                                window.clearInterval(stopTime);
                                $('#start').text("开始");
                                Lotterynumber = Lotterynumber_global;
                                trigger = true;
							}
                        },
                        'json'
                    );
					if ( i == Lotterynumber_global ) {
						window.clearInterval(stopTime);
						$('#start').text("开始");
						Lotterynumber = Lotterynumber_global;
						trigger = true;
					}
				}
			},500);
		}
	}
}

