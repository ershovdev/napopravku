<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'WelcomeController@index')->name('welcome.index');

Auth::routes();

Route::middleware(['auth'])->group(function() {
    Route::get('/my-drive', 'FolderController@root')->name('folders.root.show');

    Route::get('/my-drive/folders/{folder}', 'FolderController@show')->name('folders.show');
    Route::get('/my-drive/files/{file}', 'FileController@show')->name('files.show');

    Route::get('/host/file/{file}', 'FileController@hostFile')->name('files.host');

    Route::post('/my-drive/folders', 'FolderController@store')->name('folders.store');
    Route::post('/my-drive/files', 'FileController@store')->name('files.store');

    Route::get('/my-drive/files/{file}/download', 'FileController@download')->name('files.download');
    Route::post('/my-drive/files/{file}/makePublic', 'FileController@makePublic')->name('files.makePublic');
    Route::post('/my-drive/files/{file}/rename', 'FileController@rename')->name('files.rename');
    Route::post('/my-drive/files/{file}/delete', 'FileController@delete')->name('files.delete');
});

