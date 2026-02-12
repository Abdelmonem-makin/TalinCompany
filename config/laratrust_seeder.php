<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'superadministrator' => [
            'users' => 'c,r,u,d',
            'Product' => 'c,r,u,d',
            'Order' => 'c,r,u,d',
            'Stock' => 'c,r,u,d',
            'supplier' => 'c,r,u,d',
            'customers' => 'c,r,u,d',
            'employe' => 'c,r,u,d',
            'debt' => 'c,r,u,d',
            'Expense' => 'c,r,u,d',
        ],
        'administrator' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u',
        ],
        'user' => [
            'profile' => 'r,u',
        ],
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];
