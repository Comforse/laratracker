<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware' => ['web']], function () {

    Route::get('/', ['middleware' => 'auth', function() {
        return view('index/index');
    }]);
    Route::get('/home', ['middleware' => 'auth', function() {
        return view('index/index');
    }]);
    Route::get('/welcome', ['middleware' => 'auth', function() {
        return view('index/index');
    }]);
    // Torrents
    Route::get('/torrents', ['middleware' => 'auth', 'uses' => 'Torrents\TorrentsController@index']);
    Route::get('/torrents/upload', ['middleware' => 'auth', 'uses' => 'Torrents\TorrentsController@upload']);
    Route::post('/torrents/uploadPost', ['middleware' => 'auth', 'uses' => 'Torrents\TorrentsController@uploadPost']);
    Route::get('/torrents/{id}', ['middleware' => 'auth', 'uses' => 'Torrents\TorrentsController@view']);
    Route::get('/torrents/download/{id}', ['middleware' => 'auth', 'uses' => 'Torrents\TorrentsController@download']);

    // Authentication routes...
    Route::get('auth/login', 'Auth\AuthController@getLogin');
    Route::post('auth/login', 'Auth\AuthController@postLogin');
    Route::get('auth/logout', 'Auth\AuthController@getLogout');

    // Registration routes...
    Route::get('auth/register', 'Auth\AuthController@getRegister');
    Route::post('auth/register', 'Auth\AuthController@postRegister');

    Route::get('/announce', 'Announce\AnnounceController@announce');
});