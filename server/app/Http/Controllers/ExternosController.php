<?php namespace App\Http\Controllers;
/***** Controlador para busquedas y operaciones de externos *****/
/***** Samuel Ramírez - Marzo 2018 *****/

use DB;
use Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ExternosController extends Controller {
	public function listadoMedicos()
	{
		$listado = DB::connection('externos')
							 ->table('medicos')
							 ->Where('MED_activo', 1)
							 ->get();
		return $listado;
	}

	public function listadoPases()
	{
		$listado = DB::connection('externos')
							 ->table('pases')
							 ->Where('PAS_cancelado', 0)
							 ->Where('PAS_atendido', 0)
							 ->limit(20)
							 ->get();
		return $listado;
	}

	private function verificaUsuario( $usuario ){
		return DB::connection('externos')
						 ->table('usuarios')
						 ->where('USU_username', $usuario)
						 ->limit(1)
						 ->get();
	}

	public function generaPase()
	{
		$datos = Input::All();
		$usuario = ExternosController::verificaUsuario( $datos['usuario'] );

		if ( sizeof( $usuario ) > 0 ) {
			try {
				$idPase = DB::connection('externos')
										 ->table('pases')
										 ->insertGetId(['PAS_nombre' 					=> $datos['nombre'],
										 								'PAS_aPaterno' 				=> $datos['aPaterno'],
																		'PAS_aMaterno' 				=> $datos['aMaterno'],
																		'PAS_fechaNacimiento'	=> $datos['fechaNacimiento'],
																		'ORG_id' 							=> 0,
																		'USU_id' 							=> $usuario[0]->USU_id,
																		'UNI_clave' 					=> 0,
																		'PAS_diagnostico' 		=> $datos['diagnostico'],
																		'PAS_servicio' 				=> $datos['objetivo'],
																		'PAS_tipoTerapia' 		=> $datos['tipoTerapia'],
																		'PAS_catidadRehab' 		=> $datos['sesiones'],
																		'PAS_email' 					=> $datos['email'],
																		'PAS_telefono' 				=> $datos['telefono'],
																		'PAS_fechaAlta' 			=> DB::raw('now()'),
																		'PAS_fechaMod' 				=> DB::raw('now()')]);

				if ( $idPase > 0 ) {
					return array('mensaje'	=> 'Registrado',
											 'id' 			=> $idPase,
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

}
