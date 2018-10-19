##注意
- 本项目权限使用 `spatie/Laravel-permission` 扩展。

##菜单
1. 项目开始从 
    `IndexController --> AdminServiceProvider.php --> 
     boot 方法 --> SidebarMenuComposer --> _menu.blade.php
     --> MenuPresenter`;
1.  uri 字段只能是英文，用于控制菜单显示权限，
    每次添加菜单都会添加一个对应的权限。
    在遍历菜单时会根据用户的权限来显示菜单。

##权限

每次直接修改数据库权限信息，需要执行：
`php artisan cache:forget spatie.permission.cache`



