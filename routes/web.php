<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return redirect('blog');
});

// TODO: single definition
Route::get('/blog', 'SPAController@index');
Route::get('/post{any}', 'SPAController@index')->where('any', '.*');

Route::get('/categories', 'SPAController@index');
Route::get('/categories{any}', 'SPAController@index')->where('any', '.*');

// routes required by Authentication operations
Auth::routes();

Route::middleware('auth')->group(
    function () {
        Route::get('admin', function () {
            return redirect('admin/posts');
        });
        Route::get('admin/categories', 'AdminController@categories');
        Route::get('admin/posts', 'AdminController@posts');
    }
);

