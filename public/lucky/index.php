<?php

$dirroot = $_SERVER['DOCUMENT_ROOT'];
require_once($dirroot . '/common/bootstrap.php');

//判断session是否存在
if (empty($_SESSION['user_name']) || $_SESSION['user_name'] != '350game') {
    header('Location:http://lucky.350.com');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>头像抽奖</title>
    <link rel="stylesheet" href="assert/css/style.css">
</head>
<body>
<div class='luck-back'>
    <div class="luck-content ce-pack-end">
        <div id="luckuser" class="slotMachine">
            <div class="slot">
                <!--<span class="name">姓名</span>-->
            </div>
        </div>
        <div class="luck-content-btn">
            <a id="start" class="start" onclick="start()">开始抽奖</a>
        </div>
        <div class="luck-user">
            <div class="luck-user-title">
                <span>中奖名单</span>
            </div>
            <ul class="luck-user-list"></ul>
            <div class="luck-user-btn">
                <!--<a class="reset-prize-btn" href="javascript:;">重置获奖名单</a>-->
            </div>
        </div>
    </div>
</div>
<script src="assert/js/jquery-2.2.1.min.js" type="text/javascript"></script>
<script src="assert/js/user_arr.js" type="text/javascript"></script>
<script src="assert/js/Luckdraw.js" type="text/javascript"></script>
<!--<script>
    $(function () {
        $('.reset-prize-btn').bind('click', function () {
            $('.luck-user-list').html('');
            nametxt.css('background-image','url('+xinm[0]+')');
            phonetxt.html(phone[0]);
        })
    })
</script>-->
<script type="text/javascript">
        //监控键盘输入
         $(document).keydown(function(event){
            if( event.keyCode == 38 ){
                $('#start').trigger('click');
            }
             if ((window.event.altKey)&&(window.event.keyCode==115))
             {
                 window.showModelessDialog("about:blank","","dialogWidth:1px;dialogheight:1px");
                 return false;
             }
         });
    </script>
</body>
</html>
