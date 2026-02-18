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
            'items' => 'c,r,u,d',
            'customers' => 'c,r,u,d',
            'suppliers' => 'c,r,u,d',
            'purchases' => 'c,r,u,d',
            'sales' => 'c,r,u,d',
            // 'invoices' => 'c,r,u,d',
            'stocks' => 'c,r,u,d',
            'accounts' => 'c,r,u,d',
            // 'banks' => 'c,r,u,d',
            'transactions' => 'c,r,u,d',
            'employees' => 'c,r,u,d',
            'expenses' => 'c,r,u,d',
            'payroll' => 'c,r,u,d',
        ],

        'Admin' => [],
        'employe' => [],
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];
