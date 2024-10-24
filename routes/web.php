<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::view('eitaa', 'eitaa');

Route::get('/re', function () {

    dd([
        'headers' => request()->header(),
        'body' => request()->all(),
        'ip' => request()->ip(),
        'method' => request()->method(),
        'url' => request()->url(),
        'user_agent' => request()->userAgent(),
    ]);

    return view('eitaa');
});
