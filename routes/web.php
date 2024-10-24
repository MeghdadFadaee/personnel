<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

Route::view('/', 'welcome');
Route::view('/eitaa', 'eitaa');

Route::get('/re', function () {
    echo '<script src="https://developer.eitaa.com/eitaa-web-app.js"></script>';

    return view('eitaa');
});
