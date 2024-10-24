<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::view('/eitaa', 'eitaa');

Route::get('/re', function () {
    echo '<script src="https://developer.eitaa.com/eitaa-web-app.js"></script>';

    dd(
        [
            'eitaa-web-app' => true,
        ],
        [
            'headers' => array_map(fn($value) => Arr::join($value, ' '), request()->header()),
            'body' => request()->all(),
            'full_url' => request()->fullUrl(),
            'ip' => request()->ip(),
            'method' => request()->method(),
            'url' => request()->url(),
            'user_agent' => request()->userAgent(),
        ]
    );

    return view('eitaa');
});
