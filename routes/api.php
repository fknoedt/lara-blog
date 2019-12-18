<?php

use Illuminate\Http\Request;

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

// authenticated routes
Route::middleware('auth:api')->namespace('Api')->group(function () {

    // retrieve authenticated user information
    Route::get('/user', function(Request $request) {
        return $request->user();
    });

    // Categories CRUD routes
    Route::resource('categories', 'CategoryController')
        ->only([
            'index', 'show', 'store', 'update', 'destroy'
        ]);

    // Posts CRUD routes
    Route::resource('posts', 'PostController')
        ->only([
            'index', 'show', 'store', 'update', 'destroy'
        ]);

});

// request base path not found
Route::fallback(function(){
    return response()->json(['message' => 'Invalid Path'], 404);
});

// non-authenticated routes
// Route::get('/{any}', 'SpaController@index')->where('any', '.*');
