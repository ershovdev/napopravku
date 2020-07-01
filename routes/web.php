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

// Public block
Route::get('/files/{public_url}', 'FileController@showPublic')->name('files.public.show');
Route::get('/files/{public_url}/download', 'FileController@downloadPublic')->name('files.public.download');
Route::get('/host/file/public/{file}', 'FileController@hostPublicFile')->name('files.public.host');
Route::get('/host/file/public/word/{file}', 'FileController@hostPublicWordFile')
    ->name('files.public.word.host');


Route::middleware(['auth'])->group(function() {
    Route::get('/my-drive', 'FolderController@root')->name('folders.root.show');

    Route::get('/my-drive/folders/{folder}', 'FolderController@show')->name('folders.show');
    Route::get('/my-drive/files/{file}', 'FileController@show')->name('files.show');

    Route::get('/host/file/{file}', 'FileController@hostPrivateFile')->name('files.host');
    Route::get('/host/file/word/{file}', 'FileController@hostPrivateWordFile')->name('files.word.host');

    Route::post('/my-drive/folders', 'FolderController@store')->name('folders.store');
    Route::post('/my-drive/files', 'FileController@store')->name('files.store');

    Route::get('/my-drive/files/{file}/download', 'FileController@download')->name('files.download');
    Route::post('/my-drive/files/{file}/switchPublic', 'FileController@switchPublic')
        ->name('files.switchPublic');
    Route::post('/my-drive/files/{file}/rename', 'FileController@rename')->name('files.rename');
    Route::post('/my-drive/files/{file}/delete', 'FileController@delete')->name('files.delete');
});

