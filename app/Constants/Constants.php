<?php

namespace App\Constants;

class Constants
{
    const ADMIN_ROLE = 'admin';
    const USER_ROLE = 'user';
    const MALE_GENDER = 'MALE';
    const FEMALE_GENDER = 'FEMALE';

    const SECTIONS_TYPES = [
        'super' =>  [
            'attributes' => [
                'name',
                'image',
                'description',
            ],
            'rules' =>
            [
                'create' => [
                    'name' => 'required|string',
                    'image' => 'required|mimes:jpeg,png,jpg'
                ],
                'update' => [
                    'name' => 'string',
                    'image' => 'mimes:jpeg,png,jpg'
                ]
            ]

        ],

        'courses' => [
            'attributes' => [
                'name',
                'image',
                'is_free',
                'description',
            ],
            'rules' =>
            [
                'create' => [
                    'name' => 'required|string',
                    'image' => 'required|mimes:jpeg,png,jpg',
                    'is_free' => 'required|boolean',
                    'description' => 'required|string',
                ],
                'update' => [
                    'name' => 'string',
                    'image' => 'mimes:jpeg,png,jpg',
                    'is_free' => 'boolean',
                    'description' => 'string',
                ],
            ]

        ],

        'course_sections' => [
            'attributes' => [
                'name',
                'image',
                'description',
            ],
            'rules' =>
            [
                'create' => [
                    'name' => 'required|string',
                    'image' => 'required|mimes:jpeg,png,jpg'
                ],
                'update' => [
                    'name' => 'string',
                    'image' => 'mimes:jpeg,png,jpg'
                ]
            ]

        ],

    ];
}
