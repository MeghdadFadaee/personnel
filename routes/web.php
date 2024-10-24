<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

Route::view('/', 'welcome');
Route::view('/eitaa', 'eitaa');

Route::get('/re', function () {
    echo '<script src="https://developer.eitaa.com/eitaa-web-app.js"></script>';

    $data = 'auth_date=1729767550&query_id=10111140801903956&user={"id":4332838,"first_name":"محمد مقداد","last_name":"فدایی","language_code":"fa"}&hash=76dc3c6e7693c39ec0209780e1da82e1ddc65157a0321804af83620356a2236b';
    $data = 'user=%7B%22id%22%3A279058397%2C%22first_name%22%3A%22Vladislav%22%2C%22last_name%22%3A%22Kibenko%22%2C%22username%22%3A%22vdkfrost%22%2C%22language_code%22%3A%22en%22%2C%22is_premium%22%3Atrue%2C%22allows_write_to_pm%22%3Atrue%7D&chat_instance=-3788475317572404878&chat_type=private&auth_date=1709144340&hash=371697738012ebd26a111ace4aff23ee265596cd64026c8c3677956a85ca1827';

    parse_str($data, $request);
//    $request['user'] = json_decode($request['user'], true);

    ksort($request);

    $request = collect($request);


    $hash = $request->pull('hash');

    $WebAppData = 'WebAppData';
    $token = '5768337691:AAGDAe6rjxu1cUgxK4BizYi--Utc3J9v5AU';

    $hmacKey = hash_hmac('sha256', $WebAppData, $token);

    $dataString = $request
        ->map(fn($value, $key) => "$key=$value")
        ->implode("\n");

    $calculatedHash = hash_hmac('sha256', $dataString, $hmacKey);

    dd(
        [
            'Hash:' => $hash,
            'Calculated Hash:' => $calculatedHash,
            'Comparison Result:' => hash_equals($hash, $calculatedHash)
        ]
    );
    dd(
        hash_equals($hash, $calculatedHash),

    );

    return view('eitaa');
});
