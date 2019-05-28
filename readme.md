##注意
- 本项目权限使用 `spatie/Laravel-permission` 扩展。
- 数据库文件（*.sql）都在 database/seeds 里面了懒得写数据填充了。

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



##数据查询条件语句

文档：https://laravel-china.org/docs/laravel/5.5/queries/1327
其中的 `条件语句` 中的 when 适用。