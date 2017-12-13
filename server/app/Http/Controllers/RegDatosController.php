<?php namespace App\Http\Controllers;
/***** Controlador para el registro de datos en las bases de datos *****/
/***** Samuel Ramírez - Octubre 2017 *****/

use DB;
use Input;
use Response;
use Mail;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegDatosController extends Controller {

	public function saveNotaSoap()
	{
		$app 				= RegDatosController::limpiaCadena( Input::get('app') );
		$apx 				= RegDatosController::limpiaCadena( Input::get('apx') );
		$subjetivos = RegDatosController::limpiaCadena( Input::get('subjetivos') );
		$objetivos 	= RegDatosController::limpiaCadena( Input::get('objetivos') );
		$analisis 	= RegDatosController::limpiaCadena( Input::get('analisis') );
		$plan 			= RegDatosController::limpiaCadena( Input::get('plan') );
		$folio 			= Input::get('folioPaciente');
		$username 	= Input::get('username');
		$idRegistro = Input::get('idRegistro');
		$unidad			= Input::get('unidad');

		// return Input::all();

		try {
			DB::table('NotaSOAP')->insert([
			    'Exp_folio' 				=> $folio,
					'app_notSOAP' 			=> $app,
					'aqx_notSOAP' 			=> $apx,
					'sub_notSOAP' 			=> $subjetivos,
					'obj_notSOAP' 			=> $objetivos,
					'analisis_notSOAP' 	=> $analisis,
					'pronos_notSOAP' 		=> $plan,
					'Usu_login' 				=> $username,
					'Uni_clave' 				=> $unidad,
					'fecreg_notSOAP' 		=> DB::raw('now()'),
					'comentario_notSOAP'=> 'Sistema MV-Zima'
			]);
		} catch (Exception $e) {
			return $e;
		}

		return Response::json(array('respuesta' => 'Nota Creada Correctamente'));
	}

	public function guardaAsignacion()
	{
		$folio 					= Input::get('folio');
		$username 			= Input::get('username');
		$cveMedico 			= Input::get('cveMedico');
		$username				= Input::get('username');
		$loginMedico		= Input::get('loginmedico');
		$motivo					= RegDatosController::limpiaCadena( Input::get('motivo') );
		$observaciones	= RegDatosController::limpiaCadena( Input::get('observaciones') );

		try {
			$respuestaBase = DB::table('redQx_asignaciones')
													->insertGetId([
																			    'Exp_folio' 						=> $folio,
																					'USU_loginMedico' 			=> $loginMedico,
																					'ASI_motivo'						=> $motivo,
																					'ASI_observaciones'			=> $observaciones,
																					'USU_loginRegistro' 		=> $username,
																					'ASI_fechaRegistro' 		=> DB::raw('now()')
																				]);

			$correo = RegDatosController::correoAsignacion( $respuestaBase );

			$respuesta = array('base' 	=> $respuestaBase,
												 'correo' => $correo);

		} catch (Exception $e) {
			$respuesta = $e;
		}

		return Response::json( array('respuesta' => $respuesta) );
	}

	public function correoAsignacion( $idAsignacion ){
		$asignacion = DB::table('redQx_asignaciones')
										->select('redQx_asignaciones.*', 'Exp_completo', 'Uni_nombrecorto', 'USU_nombreCompleto', 'USU_id', 'USU_email')
										->join('Expediente', 'redQx_asignaciones.Exp_folio', '=', 'Expediente.Exp_folio')
										->leftJoin('Unidad', 'redQx_asignaciones.UNI_clave', '=', 'Unidad.Uni_clave')
										->join('redQx_usuarios', 'redQx_asignaciones.USU_loginMedico', '=', 'redQx_usuarios.USU_login')
										->where('ASI_id', '=', $idAsignacion)
										->first();

		$creador = DB::table('redQx_usuarios')
									->select('USU_login', 'USU_email', 'USU_nombreCompleto')
									->where('USU_login', $asignacion->USU_loginRegistro)
									->first();

		$datos = array( 'folio' 		=> $asignacion->Exp_folio,
										'paciente'	=> $asignacion->Exp_completo,
										'motivo' 		=> $asignacion->ASI_motivo,
										'obs' 			=> $asignacion->ASI_observaciones,
										'medico'		=> $asignacion->USU_nombreCompleto,
										'email' 		=> $asignacion->USU_email,
										'fecha' 		=> $asignacion->ASI_fechaRegistro,
										'admin' 		=> $creador->USU_nombreCompleto,
										'mailAdmin'	=> $creador->USU_email,
										'userAdmin'	=> $creador->USU_login,
									);

		Mail::send('emails.asignacion', $datos, function($message) use ($datos)
		{
			$message->from('sramirez@medicavial.com.mx', 'Médica Vial');
		  $message->to($datos['email'], $datos['medico'])->subject('Seguimiento a paciente');
			$message->cc($datos['mailAdmin']);
    	$message->bcc(array('samuel11rr@gmail.com'));
		});

    return Response::json(array('respuesta' => 'Correo enviado Correctamente'));
		// return $asignacion->ASI_id;
		// return view('emails.asignacion', $datos);
	}

	public function verificaUserlogin( $usrLogin )
	{
		$verificacion = DB::table('redQx_usuarios')
														->select('USU_login')
														->where('USU_login', $usrLogin)
														->get();

		return sizeof( $verificacion );
	}

	public function nuevoUsuario( )
	{
		$nombre 			= Input::get('nombre');
		$aPaterno 		= Input::get('aPaterno');
		$aMaterno 		= Input::get('aMaterno');
		$email 				= Input::get('email');
		$clavePermiso	= Input::get('rol');
		$creador			= Input::get('creador');
		$emailCreador	= Input::get('emailCreador');

		// if ( $rol == true ) {
		// 		$clavePermiso = 2;
		// } elseif ( $rol == false ){
		// 		$clavePermiso =3;
		// }

		//creacion del nombre de usuario
		$usrLogin = strtolower( substr ($nombre, 0, 1 ).$aPaterno.substr( $aMaterno, 0, 1 ) );
		$usrLogin = str_replace(' ','',$usrLogin);
		$usrLogin = str_replace('ñ','n',$usrLogin);
		$usrLogin = str_replace('á','a',$usrLogin);
		$usrLogin = str_replace('é','e',$usrLogin);
		$usrLogin = str_replace('í','i',$usrLogin);
		$usrLogin = str_replace('ó','o',$usrLogin);
		$usrLogin = str_replace('ú','u',$usrLogin);

		$verificacion = RegDatosController::verificaUserlogin( $usrLogin );

		$contador = 2;

		while ( $verificacion > 0 ) {
			$usrLogin = strtolower( substr ($nombre, 0, $contador ).$aPaterno.substr( $aMaterno, 0, 1 ) );
			$usrLogin = str_replace(' ','',$usrLogin);
			$usrLogin = str_replace('ñ','n',$usrLogin);
			$usrLogin = str_replace('á','a',$usrLogin);
			$usrLogin = str_replace('é','e',$usrLogin);
			$usrLogin = str_replace('í','i',$usrLogin);
			$usrLogin = str_replace('ó','o',$usrLogin);
			$usrLogin = str_replace('ú','u',$usrLogin);

			$verificacion = RegDatosController::verificaUserlogin( $usrLogin );

			if ( $contador == strlen( $nombre ) && $verificacion > 0 ) {
						$usrLogin = $usrLogin.$contador;
			}

			$contador++;
		}

		// return $usrLogin;

		//generamos la contraseña
		$mayuculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$caracteres = '#$%&0123456789abcdefghijklmnopqrstuvwxyz';
    $password = '';

    for ($i=0; $i<1; $i++) {
        $password .= $mayuculas[rand(0, strlen($mayuculas) - 1)];
    }

		for ($i=0; $i<5; $i++) {
        $password .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }

    $perfilUsuario = array(	'userLogin'			=> $usrLogin,
														'password' 			=> $password,
													 	'usuario'				=> strtoupper($nombre.' '.$aPaterno.' '.$aMaterno),
														'email'					=> $email,
														'rol' 					=> $clavePermiso,
														'creador'				=> $creador,
														'emailCreador' 	=> $emailCreador);

		try {
			$usrGenerado = DB::table('redQx_usuarios')
												->insert([
															    'USU_login' 						=> $perfilUsuario['userLogin'],
																	'USU_email' 						=> $perfilUsuario['email'],
																	'USU_nombreCompleto' 		=> $perfilUsuario['usuario'],
																	'USU_password' 					=> md5( $perfilUsuario['password'] ),
																	'PER_clave' 						=> $perfilUsuario['rol'],
																	'USU_fechaRegistro' 		=> DB::raw('now()')
																	]);
			$respuesta = array('usrGenerado' => $usrGenerado);
		} catch (Exception $e) {
			$respuesta = $e;
		}

		$perfilUsuario = array_merge($perfilUsuario, $respuesta);

		RegDatosController::correoCredenciales( $perfilUsuario );

		return $perfilUsuario;
	}

	public function correoCredenciales( $datos )
	{
		Mail::send('emails.credenciales', $datos, function($message) use ($datos)
		{
			$message->from('sramirez@medicavial.com.mx', 'Médica Vial');
		  $message->to($datos['email'], $datos['usuario'])->subject('Nombre de usuario y contraseña');
			$message->cc($datos['emailCreador']);
    	$message->bcc(array('samuel11rr@gmail.com'));
		});

    return Response::json(array('respuesta' => 'Correo enviado Correctamente'));

	}

	public function agregaItemReceta()
	{
		$folio					= Input::get('folio');
		$unidad					= Input::get('unidad');
		$usuario				= Input::get('usuario');
		$almacen				= Input::get('almacen');
		$cantidad				= Input::get('cantidad');
		$descripcion		= Input::get('descripcion');
		$idMedicamento	= Input::get('idMedicamento');
		$presentacion		= Input::get('presentacion');
		$existencia			= Input::get('existencia');
		$tipo_item			= Input::get('tipo_item');
		$posologia			= RegDatosController::limpiaCadena( Input::get('posologia') );

		$tipoReceta = 6;

		try {
			//primero realizamos la reserva del item
			$reserva = DB::connection('inventario')
										->table('reservas')
										->insertGetId([
																	'ITE_clave' 		=> $idMedicamento,
																	'ALM_clave' 		=> $almacen,
																	'RES_cantidad' 	=> $cantidad,
																	'RES_fecha' 		=> DB::raw('now()')
																	]);
			$respuesta = $reserva;

			if ($reserva > 0) {
				$recetasAbiertas = DB::table('RecetaMedica')
														->where('Exp_folio', '=', $folio)
														->where('tipo_receta', '=', $tipoReceta)
														->where('RM_terminada', '<>', 1)
														->count();

				if ($recetasAbiertas == 0) {
						//buscamos el id de la ultima receta para asignarle id a la nueva receta
						$idReceta = DB::table('RecetaMedica')
													->max('id_receta');
						$idReceta = $idReceta+1;

						$recetaXfolio = DB::table('RecetaMedica')
													->where('Exp_folio', '=', $folio)
													->where('tipo_receta', '=', $tipoReceta)
													->max('cont_receta');

						if ($recetaXfolio == null || $recetaXfolio == 0 ) {
								$recetaXfolio = 1;
						}

						DB::table('RecetaMedica')
							->insert([
												'id_receta' 	=> $idReceta,
												'Exp_folio' 	=> $folio,
												'RM_fecreg' 	=> DB::raw('now()'),
												'Usu_login' 	=> $usuario,
												'Uni_clave' 	=> $unidad,
												'tipo_receta' => $tipoReceta,
												'cont_receta' => $recetaXfolio,
												]);

						$idSuministro = DB::table('NotaSuministros')
															->max('NS_id');
						$idSuministro = $idSuministro+1;

						DB::table('NotaSuministros')
							->insert([
												'NS_id' 					=> $idSuministro,
												'id_receta' 			=> $idReceta,
												'NS_descripcion' 	=> $descripcion,
												'NS_cantidad' 		=> $cantidad,
												'NS_fecha' 				=> DB::raw('now()'),
												'NS_tipoDoc' 			=> $tipoReceta,
												'NS_posologia' 		=> $posologia,
												'NS_presentacion' => $presentacion,
												'NS_tipoItem' 		=> $tipo_item,
												'id_reserva' 			=> $reserva,
												'id_almacen' 			=> $almacen,
												'id_existencia' 	=> $existencia,
												'id_item' 				=> $idMedicamento,
												'cont_recetaTipo' => 1,
												]);
				} else{ // cuando SI hay recetas abiertas
					$recetasAbiertas = DB::table('RecetaMedica')
																->where('Exp_folio', '=', $folio)
																->where('tipo_receta', '=', $tipoReceta)
																->where('RM_terminada', '<>', 1)
																->count();

					$idReceta = DB::table('RecetaMedica')
													->where('Exp_folio', '=', $folio)
													->where('tipo_receta', '=', $tipoReceta)
													->where('cont_receta', '=', $recetasAbiertas)
													->where('RM_terminada', '<>', 1)
													->max('id_receta');

					$idSuministro = DB::table('NotaSuministros')
															->max('NS_id');
					$idSuministro = $idSuministro+1;

					DB::table('NotaSuministros')
						->insert([
											'NS_id' 					=> $idSuministro,
											'id_receta' 			=> $idReceta,
											'NS_descripcion' 	=> $descripcion,
											'NS_cantidad' 		=> $cantidad,
											'NS_fecha' 				=> DB::raw('now()'),
											'NS_tipoDoc' 			=> $tipoReceta,
											'NS_posologia' 		=> $posologia,
											'NS_presentacion' => $presentacion,
											'NS_tipoItem' 		=> $tipo_item,
											'id_reserva' 			=> $reserva,
											'id_almacen' 			=> $almacen,
											'id_existencia' 	=> $existencia,
											'id_item' 				=> $idMedicamento,
											'cont_recetaTipo' => 1,
											]);
				}

				DB::table('HistoriaReceta')
					->insert([
										'id_receta' 			=> $idReceta,
										'Usu_login' 			=> $usuario,
										'HRE_fecMov'			=> DB::raw('now()'),
										'HRE_mov' 				=> 1,
										'HRE_descripcion' => $descripcion,
										]);

				$respuesta = DB::table('NotaSuministros')
												->where('id_receta', '=', $idReceta)
												->get();
			} //termina if reserva

		} catch (Exception $e) {
			$respuesta = array('error' => $e, 'mensaje' => 'error al reservar el item');
		}

		return $respuesta;
	}

	public function guardaIndicacion()
	{
		$folio			= Input::get('folio');
		$tipoReceta	= Input::get('tipoReceta');
		$indicacion	= RegDatosController::limpiaCadena( Input::get('obs') );

		$idReceta = DB::table('RecetaMedica')
									->where('Exp_folio', $folio)
									->where('RM_terminada', '<>', 1)
									->where('tipo_receta', '=', $tipoReceta)
									->max('id_receta');

		$idRecetaExt = DB::table('recetaExterna')
											->where('Exp_folio', $folio)
											->where('RE_terminada', '<>', 1)
											->max('RE_idReceta');

		try {
			$respuesta = DB::table('NotaIndAlternativa')
											->insertGetId([
													'Exp_folio' 	=> $folio,
													'Nind_obs' 		=> $indicacion,
													'id_receta' 	=> $idReceta,
													'RE_idReceta'	=> $idRecetaExt
											]);
		} catch (Exception $e) {
			$respuesta = array('error' => $e, 'mensaje' => 'error al registrar');
		}

		return $respuesta;
	}

	public function editaUsuario()
	{
		$id				= Input::get('id');
		$permiso	= Input::get('permiso');
		$status		= Input::get('status');

		try {
			$respuesta = DB::table('redQx_usuarios')
											->where('USU_id', $id)
											->update(['USU_activo'	=> DB::raw($status),
																'PER_clave' 	=> $permiso
																]);
		} catch (Exception $e) {
			$respuesta = array('error' => $e, 'mensaje' => 'error al actualizar');
		}

		return $respuesta;
	}

	public function cierraAtencion()
	{
		$idAsignacion	= Input::get('idAsignacion');
		$folio				= Input::get('folio');
		$username			= Input::get('username');
		$unidad				= Input::get('unidad');

		try {
			$respuesta = DB::table('redQx_asignaciones')
											->where('ASI_id', $idAsignacion)
											->where('Exp_folio', $folio)
											->update(['UNI_clave' => $unidad,
																'ASI_fechaAtencion' => DB::raw('now()'),
																'ASI_terminada'	=> DB::raw(1),
																'USU_terminada' 	=> $username
																]);
		} catch (Exception $e) {
			$respuesta = array('error' => $e, 'mensaje' => 'error al actualizar');
		}

		return $respuesta;
	}

	public function actualizaObs()
	{
		$idAsignacion		= Input::get('idAsignacion');
		$observaciones 	= RegDatosController::limpiaCadena( Input::get('observaciones') );

		try {
			$respuesta = DB::table('redQx_asignaciones')
											->where('ASI_id', $idAsignacion)
											->update(['ASI_observaciones' => $observaciones]);
		} catch (Exception $e) {
			$respuesta = array('error' => $e, 'mensaje' => 'error al actualizar');
		}

		return $respuesta;
	}

	public function editaAsignacion()
	{
		$id				= Input::get('id');
		$medico		= Input::get('medico');
		$usuario 	= Input::get('usuario');
		$obs 			= RegDatosController::limpiaCadena( Input::get('obs') );
		$motivo 	= RegDatosController::limpiaCadena( Input::get('motivo') );

		try {
			$respuesta = DB::table('redQx_asignaciones')
											->where('ASI_id', $id)
											->update(['USU_loginMedico' 	=> $medico,
																'ASI_motivo' 				=> $motivo,
																'ASI_observaciones' => $obs,
																'USU_loginRegistro' => $usuario]);
		} catch (Exception $e) {
			$respuesta = array('error' => $e, 'mensaje' => 'error al actualizar');
		}

		return $respuesta;
	}

	public function eliminaAsignacion()
	{
		$id				= Input::get('id');
		$obs			= Input::get('obs');
		$usuario 	= Input::get('usuario');

		try {
			$respuesta = DB::table('redQx_asignaciones')
											->where('ASI_id', $id)
											->update(['ASI_observaciones' 	=> $obs,
																'USU_terminada' 			=> $usuario,
																'ASI_cancelada'				=> DB::raw(1),
																'ASI_fecha terminada'	=> DB::raw('now()')]);
		} catch (Exception $e) {
			$respuesta = array('error' => $e, 'mensaje' => 'error al actualizar');
		}

		return $respuesta;
	}

	public function limpiaCadena( $cadena )
	{
		$noPermitidos = array("'", '\\', '<', '>', "\"");
		$cadenaLimpia = str_replace($noPermitidos,"", $cadena);

		return $cadenaLimpia;
	}
}
