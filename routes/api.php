<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authenticate users
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password', 'User\SettingsController@updatePassword');

    Route::post('designs', 'Designs\UploadController@upload');
    Route::get('designs/{id}', 'Designs\DesignController@show');
    Route::put('designs/{id}', 'Designs\DesignController@update');
    Route::delete('designs/{id}', 'Designs\DesignController@destroy');

    Route::post('designs/{id}/comments', 'Designs\CommentController@store');
    Route::put('comments/{id}', 'Designs\CommentController@update');
    Route::delete('comments/{id}', 'Designs\CommentController@destroy');

    Route::post('designs/{id}/like', 'Designs\DesignController@like');
    Route::get('designs/{id}/liked', 'Designs\DesignController@checkIfUserHasLiked');
    Route::post('designs/{id}/unlike', 'Designs\DesignController@unlike');

    Route::get('teams', 'Teams\TeamController@index');
    Route::post('teams', 'Teams\TeamController@store');
    Route::get('teams/{id}', 'Teams\TeamController@show');
    Route::get('users/teams', 'Teams\TeamController@fetchUserTeams');
    Route::put('teams/{id}', 'Teams\TeamController@update');
    Route::delete('teams/{id}', 'Teams\TeamController@destroy');
    Route::delete('teams/{id}/users/{userId}', 'Teams\TeamController@removeFromTeam');

    Route::post('invitations/{teamId}', 'Teams\InvitationController@invite');
    Route::post('invitations/{id}/resend', 'Teams\InvitationController@resend');
    Route::post('invitations/{id}/respond', 'Teams\InvitationController@respond');
    Route::delete('invitations/{id}', 'Teams\InvitationController@destroy');

    Route::post('chats', 'Chats\ChatController@sendMessage');
    Route::get('chats', 'Chats\ChatController@getUserChats');
    Route::get('chats/{id}/messages', 'Chats\ChatController@getChatMessages');
    Route::put('chats/{id}/markAsRead', 'Chats\ChatController@markAsRead');
    Route::delete('messages/{id}', 'Chats\ChatController@destroyMessage');
});

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

Route::get('designs', 'Designs\DesignController@index');
Route::get('designs/{id}', 'Designs\DesignController@show');
Route::get('designs/slug/{slug}', 'Designs\DesignController@findBySlug');
Route::get('designs/{id}/byUser', 'Designs\DesignController@userOwnsDesign');

Route::get('teams/slug/{slug}', 'Teams\TeamController@findBySlug');
Route::get('teams/{id}/designs', 'Designs\DesignController@getForTeam');

Route::get('users', 'User\UserController@index');
Route::get('users/{username}', 'User\UserController@findByUsername');
Route::get('users/{id}/designs', 'Designs\DesignController@getForUser');

Route::get('search/designs', 'Designs\DesignController@search');
Route::get('search/designers', 'User\UserController@search');


/*
|--------------------------------------------------------------------------
| Guests
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'guest:api'], function () {
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
});
