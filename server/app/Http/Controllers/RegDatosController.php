<?php namespace App\Http\Controllers;
/***** Controlador para el registro de datos en las bases de datos *****/
/***** Samuel Ramírez - Octubre 2017 *****/

use DB;
use Input;
use Response;
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

		} catch (Exception $e) {

		}

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

		return Response::json(array('respuesta' => 'Nota Creada Correctamente'));
	}

	public function guardaAsignacion()
	{
		$folio 					= Input::get('folio');
		$username 			= Input::get('username');
		$cveMedico 			= Input::get('cveMedico');
		$username				= Input::get('username');
		$fechaAtencion 	= Input::get('fechaAtencion');
		$cveUnidad 			= Input::get('cveUnidad');

		try {
			$respuesta = DB::table('redQx_asignaciones')
												->insert([
															    'Exp_folio' 						=> $folio,
																	'UNI_clave' 						=> $cveUnidad,
																	'ASI_fechaCita' 				=> $fechaAtencion,
																	'USU_idMedico' 					=> $cveMedico,
																	'USU_loginRegistro' 		=> $username,
																	'ASI_fechaRegistro' 		=> DB::raw('now()')
																	]);
		} catch (Exception $e) {
			$respuesta = $e;
		}

		return Response::json(array('respuesta' => $respuesta));
	}

	public function generaCredenciales( $cveMedico )
	{
		$inicial = DB::table('Medico')
									->select('Med_nombre')
									->Where('Med_clave', $cveMedico)
									// ->Where('Uni_clave', '<>', 8) //deberiamos quitar la unidad 8
									->get();

		return $inicial;

		// try {
    //
		// } catch (Exception $e) {
		// 	$respuesta = $e;
		// }
    //
		// return Response::json(array('respuesta' => $respuesta));
	}

}
