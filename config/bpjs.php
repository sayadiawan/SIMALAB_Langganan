<?php

// return [
//     'cons_id' => env('BPJS_CONS_ID'), // consumer ID dari BPJS Kesehatan
//     'secret_key' => env('BPJS_SECRET_KEY'), // consumer secret key
//     'username' => env('BPJS_USERNAME'), // username pcare
//     'password' => env('BPJS_PASSWORD'), // password pcare
//     'user_key' => env('BPJS_USER_KEY'), // user_key untuk akses webservice
//     'app_code' => env('BPJS_APP_CODE', '0149L005'), // kode aplikasi
//     'base_url' => env('BPJS_BASE_URL', 'https://apijkn-dev.bpjs-kesehatan.go.id'), // base url aplikasi
// ];

return [
    'base_url'      => env('BPJS_PCARE_BASE_URL', 'https://new-api.bpjs-kesehatan.go.id'),
    'service'       => env('BPJS_PCARE_SERVICE', ''),
    'cons_id'       => env('BPJS_PCARE_CONS_ID'),
    'secret_key'    => env('BPJS_PCARE_SECRET_KEY'),
    'user_key'      => env('BPJS_PCARE_USER_KEY'),
    'pcare_user'    => env('BPJS_PCARE_USER'),
    'pcare_pass'    => env('BPJS_PCARE_PASS'),
    'kd_aplikasi'   => env('BPJS_PCARE_KD_APLIKASI'),
    // header names bisa beda antar versi/dokumen; gunakan ini kalau perlu ganti cepat
    'headers' => [
        'cons_id'      => 'X-cons-id',
        'timestamp'    => 'X-timestamp',
        'signature'    => 'X-signature',
        'user_key'     => 'X-User-Key',   // beberapa implementasi: 'user_key'
        'authorization'=> 'Authorization' // beberapa implementasi: 'X-Authorization'
    ],
];