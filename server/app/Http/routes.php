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

// Route::get('/', 'WelcomeController@index');

// Route::get('home', 'HomeController@index');

// Route::controllers([
// 	'auth' => 'Auth\AuthController',
// 	'password' => 'Auth\PasswordController',
// ]);

Route::get('/', 'WelcomeController@index');
Route::get('/inicio', 'BusquedasController@index');
Route::post('/login', 'SesionController@buscaUsuario');
Route::post('/busca', 'BusquedasController@buscaPaciente');

Route::group(['prefix' => 'paciente'], function()
{
    // Route::get('prueba', function()
    // {
    //     // Matches The "/paciente/prueba" URL
    //     return "funciona";
    // });
    // Route::get('digitales-{folio}', 'BusquedasController@buscaDigitalizados');
    Route::post('digitales', 'BusquedasController@buscaDigitalizados');
});
