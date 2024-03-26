<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', 'AuthController@login')->name('login');
Route::post('/login', 'AuthController@doLogin');
Route::get('/register', 'AuthController@register')->name('register');
Route::post('/register', 'AuthController@doRegister');
