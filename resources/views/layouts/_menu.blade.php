{{--服务注入--}}
@inject('MenuPresenter', 'App\Presenters\MenuPresenter')
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">菜单列表</li>
            {!!$MenuPresenter->sidebarMenuList($overall_menus)!!}

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-share"></i> <span>第一层</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> 第二层
                            <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
                        </a>
                        <ul class="treeview-menu" style="display: none;">
                            <li><a href="#"><i class="fa fa-circle-o"></i> 第三层</a></li>
                            
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
