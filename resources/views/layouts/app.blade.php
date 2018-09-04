<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','综合后台')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="/AdminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/AdminLTE/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/AdminLTE/bower_components/Ionicons/css/ionicons.min.css">
    {{--<!-- jvectormap -->--}}
    {{--<link rel="stylesheet" href="/AdminLTE/bower_components/jvectormap/jquery-jvectormap.css">--}}
    <!-- Theme style -->
    <link rel="stylesheet" href="/AdminLTE/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/AdminLTE/dist/css/skins/_all-skins.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery 3 -->
    <script src="/AdminLTE/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Google Font -->
    {{--<link rel="stylesheet"--}}
          {{--href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">--}}
    @yield('styles')
</head>
<body class="fixed hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    @include('layouts._header')
    @include('layouts._menu')

    @yield('contents')

    @include('layouts._footer')
    @include('layouts._tip')
    <div class="control-sidebar-bg"></div>
</div>

<!-- Bootstrap 3.3.7 -->
<script src="/AdminLTE/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- FastClick 触摸设备快速点击体验-->
<script src="/AdminLTE/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="/AdminLTE/dist/js/adminlte.min.js"></script>
<!-- Sparkline 信息体积小和数据密度高的图表-->
<script src="/AdminLTE/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
{{--<!-- jvectormap  -->--}}
{{--<script src="/AdminLTE/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>--}}
{{--<script src="/AdminLTE/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>--}}
<!-- SlimScroll 菜单和页面中的滚动条样式-->
<script src="/AdminLTE/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS 图表JS-->
<script src="/AdminLTE/bower_components/chart.js/Chart.js"></script>

{{--<!-- AdminLTE 仪表盘 demo (This is only for demo purposes) -->--}}
{{--<script src="/AdminLTE/dist/js/pages/dashboard2.js"></script>--}}
<!-- AdminLTE for demo purposes -->

{{--这个控制主题改变--}}
<script src="/AdminLTE/dist/js/demo.js"></script>


@yield('script')

</body>
</html>
