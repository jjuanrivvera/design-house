<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('me', 'User\MeController@getMe');

/*
|--------------------------------------------------------------------------
| Authenticate users
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'api'], function () {
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password', 'User\SettingsController@updatePassword');
});


/*
|--------------------------------------------------------------------------
| Guests
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'guest:api'], function () {
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
});
