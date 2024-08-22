<?php

namespace App\Constants;

class Constants
{
    const ADMIN_ROLE = 'admin';
    const USER_ROLE = 'user';
    const TEACHER_ROLE = 'teacher';
    const MALE_GENDER = 'MALE';
    const FEMALE_GENDER = 'FEMALE';

    const SECTIONS_TYPES = [
        //todo add your info in the same way
        'sections' => [
            'attributes' => [
                'name',
                'image',
            ],
            'rules' => [
                'create' => [
                    'ar_name' => 'required',
                    'en_name' => 'required',
                    'image' => 'required|mimes:jpeg,png,jpg',
                ],
                'update' => [
                    'ar_name' => 'nullable',
                    'en_name' => 'nullable',
                    'image' => 'mimes:jpeg,png,jpg',
                ],
            ],
        ],
        'brands' => [
            'attributes' => [
                'name',
                'image',
            ],
            'rules' => [
                'create' => [
                    'ar_name' => 'required',
                    'en_name' => 'required',
                    'image' => 'required|mimes:jpeg,png,jpg',
                ],
                'update' => [
                    'ar_name' => 'nullable',
                    'en_name' => 'nullable',
                    'image' => 'mimes:jpeg,png,jpg',
                ],
            ],
        ],

    ];
    const ORDER_STATUSES = [
        'pending' => [
            'ar' => 'معلق',
            'en' => 'pending',
        ],
        'rejected' => [
            'ar' => 'مرفوض',
            'en' => 'rejected',
        ],
        'completed' => [
            'ar' => 'مكتمل',
            'en' => 'completed',
        ],
    ];
}
