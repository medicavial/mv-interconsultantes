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
		$nombre 	= Input::get('nombre');
		$aPaterno = Input::get('aPaterno');
		$aMaterno = Input::get('aMaterno');
		$email 		= Input::get('email');
		$rol 			= Input::get('rol');

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
			$contador++;
		}

		return $usrLogin;

		// $respuesta = DB::table('Medico')
		// 								->select( DB::raw('LOWER( CONCAT( SUBSTRING( Med_nombre,1,1 ), Med_paterno, SUBSTRING( Med_materno,1,1 ) ) ) as username'), 'Medico.*' )
		// 								->where('Med_clave', $cveMedico)
		// 								->get();
    //
		// foreach ($respuesta as $datos) {
		// 	$nombre = $datos->Med_nombre.' '.$datos->Med_paterno.' '.$datos->Med_materno;
		// 	$email 	= $datos->Med_correo;
    //
		// 	$username = $datos->username;
		// 	$username = str_replace(' ','',$username);
		// 	$username = str_replace('ñ','n',$username);
		// 	$username = str_replace('á','a',$username);
		// 	$username = str_replace('é','e',$username);
		// 	$username = str_replace('í','i',$username);
		// 	$username = str_replace('ó','o',$username);
		// 	$username = str_replace('ú','u',$username);
		// }

		// $mayuculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		// $caracteres = '#$%&0123456789abcdefghijklmnopqrstuvwxyz';
    // $cadena = '';
    //
    // for ($i=0; $i<1; $i++) {
    //     $cadena .= $mayuculas[rand(0, strlen($mayuculas) - 1)];
    // }
    //
		// for ($i=0; $i<5; $i++) {
    //     $cadena .= $caracteres[rand(0, strlen($caracteres) - 1)];
    // }

    // $perfilUsuario = array(	'username'	=> $username,
		// 												'password' 	=> $cadena,
		// 											 	'usuario'		=> $nombre,
		// 												'email'			=> $email);

		// try {
		// 	$respuesta = DB::table('redQx_usuarios')
		// 										->insert([
		// 													    'USU_login' 						=> $perfilUsuario['username'],
		// 															'USU_email' 						=> $perfilUsuario['email'],
		// 															'USU_nombreCompleto' 		=> $perfilUsuario['usuario'],
		// 															'USU_password' 					=> md5( $perfilUsuario['password'] ),
		// 															'PER_clave' 						=> 3, //esto lo cambiaremos despues
		// 															'USU_fechaRegistro' 		=> DB::raw('now()')
		// 															]);
		// } catch (Exception $e) {
		// 	$respuesta = $e;
		// }
    //
		// // RegDatosController::correoCredenciales( $perfilUsuario );
		// array_push($perfilUsuario, $respuesta);
		// return $perfilUsuario;
	}

	public function correoCredenciales(  )
	{
		$datos = array(
										"username" => "scisnerosm",
										"password" => "Xjmwfr",
										"usuario" => "Sergio Cisneros Mora",
										"email" => "algo@asas"
										);
		// return $datos;
		// return view('emails/credenciales', $datos);

		Mail::send('emails.credenciales', $datos, function($message) use ($datos)
		{
			$message->from('sramirez@medicavial.com.mx', 'Médica Vial');
		  $message->to('samuel11rr@gmail.com', 'Samuel')->subject('Nombre de usuario y contraseña');
			// $message->cc('jacortes@medicavial.com.mx');
    	// $message->bcc(array('samuel11rr@gmail.com','samuel_ramirez@live.com.mx'));
		});

        return Response::json(array('respuesta' => 'Correo enviado Correctamente'));

	}

}
