<?php

namespace App\Constants;

class Constants
{
    const ADMIN_ROLE = 'admin';
    const USER_ROLE = 'user';
    const SERVICE_PROVIDER_ROLE = 'service provider';
    const MALE_GENDER = 'MALE';
    const FEMALE_GENDER = 'FEMALE';

    // const ORDER_STATUSES = [
    //     'pending' => [
    //         'ar' => 'معلق',
    //         'en' => 'pending',
    //     ],
    //     'rejected' => [
    //         'ar' => 'مرفوض',
    //         'en' => 'rejected',
    //     ],
    //     'completed' => [
    //         'ar' => 'مكتمل',
    //         'en' => 'completed',
    //     ],
    // ];

    const ORDER_STATUSES = [
        'pending' ,
        'in_progress' ,
        'completed',
        'canceled'
    ];
    
}
