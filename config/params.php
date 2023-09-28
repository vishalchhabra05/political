<?php

return [
    'role_ids' => ['superadmin' => 1, "admin" => 2, 'subadmin' => 3, 'member' => 4],
    'role_names' => [1 => 'Superadmin', 2 => "Admin", 3 => 'Subadmin', 4 => 'Member'],
    'user_role_ids' => [4],
    'admin_user_role_ids' => [2,3], // admin & subadmin
    'admin_page_length' => 10,
    'site_title' => 'Political Party',
    'mail_username' => 'hireinstructor@qualitygb.com',
    //'website_url' => 'http://localhost:8000',
    'website_url' => 'https://political-html.sandboxdevelopment.in/',
    'super_admin_website_url' => 'http://localhost:8000/superadmin',
    'admin_website_url' => 'http://localhost:8000/admin',
    'default_country_id' => 1,
    'records_per_page' => 10,
    // 'distance_const' => '6371', // In KMs
    'distance_const' => '3959', // In miles
];

