<?php namespace App\Http\Controllers;
/***** Controlador para busquedas y operaciones de externos *****/
/***** Samuel Ramírez - Marzo 2018 *****/

use DB;
use View;
use Input;
use Mail;
use Response;
use Dompdf\Dompdf;
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

	public function creapdf(){
		$html = View::make('pdf.orden')->render();

		$dompdf = new Dompdf();
		$dompdf->loadHtml($html);
		$dompdf->setPaper('Letter', 'portrait');
		$dompdf->render();
		$dompdf->stream();
	}

	public function listadoPases( $idUsuario )
	{
		$listado = DB::connection('externos')
							 ->table('pases')
							 ->Where('PAS_cancelado', 0)
							 ->Where('PAS_atendido', 0);
							 if ( $idUsuario > 1 ) {
							 	$listado->where('USU_id', $idUsuario);
							 }
		return $listado->limit(20)->get();
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
		$enviaPaciente = Input::get('mailPaciente');
		$mailPaciente = Input::get('email');
		$enviaMedico = Input::get('copiaOrden');
		$mailUsuario = Input::get('mailUsuario');

		// return Input::All();

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
																		'UNI_clave' 					=> $datos['unidad'],
																		'PAS_diagnostico' 		=> $datos['diagnostico'],
																		'PAS_servicio' 				=> $datos['objetivo'],
																		'PAS_tipoTerapia' 		=> $datos['tipoTerapia'],
																		'PAS_catidadRehab' 		=> $datos['sesiones'],
																		'PAS_email' 					=> $datos['email'],
																		'PAS_telefono' 				=> $datos['telefono'],
																		'PAS_fechaAlta' 			=> DB::raw('now()'),
																		'PAS_fechaMod' 				=> DB::raw('now()')]);

				if ( $idPase > 0 ) {
					$mensaje = 'Pase generado correctamente.';
					// if ( $copiaCorreo == true ) {
					// }
					if ( $enviaPaciente == true ) { $mensaje .= ' El pase fue enviado al email del paciente.'; }
					if ( $enviaMedico == true ) { $mensaje .= ' Recibirá una copia de la órden en su correo electrónico.'; }
					if ( $enviaPaciente == true || $enviaMedico == true ) {
						try {
							Mail::send('emails.pruebaCorreo', $datos, function($message) use ($datos)
							{
								$message->from('contacto@medicavial.com.mx', 'Médica Vial');
								$message->subject('Orden de Rehabilitación');
								if ( $datos['mailPaciente'] == true ) {
									$message->to($datos['email']);
								}
								if ( $datos['copiaOrden'] == true ) {
									// $message->cc($datos['mailAdmin']);
									$message->bcc(array($datos['mailUsuario']));
								}
							});
						} catch (Exception $e) {

						}
					}
					return array('mensaje'	=> $mensaje,
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
