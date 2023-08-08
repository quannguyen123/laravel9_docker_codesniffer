<?php
    return [
        'paginate' => 10,

        'page-limit' => [
            10,25,50,100,250,500
        ],

        'status' => [
            'active' => 1,
            'lock' => 2
        ],

        'status-payment' => [
            'paid' => 1,
            'unpaid' => 2,
            'pay_failed' => 3,
        ],

        'job-status' => [
            'draft' => 1,
            'public' => 2,
            'hidden' => 3,
            'about_to_expire' => 4,
            'expired' => 5,
            'virtual' => 7
        ],

        'service-type' => [
            'post-job' => 1,
            'find-cv' => 2,
        ],

        'user-type' => [
            'type-user' => 1,
            'type-partner' => 2,
        ],

        'user-role' => [
            'role-hr' => 3,
            'role-accountant' => 4,
        ],
    ];
?>