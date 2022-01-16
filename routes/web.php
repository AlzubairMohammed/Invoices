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

// Route::get('/', function () {
//     return view('index');
// });


Auth::routes();

Route::get('/invoicesPaid', 'InvoicesController@invoicesPaid');

Route::get('/invoicesUnPaid', 'InvoicesController@invoicesUnPaid');

Route::get('/invoicesPartial', 'InvoicesController@invoicesPartial');

Route::get('/', 'HomeController@index')->name('home');

Route::resource('invoices','InvoicesController');

Route::resource('archive','InvoicesArchiveController');

Route::resource('sections','SectionsController');

Route::resource('products','ProductsController');

Route::get('/edit_invoice/{id}', 'InvoicesController@edit');

Route::get('/section/{id}', 'InvoicesController@getproducts');

Route::get('/status_show/{id}', 'InvoicesController@show')->name('status_show');

Route::get('/{page}', 'AdminController@index')->middleware('auth');

Route::post('/status_update/{id}', 'InvoicesController@status_update')->name('status_update');



