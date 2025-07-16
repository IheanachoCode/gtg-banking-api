<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-login', function () {
    return view('test-login');
});

Route::get('/test-web', function () {
    return 'Web route is working!';
});

Route::get('/api-key-guide', function () {
    return view('api-key-guide');
});
