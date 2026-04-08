<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Vite Server
    |--------------------------------------------------------------------------
    |
    | This is the default Vite server URL. You can adjust this based on your
    | development environment. This value is used when generating the Vite
    | tags for your assets.
    |
    */

    'dev_server_url' => env('VITE_DEV_SERVER_URL', 'https://localhost:5173'),

    /*
    |--------------------------------------------------------------------------
    | Build Directory
    |--------------------------------------------------------------------------
    |
    | This is the directory where Vite will place your built assets.
    |
    */

    'build_directory' => env('VITE_BUILD_DIR', 'public/build'),

    /*
    |--------------------------------------------------------------------------
    | Hot File
    |--------------------------------------------------------------------------
    |
    | This is the path to the Vite hot file. This file is used to
    | determine if the Vite server is running.
    |
    */

    'hot_file' => env('VITE_HOT_FILE', 'public/hot'),
];
