<?php namespace App\Http\Controllers;

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


}
