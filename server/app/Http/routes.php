<?php
/***** Rutas del API *****/
/***** Samuel Ramírez - Octubre 2017 *****/

Route::get('/', 'WelcomeController@index');
Route::get('/inicio', 'BusquedasController@index');
Route::post('/login', 'SesionController@buscaUsuario');
Route::post('/busca', 'BusquedasController@buscaPaciente');
Route::get('/correo', 'BusquedasController@pruebaCorreos');


Route::group(['prefix' => 'paciente'], function()
{
    // Route::get('prueba', function()
    // {
    //     // Matches The "/paciente/prueba" URL
    //     return "funciona";
    // });
    Route::post('guardaAsignacion', 'RegDatosController@guardaAsignacion');
    Route::post('digitales', 'BusquedasController@buscaDigitalizados');
    Route::post('nota-soap', 'RegDatosController@saveNotaSoap');
    Route::get('listadosoap-{folio}', 'BusquedasController@notaSoapXpaciente');
});

Route::group(['prefix' => 'busquedas'], function()
{
    Route::get('listadoUnidades', 'BusquedasController@buscaUnidades');
    Route::get('listadoMedicos', 'BusquedasController@getMedicos');
    Route::get('listadoUsuarios', 'BusquedasController@getUsuarios');
    Route::get('asignaciones-{usrLogin}', 'BusquedasController@getAsignaciones');
});

Route::group(['prefix' => 'administracion'], function()
{
    Route::post('nuevoUsuario', 'RegDatosController@nuevoUsuario');
});
