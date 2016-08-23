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

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');
Route::get('/about', 'HomeController@about_us' );
//Route::resource('user', 'UserController');
Route::get('/change/password', 'UserController@change_password');
Route::post('/change/password/save', 'UserController@change_password_save');
Route::get('/user/list', 'UserController@index');
Route::any('/user/add', 'UserController@add_user');
Route::any('/user/edit/{id}', 'UserController@edit_user');
Route::any('/user/delete/{id}', 'UserController@delete_user');

