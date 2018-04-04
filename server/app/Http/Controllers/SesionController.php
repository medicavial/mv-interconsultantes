<?php namespace App\Http\Controllers;
/***** Controlador para logueo de usuarios *****/
/***** Samuel Ramírez - Octubre 2017 *****/

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

		$usuario = SesionController::sesionRedQx( $username, $password );
		return $usuario;

		// $usuario = SesionController::sesionMV( $username, $password );
		// if ( sizeof($usuario) > 0 ) {
		// 	return $usuario;
		// } else{
		// 	$usuario = SesionController::sesionZima( $username, $password );
		// 	return $usuario;
		// }
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

	public function sesionRedQx( $username, $password )
	{
		$busqueda = DB::table('redQx_usuarios')
										->select('USU_login as username', 'USU_nombreCompleto as fullName', 'redQx_permisos.PER_clave',
														'USU_fechaRegistro', DB::raw('CONCAT(0) as unidad'), DB::raw('redQx_permisos.*'), 'USU_email')
										->join('redQx_permisos', 'redQx_usuarios.PER_clave', '=', 'redQx_permisos.PER_clave')
										->where('USU_login', $username)
										->where('USU_password', $password)
										->where('USU_activo', 1)
										->get();
		return $busqueda;
	}

	public function sesionExternos()
	{
		$username 	= Input::get('username');
		$password 	= md5(Input::get('password'));

		$busqueda = DB::connection('externos')
							 ->table('usuarios')
							 ->select('USU_nombre as fullName', 'USU_username as username', 'USU_email', 'USU_telefono',
							 					'USU_avatar', 'USU_activo', 'USU_fechaAlta', 'permisos.*')
							 ->join('permisos', 'usuarios.PER_id', '=', 'permisos.PER_id')
							 ->Where('USU_username', $username)
							 ->Where('USU_password', $password)
							 ->Where('USU_activo', 1)
							 ->get();
		return $busqueda;
	}

}
