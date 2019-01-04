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
		return 'it´s works';
	}


	public function alertaVenta(){
		Mail::raw('Entro un pago', function($message)
		{
			$message->from('sramirez@medicavial.com.mx', 'SistemaMV');

			$message->to('samuel11rr@gmail.com');
			$message->cc('sramirez@medicavial.com.mx');
		});


		$data = Input::get('object');

		// return $data['customer']['default_address'];

		$customer	= $data['customer']['name'];
		$address 	= $data['customer']['default_address']['address1'].', '.$data['customer']['default_address']['address2'].','.$data['customer']['default_address']['address2'];
		$items 		= $data['items'][0]['name'];
		$cantidad	= $data['items'][0]['quantity'];
		$email 		= $data['email'];
		$phone 		= $data['customer']['default_address']['phone'];
		
		$texto = 'Se ha completado la compra de '.$cantidad.' '.$items.' para '.$customer.': '.$address.' | '.$phone.' | '.$email;

		try {
			Mail::raw($texto, function($message)
			{
				$message->from('sramirez@medicavial.com.mx', 'SistemaMV');

				$message->to('samuel11rr@gmail.com');
				$message->cc('sramirez@medicavial.com.mx');
			});
		} catch (Exception $e) {
			return array('error' 	=> $e, 
						 'data' 	=> $data,
						 'message' 	=> 'No enviado' );
		}

		return array('data' 	=> $data,
					 'message' 	=> 'Enviado' );
	}

}
