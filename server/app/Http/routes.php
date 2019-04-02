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
  Route::get('listadoMedicos/{usuario?}', 'ExternosController@listadoMedicos');

  Route::post('generapase', 'ExternosController@generaPase');
  Route::get('listadopases-{idUsuario}', 'ExternosController@listadoPases');
  Route::get('buscaPase-{idPase}', 'ExternosController@buscaPase');
  Route::post('pasesXnombre', 'ExternosController@buscaPaseXnombre');
  Route::post('pasesXfolio', 'ExternosController@buscaPaseXfolioMV');
  Route::post('actualizaEstado', 'ExternosController@actualizaEstatus');

  Route::get('creapdf-{idPase}', 'ExternosController@creapdf');
  Route::get('verpdf-{idPase}', 'ExternosController@verpdf');

  Route::get('infosesiones/{folio}', 'ExternosController@datosSesionesRh');

  Route::post('guarda-usuario', 'ExternosController@guardaUsuario');
  Route::get('verifica-{username}', 'ExternosController@verificaUsername');
});

Route::group(['prefix' => 'reportes'], function()
{
  Route::get('prueba', 'ReportesController@index');
  Route::get('datos', 'ReportesController@repDatos');
  Route::get('template', 'ReportesController@repTemplate');
  Route::get('test', 'ReportesController@connectionTest');
  // Route::post('login', 'SesionController@sesionExternos');
});

Route::group(['prefix' => 'qws'], function()
{
  Route::get('prueba', 'QWSController@index');
});


Route::group(['prefix' => 'tienda'], function()
{
  Route::get('test', 'TiendaController@index');
  Route::post('venta', 'TiendaController@alertaVenta');
  Route::post('vista-venta', 'TiendaController@vistaVenta');
  Route::post('pdf-venta', 'TiendaController@pdfVenta');
});


Route::group(['prefix' => 'directorios'], function()
{
    Route::get('test', function(){
      return 'It works!';
    });

    Route::group(['prefix' => 'catalogos'], function(){
        Route::get('tipo-clave-unidad/{usuario?}', 'DirectoriosController@tipoClaveUnidad');
        Route::get('tipo-contribuyente/{usuario?}', 'DirectoriosController@tipoContribuyente');
        Route::get('tipo-creacion-usuario/{usuario?}', 'DirectoriosController@tipoCreacionUsuario');
        Route::get('tipo-imagen/{usuario?}', 'DirectoriosController@tipoImagen');
        Route::get('tipo-paquete/{usuario?}', 'DirectoriosController@tipoPaquete');
        Route::get('tipo-unidad/{usuario?}', 'DirectoriosController@tipoUnidad');
    });

    Route::group(['prefix' => 'busquedas'], function(){
        Route::get('unidades/{unidad?}/{usuario?}', 'DirectoriosController@unidades');
    });

    Route::group(['prefix' => 'operacion'], function(){
      Route::get('test', function(){
        return 'OPERACION works!';
      });
    });
});