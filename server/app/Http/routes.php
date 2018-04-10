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
    Route::post('agrega-item-receta', 'RegDatosController@agregaItemReceta');
    Route::get('receta-{folio}', 'BusquedasController@getRecetaAbierta');
    Route::post('guardaIndicacion', 'RegDatosController@guardaIndicacion');
    Route::get('recetas-{folio}', 'BusquedasController@getRecetasXfolio');
    Route::post('cierraAtencion', 'RegDatosController@cierraAtencion');
    Route::post('actualizaObservaciones', 'RegDatosController@actualizaObs');
    Route::get('asignacion-{folio}', 'BusquedasController@getAsignacion');
});

Route::group(['prefix' => 'busquedas'], function()
{
    Route::get('listadoUnidades', 'BusquedasController@buscaUnidades');
    Route::get('listadoMedicos', 'BusquedasController@getMedicos');
    Route::get('listadoUsuarios', 'BusquedasController@getUsuarios');
    Route::get('listadoPermisos', 'BusquedasController@getPermisos');
    Route::get('asignaciones-{usrLogin}', 'BusquedasController@getAsignaciones');
    Route::get('botiquin-{unidad}', 'BusquedasController@getItemsBotiquin');
    Route::get('indicaciones-{folio}', 'BusquedasController@getIndicacionesReceta');
});

Route::group(['prefix' => 'administracion'], function()
{
  Route::post('nuevoUsuario', 'RegDatosController@nuevoUsuario');
  Route::post('editaUsuario', 'RegDatosController@editaUsuario');
  Route::get('listadoAsignaciones', 'BusquedasController@getListadoAsignaciones');
  Route::post('editarAsignacion', 'RegDatosController@editaAsignacion');
  Route::post('eliminarAsignacion', 'RegDatosController@eliminaAsignacion');
  Route::get('correoAsignacion-{id}', 'RegDatosController@correoAsignacion');
});

Route::group(['prefix' => 'externos'], function()
{
  Route::post('login', 'SesionController@sesionExternos');
  Route::get('listadoMedicos', 'ExternosController@listadoMedicos');
  Route::post('generapase', 'ExternosController@generaPase');
  Route::get('listadopases-{idUsuario}', 'ExternosController@listadoPases');
  Route::get('creapdf', 'ExternosController@creapdf');
});
