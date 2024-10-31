<?php

return [
    'users' => [
        'valid_sort_columns' => ['id', 'username', 'name', 'guard_name', 'created_at'],
        'default_sort_column' => 'created_at',
        'default_sort_order' => 'desc',
    ],
    'roles' => [
        'valid_sort_columns' => ['id', 'name', 'guard_name', 'created_at'],
        'default_sort_column' => 'id',
        'default_sort_order' => 'asc',
    ],
    'permissions' => [
        'valid_sort_columns' => ['id', 'name', 'guard_name', 'created_at'],
        'default_sort_column' => 'created_at',
        'default_sort_order' => 'desc',
    ],
];
