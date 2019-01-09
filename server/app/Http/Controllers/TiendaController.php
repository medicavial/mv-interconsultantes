<?php namespace App\Http\Controllers;
/***** Controlador para comunicacion con ecomerce *****/
/***** Samuel Ramírez - Enero 2019 *****/

use DB;
use View;
use Input;
use Mail;
use Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class TiendaController extends Controller {

	public function index() {
		return 'Funciona';
	}

	public function alertaVenta(){
		// Recibir el cuerpo de la petición.
		$input = @file_get_contents("php://input");
		// Parsear el contenido como JSON.
		$data = json_decode($input);

		// Usar los datos del Webhooks para alguna acción.
		$datos = array('datos' => $data->data->object);
		
		try {
			Mail::send('emails.venta', $datos, function($message)
			{
				$message->from('sramirez@medicavial.com.mx', 'SistemaMV');
				$message->subject('Venta Online MedicaVial');

				$message->to('samuel11rr@gmail.com');
				$message->cc('sramirez@medicavial.com.mx');
			});
		} catch (Error $e) {
			
		}

		// Responder
		return http_response_code(200);
	}


	public function vistaVenta(){
		// Recibir el cuerpo de la petición.
		$input = @file_get_contents("php://input");
		// Parsear el contenido como JSON.
		$data = json_decode($input);

		$datos = array('datos' => $data->data->object);


		return view('emails.venta', $datos);
	}

}
