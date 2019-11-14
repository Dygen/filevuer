<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Filevuer Connections
    |--------------------------------------------------------------------------
    |
    */
    'connections' => [

        'FTP' => [
            [
                'name'     => env('FV_FTP_CONNECTION_NAME_1'),
                'host'     => env('FV_FTP_HOST_1'),
                'username' => env('FV_FTP_USERNAME_1'),
                'password' => env('FV_FTP_PASSWORD_1'),
                'port'     => env('FV_FTP_PORT_1', 21),
                'home_dir' => env('FV_FTP_HOME_DIR_1', null),
            ],
            [
                'name'     => env('FV_FTP_CONNECTION_NAME_2'),
                'host'     => env('FV_FTP_HOST_2'),
                'username' => env('FV_FTP_USERNAME_2'),
                'password' => env('FV_FTP_PASSWORD_2'),
                'port'     => env('FV_FTP_PORT_2', 21),
                'home_dir' => env('FV_FTP_HOME_DIR_2', null),
            ],
            [
                'name'     => env('FV_FTP_CONNECTION_NAME_3'),
                'host'     => env('FV_FTP_HOST_3'),
                'username' => env('FV_FTP_USERNAME_3'),
                'password' => env('FV_FTP_PASSWORD_3'),
                'port'     => env('FV_FTP_PORT_3', 21),
                'home_dir' => env('FV_FTP_HOME_DIR_3', null),
            ],
        ],

        'S3' => [
            [
                'name'     => env('FV_AWS_S3_CONNECTION_NAME_1'),
                'key'      => env('FV_AWS_S3_KEY_1'),
                'secret'   => env('FV_AWS_S3_SECRET_1'),
                'bucket'   => env('FV_AWS_S3_BUCKET_1'),
                'region'   => env('FV_AWS_S3_REGION_1', 'us-east-1'),
                'home_dir' => env('FV_AWS_S3_HOME_DIR_1', null),
            ],
            [
                'name'     => env('FV_AWS_S3_CONNECTION_NAME_2'),
                'key'      => env('FV_AWS_S3_KEY_2'),
                'secret'   => env('FV_AWS_S3_SECRET_2'),
                'bucket'   => env('FV_AWS_S3_BUCKET_2'),
                'region'   => env('FV_AWS_S3_REGION_2', 'us-east-1'),
                'home_dir' => env('FV_AWS_S3_HOME_DIR_2', null),
            ],
            [
                'name'     => env('FV_AWS_S3_CONNECTION_NAME_3'),
                'key'      => env('FV_AWS_S3_KEY_3'),
                'secret'   => env('FV_AWS_S3_SECRET_3'),
                'bucket'   => env('FV_AWS_S3_BUCKET_3'),
                'region'   => env('FV_AWS_S3_REGION_3', 'us-east-1'),
                'home_dir' => env('FV_AWS_S3_HOME_DIR_3', null),
            ],
        ]
    ]
];
