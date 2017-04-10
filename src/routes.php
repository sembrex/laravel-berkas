<?php

use Illuminate\Support\Facades\Route;

Route::get('berkas/partialindex', ['as' => 'berkas.partialindex', 'uses' => 'Karogis\Berkas\BerkasController@partialindex']);
Route::resource('berkas', 'Karogis\Berkas\BerkasController');
