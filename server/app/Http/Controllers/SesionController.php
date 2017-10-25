<?php namespace App\Http\Controllers;

use DB;
use Input;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class SesionController extends Controller {

	public function buscaUsuario()
	{
		$username 	= Input::get('username');
		$password 	= md5(Input::get('password'));

		$usuario = SesionController::sesionMV( $username, $password );
		if ( sizeof($usuario) > 0 ) {
			return $usuario;
		} else{
			$usuario = SesionController::sesionZima( $username, $password );
			return $usuario;
		}
	}

	public function sesionMV( $username, $password )
	{
		$busqueda = DB::table('Usuario')
										->select('Usu_login as username', 'Usu_nombre as fullName', 'Usuario.Uni_clave as cveUnidad',
														 'Uni_nombrecorto as nombreUnidad', DB::raw('CONCAT("Médica Vial") as origen'))
										->join('Unidad', 'Usuario.Uni_clave', '=', 'Unidad.Uni_clave')
										->where('Usu_login', $username)
										->where('Usu_pwd', $password)
										->where('Usu_activo', 'S')
										->get();
		return $busqueda;
	}

	public function sesionZima( $username, $password )
	{
		$busqueda = DB::connection('zima')
							 ->table('PUUsuario')
							 ->select('PUUsu_login as username', DB::raw('CONCAT(PUUsu_nombre, " ", PUUsu_apaterno, " ", PUUsu_amaterno) as fullName'),
							 					'PUUsuario.PUUni_clave as cveUnidad', 'UNI_nomCorto as nombreUnidad', DB::raw('CONCAT("Zima") as origen'))
							 ->join('Unidad', 'PUUsuario.PUUni_clave', '=', 'Unidad.UNI_clave')
							 ->Where('PUUsu_login', $username)
							 ->Where('PUUsu_cadenaentrada', $password)
							 ->Where('PUUsu_activo', 'S')
							 ->get();
		return $busqueda;
	}

}
