<?php

namespace Jwhulette\Filevuer;

use Illuminate\Support\Facades\Route;

class Filevuer
{
    public static function routes()
    {
        $namespace = '\\jwhulette\\filevuer\\controllers\\';
        
        Route::group([
            'prefix' => 'filevuer',
            'middleware' => [
                'web',
                'sessionDriver'
            ],
            ], function () use ($namespace) {
                Route::get('/', $namespace . 'FilevuerController@index')->name('filevuer.index');

                Route::get('/poll', $namespace . 'FilevuerController@poll')->name('filevuer.poll');

                Route::get('/logout', $namespace . 'FilevuerController@logout')->name('filevuer.logout');

                Route::post('/', $namespace . 'FilevuerController@connect')->name('filevuer.hash');
        
                Route::post('/download', $namespace . 'DownloadController@generate')->name('filevuer.generate');

                Route::get('/download/{hash}', $namespace . 'DownloadController@download')->name('filevuer.download');

                Route::post('/upload', $namespace . 'UploadController@upload')->name('filevuer.upload');
        
                Route::group([
                'prefix' => 'file',
                'as' => 'filevuer.file'
                ], function () use ($namespace) {
                    Route::get('/', $namespace . 'FileController@show');
                    
                    Route::post('/', $namespace . 'FileController@create');

                    Route::put('/', $namespace . 'FileController@update');

                    Route::delete('/', $namespace . 'FileController@destroy');
                });
        
                Route::group([
                'prefix' => 'directory',
                'as' => 'filevuer.directory'
                ], function () use ($namespace) {
                    Route::get('/', $namespace . 'DirectoryController@index');

                    Route::post('/', $namespace . 'DirectoryController@create');

                    Route::delete('/', $namespace . 'DirectoryController@destroy');
                });
            });
    }
}
