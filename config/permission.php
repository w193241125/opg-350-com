<?php

return [

    'models' => [

        /*
         * 当在这个包中使用 "HasRoles" trait 时, 我们需要知道哪个
         * Eloquent 模型用于检索你的权限。 当然,
         * 它通常仅仅是 "Permission" 模型，但你也可以使用其他你喜欢的模型。
         *
         * 你想用来作为权限控制模型的模型需要实现 `Spatie\Permission\Contracts\Permission` 契约
         *
         */

        'permission' => Spatie\Permission\Models\Permission::class,

        /*
         * 当在这个包中使用 "HasRoles" trait 时, 我们需要知道哪个
         * Eloquent 模型用于检索你的权限。 当然,
         * 它通常仅仅是 "Role" 模型，但你也可以使用其他你喜欢的模型。
         *
         *  你想用来作为角色模型的模型需要实现
         * `Spatie\Permission\Contracts\Role` 契约。
         */

        'role' => Spatie\Permission\Models\Role::class,

    ],

    'table_names' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'roles' => 'roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your permissions. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'permissions' => 'permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_permissions' => 'model_has_permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models roles. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_roles' => 'model_has_roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'role_has_permissions' => 'role_has_permissions',
    ],

    /*
     * 所有权限将会被缓存 24 小时，在一项权限或一个角色被更新时，
     * 他们才会被立即刷新。
     */

    'cache_expiration_time' => 60 * 24,

    /*
     * 当设置 true 时, 所需的 permission/role 名称将会被添加到异常消息中，
     * 在一些上下文中，这可能会是一个消息漏洞, 所以
     * 为保险起见，这里设置默认值为 False 。
     */

    'display_permission_in_exception' => false,
];
