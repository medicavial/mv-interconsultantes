<?php
/***** Rutas del API *****/
/***** Samuel Ramírez - Octubre 2017 *****/

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
    Route::post('digitales', 'BusquedasController@buscaDigitalizados');
    Route::post('nota-soap', 'RegDatosController@saveNotaSoap');
    Route::get('listadosoap-{folio}', 'BusquedasController@notaSoapXpaciente');
});
