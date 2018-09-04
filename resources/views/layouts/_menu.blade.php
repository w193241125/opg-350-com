{{--服务注入--}}
@inject('MenuPresenter', 'App\Presenters\MenuPresenter')
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu  tree" data-widget="tree">
            <li class="header">菜单列表</li>
            {!!$MenuPresenter->sidebarMenuList($overall_menus)!!}
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
<script type="text/javascript">
    $(document).ready(function () {
        var path_array = window.location.pathname.split('/');
        var scheme_less_url = '//' + window.location.host + window.location.pathname;
        console.log(scheme_less_url);
        if (path_array[1] == 'dashboard') {
            scheme_less_url = window.location.protocol + '//' + window.location.host + '/' + path_array[1];
        } else {
            scheme_less_url = window.location.protocol + '//' + window.location.host + '/' + path_array[1] + '/' + path_array[2] + '/' + path_array[3];
        }
        $('ul.treeview-menu>li').find('a[href="' + scheme_less_url + '"]').closest('li').addClass('active');  //二级链接高亮
        $('ul.treeview-menu>li').find('a[href="' + scheme_less_url + '"]').closest('li.treeview').addClass('active');  //一级栏目[含二级链接]高亮
        $('.sidebar-menu>li').find('a[href="' + scheme_less_url + '"]').closest('li').addClass('active');  //一级栏目[不含二级链接]高亮
    });

    $('.sidebar-menu li:not(.treeview) > a').on('click', function(){
        var $parent = $(this).parent().addClass('active');
        $parent.siblings('.treeview.active').find('> a').trigger('click');
        $parent.siblings().removeClass('active').find('li').removeClass('active');
    });

    $(window).on('load', function(){
        $('.sidebar-menu a').each(function(){
            // console.log(this);
            var cur = window.location.href;
            var url = this.href;
            // console.log(cur.match(url));
            if(cur.match(url)){
                $(this).parent().addClass('active')
                    .closest('.treeview-menu').addClass('.menu-open')
                    .closest('.treeview').addClass('active');
            }
        });
    });
</script>

