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
		$app 				= Input::get('app');
		$apx 				= Input::get('apx');
		$subjetivos = Input::get('subjetivos');
		$objetivos 	= Input::get('objetivos');
		$analisis 	= Input::get('analisis');
		$plan 			= Input::get('plan');
		$folio 			= Input::get('folioPaciente');
		$username 	= Input::get('username');
		$idRegistro = Input::get('idRegistro');

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
					'Uni_clave' 				=> 0,
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

		try {
			$respuesta = DB::table('redQx_asignaciones')
											->insert([
														    'Exp_folio' 						=> $folio,
																'USU_loginMedico' 			=> $loginMedico,
																'USU_loginRegistro' 		=> $username,
																'ASI_fechaRegistro' 		=> DB::raw('now()')
																]);
		} catch (Exception $e) {
			$respuesta = $e;
		}

		return Response::json( array('respuesta' => $respuesta) );
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
		$posologia			= Input::get('posologia');
		$presentacion		= Input::get('presentacion');
		$existencia			= Input::get('existencia');
		$tipo_item			= Input::get('tipo_item');

		$tipoReceta = 6;

		//limpiamos las Indicaciones (posologia)
		$noPermitidos = array("'", '\\', '<', '>', "\"");
		$posologia = str_replace($noPermitidos,"", $posologia);

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
		$indicacion	= Input::get('obs');
		$tipoReceta	= Input::get('tipoReceta');

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

}
