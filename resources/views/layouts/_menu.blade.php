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
            {{$MenuPresenter->sidebarMenuList($overall_menus)}}

        @foreach($menus as $v)
            @if($v->pid == 1)
                    <li class="treeview">
                        <a href="/{{$v->permission_name}}">
                            <i class="fa fa-wrench"></i> <span>{{$v->name}}</span>
                            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu" style="display: none;">
                            <li class="treeview">
                                <a href="#"><i class="fa fa-circle-o"></i> Level One
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu" style="display: none;">
                                    <li class="treeview">
                                        <a href="#"><i class="fa fa-circle-o"></i> Level Two
                                            <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                                        </a>
                                        <ul class="treeview-menu" style="display: none;">
                                            <li><a href="#"><i class="fa fa-circle-o"></i> Level Three</a></li>
                                            <li><a href="#"><i class="fa fa-circle-o"></i> Level Three</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li><a href="#"><i class="fa fa-circle-o"></i> Level One</a></li>
                        </ul>
                    </li>
            @endif
        @endforeach
            <!-- Optionally, you can add icons to the links -->
            <li class="active"><a href="#"><i class="fa fa-link"></i> <span>Link</span></a></li>
            <li><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li>
            <li class="treeview">
                <a href="#"><i class="fa fa-link"></i> <span>Multilevel</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="#">Link in level 2</a></li>
                    <li><a href="#">Link in level 2</a></li>
                </ul>
            </li>
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
