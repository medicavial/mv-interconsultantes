<?php namespace App\Http\Controllers;
/***** Controlador para busquedas y operaciones de externos *****/
/***** Samuel Ramírez - Marzo 2018 *****/

use DB;
use View;
use Input;
use Mail;
use Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ExternosController extends Controller {
	public function listadoMedicos()
	{
		$listado = DB::connection('externos')
							 ->table('usuarios')
							 ->select('USU_id', 'USU_username', 'USU_nombre', 'USU_aPaterno', 'USU_aMaterno', 'PER_id', 'USU_email', 'USU_fechaAlta')
							 ->Where('USU_activo', 1)
							 ->where('USU_id', '>', 1 )
							 ->get();
		return $listado;
	}

	public function listadoPases( $idUsuario )
	{
		$listado = DB::connection('externos')
							 ->table('pases')
							 ->select('pases.*', DB::raw( 'CONCAT( PAS_nombre, " ", PAS_aPaterno, " ", PAS_aMaterno ) as nombreCompleto' ),
									  'EAT_nombre', 'EAT_alias', 'EAD_nombre', 'EAD_alias',
									  DB::raw( 'if( PAS_id < 10, concat(PAS_clave,"00", PAS_id), if( PAS_id < 100, concat(PAS_clave, "0", PAS_id), concat(PAS_clave, PAS_id) ) ) as claveOrden' ),
									  DB::raw( 'DATEDIFF( now(), PAS_fechaAlta ) as dias' ),
									  DB::raw( 'DATE_FORMAT(PAS_fechaAlta, "%d-%m-%Y") as fechaFacil' ),
										DB::raw( 'if( datediff( now(), PAS_fechaAlta)<5, "0 a 5 días", if( datediff( now(), PAS_fechaAlta) between 5 and 10, "5 a 10 días", if( datediff( now(), PAS_fechaAlta) between 10 and 30, "10 a 30 días", if( datediff( now(), PAS_fechaAlta) between 30 and 90, "30 a 90 días", if( datediff( now(), PAS_fechaAlta) > 90, "mas de 90 días", "N/A" ) ) ) ) ) as estatusDias' ),
										DB::raw( '( select max( Rehab_cons ) from medica_registromv.Rehabilitacion
																where medica_registromv.Rehabilitacion.Exp_folio = pases.Exp_folio
															) as cantidad_rehab' ),
										'InformeRehabilitacion.infRehab_id'
									)
							 ->leftJoin('estatusAdministrativo', 'pases.EAD_id', '=', 'estatusAdministrativo.EAD_id')
							 ->leftJoin('estatusAtencion', 'pases.EAT_id', '=', 'estatusAtencion.EAT_id')
							 ->leftJoin('medica_registromv.InformeRehabilitacion', 'pases.Exp_folio', '=', 'InformeRehabilitacion.Exp_folio')
							 ->orderBy('PAS_fechaAlta', 'desc');
							 if ( $idUsuario > 1 && $idUsuario != 13 ) {
							 	$listado->where('USU_id', $idUsuario);
								$listado->where('PAS_fechaMod', '>', DB::raw('DATE(DATE_SUB(NOW(), INTERVAL 2 MONTH))'));
								$listado->limit(100);
							} elseif ( $idUsuario == 1 || $idUsuario == 13 ) {
								$listado->limit(1500);
							}
		return $listado->get();
	}

	private function verificaUsuario( $usuario ){
		return DB::connection('externos')
						 ->table('usuarios')
						 ->where('USU_username', $usuario)
						 ->limit(1)
						 ->get();
	}

	public function buscaPase( $id ){
		$pre = strtoupper( substr( $id, 0, 2 ) );
		$num = intval( substr( $id, 2 ), 10 );

		// return array('pre' => $pre, 'num' => $num);

		return DB::connection('externos')
						 ->table('pases')
						 ->select('medica_externos.pases.*', 'Unidad.*', 'USU_nombre', 'USU_aPaterno', 'USU_aMaterno', 'USU_username', 'CIA_claveMV', 'medicos.*',
						 					DB::raw( 'if( PAS_id < 10, concat(PAS_clave,"00", PAS_id), if( PAS_id < 100, concat(PAS_clave, "0", PAS_id), concat(PAS_clave, PAS_id) ) ) as claveOrden' ),
						 					DB::raw( 'CONCAT( PAS_nombre, " ", PAS_aPaterno, " ", PAS_aMaterno ) as nombreCompleto' ),
											DB::raw( 'DATEDIFF( now(), PAS_fechaAlta ) as dias' ) )
						 ->join('usuarios', 'pases.USU_id', '=', 'usuarios.USU_id')
						 ->leftJoin('medicos', 'pases.USU_id', '=', 'medicos.USU_id')
						 ->leftJoin('medica_registromv.Unidad', 'medica_externos.pases.UNI_clave', '=', 'medica_registromv.Unidad.Uni_clave')
						 // ->where('PAS_id', $id)
						 ->where('PAS_id', $num)
						 ->where( 'PAS_clave', '=', $pre )
						 // ->where('Exp_folio', null)
						 ->get();
	}

	public function buscaPaseXnombre(){
		$nombre = Input::get('nombre');

		return DB::connection('externos')
						 ->table('pases')
						 ->select('medica_externos.pases.*', 'Unidad.*', 'USU_nombre', 'USU_aPaterno', 'USU_aMaterno', 'USU_username', 'medicos.*',
						 					DB::raw( 'if( PAS_id < 10, concat(PAS_clave,"00", PAS_id), if( PAS_id < 100, concat(PAS_clave, "0", PAS_id), concat(PAS_clave, PAS_id) ) ) as claveOrden' ),
						 					DB::raw( 'CONCAT( PAS_nombre, " ", PAS_aPaterno, " ", PAS_aMaterno ) as nombreCompleto' ),
											DB::raw( 'DATEDIFF( now(), PAS_fechaAlta ) as dias' ) )
						 ->join('usuarios', 'pases.USU_id', '=', 'usuarios.USU_id')
						 ->leftJoin('medicos', 'pases.USU_id', '=', 'medicos.USU_id')
						 ->leftJoin('medica_registromv.Unidad', 'medica_externos.pases.UNI_clave', '=', 'medica_registromv.Unidad.Uni_clave')
						 // ->Where('CONCAT( PAS_nombre, " ", PAS_aPaterno, " ", PAS_aMaterno )', 'like', '%' . $nombre . '%')
						 ->Where( DB::raw('CONCAT( PAS_nombre, " ", PAS_aPaterno, " ", PAS_aMaterno )'), 'like', '%' . $nombre . '%' )
						 // ->where('Exp_folio', null)
						 ->get();
	}

	public function buscaPaseXfolioMV(){
		$folioMV = Input::get('folio');

						 return DB::connection('externos')
						 ->table('pases')
						 ->select('medica_externos.pases.*', 'Unidad.*', 'USU_nombre', 'USU_username', 'medicos.*',
											DB::raw( 'if( PAS_id < 10, concat(PAS_clave,"00", PAS_id), if( PAS_id < 100, concat(PAS_clave, "0", PAS_id), concat(PAS_clave, PAS_id) ) ) as claveOrden' ),
											DB::raw( 'CONCAT( PAS_nombre, " ", PAS_aPaterno, " ", PAS_aMaterno ) as nombreCompleto' ),
											DB::raw( 'DATEDIFF( now(), PAS_fechaAlta ) as dias' ) )
						 ->join('usuarios', 'pases.USU_id', '=', 'usuarios.USU_id')
						 ->leftJoin('medicos', 'pases.USU_id', '=', 'medicos.USU_id')
						 ->leftJoin('medica_registromv.Unidad', 'medica_externos.pases.UNI_clave', '=', 'medica_registromv.Unidad.Uni_clave')
						 ->Where('Exp_folio', $folioMV)
						 ->get();
	}

	private function generaPrefijo(){
		$longitud = 2;

		$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($caracteres);
		$cadena = '';

		for ($i = 0; $i < $longitud ; $i++) {
				$cadena .= $caracteres[rand(0, $charactersLength - 1)];
		}

		return $cadena;
	}

	public function generaPase()
	{
		$datos = Input::All();
		$usuario = ExternosController::verificaUsuario( $datos['usuario'] );
		$enviaPaciente = Input::get('mailPaciente');
		$mailPaciente = Input::get('email');
		$enviaMedico = Input::get('copiaOrden');
		$mailUsuario = Input::get('mailUsuario');
		$clave = ExternosController::generaPrefijo();
		if ( Input::has('servicio') ){
		    $claveServicio = Input::get('servicio');
		} else{
			$claveServicio = null;
		}

		if ( sizeof( $usuario ) > 0 ) {
			try {
				$idPase = DB::connection('externos')
										 ->table('pases')
										 ->insertGetId(['PAS_clave' 					=> $clave,
										 								'PAS_nombre' 					=> $datos['nombre'],
										 								'PAS_aPaterno' 				=> $datos['aPaterno'],
																		'PAS_aMaterno' 				=> $datos['aMaterno'],
																		'PAS_fechaNacimiento'	=> $datos['fechaNacimiento'],
																		'PAS_sexo'						=> $datos['sexo'],
																		'USU_id' 							=> $usuario[0]->USU_id,
																		'UNI_clave' 					=> $datos['unidad'],
																		'PAS_claveServicio'		=> $claveServicio,
																		'PAS_diagnostico' 		=> $datos['diagnostico'],
																		'PAS_servicio' 				=> $datos['objetivo'],
																		'PAS_tipoTerapia' 		=> $datos['tipoTerapia'],
																		'PAS_catidadRehab' 		=> $datos['sesiones'],
																		'PAS_email' 					=> $datos['email'],
																		'PAS_telefono' 				=> $datos['telefono'],
																		'PAS_fechaAlta' 			=> DB::raw('now()'),
																		'PAS_fechaMod' 				=> DB::raw('now()')]);

				if ( $idPase > 0 ) {
					$numerador = '';
					if ( $idPase<100 ) { $numerador = '0'.$idPase; }
					if ( $idPase<10 ) { $numerador = '00'.$idPase; }
					if ( $idPase>=100 ) { $numerador = $idPase; }
					$claveCompuesta = $clave.$numerador;
					$regPase = ExternosController::buscaPase( $claveCompuesta );
					$mensaje = 'Pase generado correctamente.';

					$archivo = ExternosController::creapdf( $claveCompuesta );

					if ( $enviaPaciente == true ) { $mensaje .= ' El pase fue enviado al email del paciente.'; }
					if ( $enviaMedico == true ) { $mensaje .= ' Recibirá una copia de la órden en su correo electrónico.'; }
					// if ( $enviaPaciente == true || $enviaMedico == true ) {

						$datosCorreo = array('nombreCompleto' => $regPase[0]->nombreCompleto,
													 			 'claveOrden' 		=> $claveCompuesta);
						// if ( file_exists( $archivo['ruta'] ) ) {
							try {
								Mail::send('emails.aviso-orden', $datosCorreo, function($message) use ($datos, $archivo)
								{
									$message->from('contacto@medicavial.com.mx', 'Médica Vial');
									$message->subject('Orden de Rehabilitación MV');

									if ( $datos['mailPaciente'] == true ) {
										$message->to($datos['email']);
										// $message->bcc(array('sramirez@medicavial.com.mx'));

									}
									if ( $datos['copiaOrden'] == true ) {
										$message->cc($datos['mailUsuario']);
										// $message->bcc(array($datos['mailUsuario']));
										// $message->bcc(array($datos['mailUsuario'], 'sramirez@medicavial.com.mx'));
									}
									$message->bcc('sramirez@medicavial.com.mx');

									$adjunto = $archivo['ruta'];
									// $adjunto =  public_path().'/'.$archivo['ruta'];
									$message->attach($adjunto);
								});
							} catch (Exception $e) {
								return array('error' => $e);
							}
						// }
					// } //cierra condicion de correos

					return array('mensaje'	=> $mensaje,
											 'id' 			=> $idPase,
											 'info'			=> $regPase,
										 	 'sistema' 	=> 'success');
				}
			} catch (Exception $e) {
				return array('mensaje'	=> 'Error al registrar',
										 'id' 			=> 0,
									 	 'sistema' 	=> $e );
			}

		} else {
			return array('mensaje'	=> 'Error con el usuario',
									 'id' 		 	=> 0,
								 	 'sistema' 	=> 'failed' );
		}
	}

	public function creapdf( $idPase ){
		$pase = ExternosController::buscaPase( $idPase );
		$data = $pase[0];
		$html = View::make('pdf.orden', compact('data'));

		$options = new Options();
		$options->setIsRemoteEnabled(true);

		$dompdf = new Dompdf();
		$dompdf->loadHtml($html);
		$dompdf->setOptions($options);
		$dompdf->render();

		$dir = 'ordenes';
		if (!is_dir($dir)) {
		    mkdir($dir);
		}

		$output = $dompdf->output();
		$rutaCompleta = $dir.'/'.$idPase.".pdf";
		file_put_contents($rutaCompleta, $output);

		if ( file_exists($rutaCompleta) ) {
			$respuesta = array( 'pdf' => true, 'ruta' => $rutaCompleta );
		} else{
			$respuesta = array( 'pdf' => false, 'ruta' => 'N/A' );
		}

		return $respuesta;
	}

	public function verpdf( $idPase ){
		$pase = ExternosController::buscaPase( $idPase );
		$data = $pase[0];

		$html = View::make('pdf.orden', compact('data'));

		$options = new Options();
		$options->setIsRemoteEnabled(true);

		$dompdf = new Dompdf();
		$dompdf->loadHtml($html);
		$dompdf->render();
		$dompdf->setOptions($options);
		// $dompdf->stream();
		$dompdf->stream($idPase.".pdf", array("Attachment" => false));
		// return view('pdf.orden', compact('pase') );
		// return $html->render();
	}

	public function verificaUsername( $usuario ){
		$respuesta = ExternosController::verificaUsuario( $usuario );
		return sizeof( $respuesta );
	}

	public function actualizaEstatus(){
		$PAS_id 			= Input::get('PAS_id');
		$claveOrden 	= Input::get('claveOrden');
		$folioMV 			= Input::get('folioMV');
		$estatusAdmin = Input::get('estatusAdmin');
		$estatusAtn 	= Input::get('estatusAtn');
		$usrMV 				= Input::get('usrMV');

		//cuando se hace el registro en clínica MV
		//deberia traer el id del pase
		if ( $estatusAtn==2 && sizeof($PAS_id) > 0 ) {
			$cambio = DB::connection('externos')
										->table('pases')
								    ->where('PAS_id', $PAS_id)
								    ->update(['Exp_folio' => $folioMV,
															'EAT_id' 		=> $estatusAtn]);
		}
		return $cambio;
	}

	public function guardaUsuario(){
		$calle 					= Input::get('calle');
		$colonia 				= Input::get('colonia');
		$comision 			= Input::get('comision');
		$comision10 		= Input::get('comision10');
		$cp 						= Input::get('cp');
		$listaCuentas 	= Input::get('cuentas');
		$email 					= Input::get('email');
		$materno 				= Input::get('materno');
		$municipio 			= Input::get('municipio');
		$nombre 				= Input::get('nombre');
		$password 			= Input::get('password');
		$paterno 				= Input::get('paterno');
		$persona 				= Input::get('persona');
		$rfc 						= Input::get('rfc');
		$telFijo 				= Input::get('telFijo');
		$telMovil 			= Input::get('telMovil');
		$tipo 					= Input::get('tipo');
		$username 			= Input::get('username');

		//se verifica que no exista el username solicitado
		$verificacion = ExternosController::verificaUsuario( $username );
		if ( sizeof($verificacion) > 0 ) {
			//si ya existe el username mandamos mensaje de error
			return array('respuesta' => 'Username invalido, ya existe.');
		} else{
			//si no existe el usuario lo registramos en la base
			// return array( 'respuesta' => Input::All() );

			if ( $tipo == 'Promotor' ) {
				$tipoUsuario = 4;
			}
			if ( $tipo == 'Ejecutivo' ) {
				$tipoUsuario = 5;
			}
			if ( $tipo == 'Convenio' ) {
				$tipoUsuario = 6;
			}

			$idUsuario = DB::connection('externos')
										 ->table('usuarios')
										 ->insertGetId(['USU_username' 	=> $username,
									 									'USU_password' 	=> MD5($password),
																		'USU_nombre' 		=> $nombre,
																		'USU_aPaterno'	=> $paterno,
																		'USU_aMaterno'	=> $materno,
																		'PER_id'				=> $tipoUsuario,
																		'USU_email'			=> $email,
																		'USU_activo'		=> DB::raw('1'),
																		'USU_fechaAlta'	=> DB::raw('now()'),
																		'USU_fechaMod' 	=> DB::raw('now()')]);

			//Si se guardó correctamente el usuario procedemos a guardar telefonos y cuenta
			if ($idUsuario > 0) {
				//guardamos telefonos
				$guardaTel 		= ExternosController::guardaTelefonos( $telFijo, $idUsuario, 1 );
				$guardaMovil 	= ExternosController::guardaTelefonos( $telMovil, $idUsuario, 2 );

				//guardamos los datos de las cuentas
				$datosCuentas = array('idUsuario' => $idUsuario,
															'username' 	=> $username,
															'cuentas'		=> $listaCuentas);
				$guardaCuentas = ExternosController::guardaCuentas( $datosCuentas );

				$datosComisiones = array('idUsuario' 	=> $idUsuario,
																 'rfc' 				=> $rfc,
																 'persona' 		=> $persona,
																 'calleNum' 	=> $calle,
																 'cp' 				=> $cp,
																 'colonia' 		=> $colonia,
															 	 'municipio' 	=> $municipio,
																 'com5'				=> $comision,
																 'com10' 			=> $comision10
															 );
				$guardaComisiones = ExternosController::datosComisiones( $datosComisiones );
			}

			return array('respuesta' 	=> 1,
		 							 'idUsuario'	=> $idUsuario);
		}
	}

	private function rutaAlmacenamiento( $username, $tipo ){
		if ( $tipo == 'clabe' ) {
			return 'reh/users/'.$username.'/'.'clabe/';
		}
	}

	private function almacenaImg( $ruta, $nombre, $archivo ){
		// verificamos que exista la ruta
		if( !is_dir($ruta) ){
			// generamos la carpeta en caso de que no exista
			mkdir($ruta, 0777, true);
		}
		// guardamos el archivo el archivo
		file_put_contents($ruta.$nombre, base64_decode($archivo['value']));

		// generamos una respuesta de acuerdo al archivo guardado o no guardado
		if ( file_exists($ruta.$nombre) ) {
			$respuesta = true;
		}
		else{
			$respuesta = false;
		}
		return $respuesta;
	}

	private function guardaTelefonos( $numero, $idUsuario, $tipo ){
		//$tipo = 1 -> fijo / Tipo = 2 -> movil
		$guarda= DB::connection('externos')
							 ->table('telefonos')
							 ->insertGetId(['TEL_numero' 				=> $numero,
							 								'TEL_tipo' 					=> $tipo,
															'USU_id' 						=> $idUsuario,
															'TEL_alta'					=> DB::raw('now()'),
															'TEL_modificacion' 	=> DB::raw('now()')]);
	}

	private function guardaCuentas( $datos ){
		//generamos la ruta en la que se van a guardar los documentos de este usuario
		$ruta = ExternosController::rutaAlmacenamiento( $datos['username'], 'clabe' );

		for ($i=0; $i < sizeof( $datos['cuentas'] ) ; $i++) {
			$file = $datos['cuentas'][$i]['archivo'];

			$extension = pathinfo($file['filename'], PATHINFO_EXTENSION);
			$nombreArchivo = $datos['cuentas'][$i]['clabe'].'.'.$extension;
			$guardaArchivo = ExternosController::almacenaImg( $ruta, $nombreArchivo, $file );

			$guarda= DB::connection('externos')
								 ->table('cuentas')
								 ->insertGetId(['USU_id' 						=> $datos['idUsuario'],
																'CUE_banco' 				=> $datos['cuentas'][$i]['banco'],
																'CUE_clabe'					=> $datos['cuentas'][$i]['clabe'],
																'CUE_archivo'				=> $ruta.$nombreArchivo,
																'CUE_alta' 					=> DB::raw('now()'),
																'CUE_modificacion'	=> DB::raw('now()'),
																'CUE_activa' 				=> DB::raw('1')]);
		}
	}

	private function datosComisiones( $datos ){
		 $guarda = DB::connection('externos')
								 ->table('cuentasComisiones')
								 ->insertGetId(['USU_id' 						=> $datos['idUsuario'],
																'CUC_tipoPersona'		=> $datos['persona'],
																'CUC_rfc'						=> $datos['rfc'],
																'CUC_calleNum'			=> $datos['calleNum'],
																'CUC_cp' 						=> $datos['cp'],
																'CUC_col'						=> $datos['colonia'],
																'CUC_municipio'			=> $datos['municipio'],
																'CUC_comision5'			=> $datos['com5'],
																'CUC_comision10'		=> $datos['com10'],
																'CUC_activo'				=> DB::raw('1'),
																'CUC_alta'					=> DB::raw('now()'),
																'CUC_modificacion'	=> DB::raw('now()')]);
	}

	// RETORNO DE IMAGENES EN BASE64
	// if ($guarda) {
	// 	$path = $ruta.$nombreArchivo;
	// 	$type = pathinfo($path, PATHINFO_EXTENSION);
	// 	$data = file_get_contents($path);
	// 	$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
	// 	// return $base64;
	// 	return array('datos' => Input::All(), 'img' => $base64 );
	// }


	// DATOS DE REHABILITACIONES PARA LA BITACORA Y EL INFORME
	public function datosSesionesRh( $folioMV ){
		$sesiones = DB::table('Rehabilitacion')->where('Exp_folio', $folioMV)->orderBy('Rehab_cons', 'desc')->get();
		$informe 	= DB::table('InformeRehabilitacion')->where('Exp_folio', $folioMV)->get();

		return array( 'sesiones' 	=> $sesiones,
									'informe' 	=> $informe
								);
	}
}
