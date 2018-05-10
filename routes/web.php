<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
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
    return redirect('/home');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/zakat-list', 'ZakatController@index')->name('zakat');
Route::get('/pelaksanaan-zakat', 'ZakatController@create')->name('zakat.createOther');
Route::get('/pelaksanaan-zakat/{id}', 'ZakatController@create')->name('zakat.create');
Route::get('nominal/{nominal}', 'ZakatController@getNominal');
Route::get('search/muzakki/{nama}', 'ZakatController@cariMuzakki');
Route::post('bayar-zakat', 'ZakatController@storeZakat')->name('zakat.store');
Route::get('konfirmasi/{id}', 'ZakatController@showInsertedZakat')->name('zakat.confirmation');
Route::get('list-transaksi/', 'ZakatController@getZakatData');
Route::get('edit-transaksi/{id}', 'ZakatController@editZakat')->name('zakat.edit');
Route::patch('update-transaksi/{transaksi}', 'ZakatController@updateZakat')->name('zakat.update');
Route::get('make-invoice/{id}', 'ZakatController@createPDF')->name('zakat.invoice');
Route::delete('zakat/delete/{id}','ZakatController@destroy')->name('zakat.destroy');

Route::get('list-pengguna/', 'UserController@getUserData');
Route::get('/users', 'UserController@index')->name('user');
Route::patch('aktivasi/{id}', 'UserController@activateUser')->name('user.activate');
Route::patch('deaktivasi/{id}', 'UserController@deactivateUser')->name('user.deactivate');
Route::get('/profil/edit', function () {
    return view('user.edit-profil');
})->name('profil.edit');
Route::patch('update-profil/{user}', 'UserController@updateProfil')->name('profil.update');
Route::get('/ganti-password', function () {
    return view('user.ganti-password');
})->name('password.change');
Route::patch('ganti-password/{id}', 'UserController@changePassword')->name('password.update');
Route::get('/role/{id}', 'UserController@editRole')->name('role.edit');
Route::patch('role-update/{id}', 'UserController@updateRole')->name('role.update');

Route::get('/jenis-zakat', function () {
    return view('zakat.jenis-zakat');
})->name('jeniszakat.change');
Route::post('jenis-zakat-store', 'ZakatController@storeJenis')->name('jeniszakat.store');