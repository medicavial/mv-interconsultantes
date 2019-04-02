<?php namespace App\Http\Controllers;
/***** Controlador para datos relacionados con directorios *****/
/***** Samuel Ramírez - Abril 2019 *****/
use DB;
use Input;
use Response;
use Mail;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DirectoriosController extends Controller {

	private function verificaUsuario( $usuario ){
		$busqueda = DB::connection('directorios')
					  ->table('usuarios')
					  ->where('USU_id', $usuario)
					  ->where('USU_activo', 1)
					  ->first();

		return ( sizeof($busqueda) == 1 ) ? true : false;
	}

	public function tipoClaveUnidad( $usuario = null ) {
		$detalles = DirectoriosController::verificaUsuario( $usuario );

		$query  = DB::connection('directorios')
				   	->table('tipo_claveUnidad');
				   	if ( !$detalles ) $query->select( 'TCL_id', 'TCL_nombre', 'TCL_descripcion' );
					if ( $detalles ) {
						$query->select( 'tipo_claveUnidad.*', 
										DB::raw( 'CONCAT( creador.USU_nombres, " ", creador.USU_apellidos ) as CRE_nombre' ),
										DB::raw( 'CONCAT( modificador.USU_nombres, " ", modificador.USU_apellidos ) as MOD_nombre' ) )
							  ->join('usuarios as creador', 'tipo_claveUnidad.USU_creador', '=', 'creador.USU_id')
							  ->join('usuarios as modificador', 'tipo_claveUnidad.USU_modificador', '=', 'modificador.USU_id');
					}
				   	$query->where('TCL_activo', 1);

		return $query->get();
	}


	public function tipoContribuyente( $usuario = null ) {
		$detalles = DirectoriosController::verificaUsuario( $usuario );

		$query  = DB::connection('directorios')
				   	->table('tipo_contribuyente');
				   	if ( !$detalles ) $query->select( 'TCO_id', 'TCO_nombre' );
					if ( $detalles ) {
						$query->select( 'tipo_contribuyente.*', 
										DB::raw( 'CONCAT( creador.USU_nombres, " ", creador.USU_apellidos ) as CRE_nombre' ),
										DB::raw( 'CONCAT( modificador.USU_nombres, " ", modificador.USU_apellidos ) as MOD_nombre' ) )
							  ->join('usuarios as creador', 'tipo_contribuyente.USU_creador', '=', 'creador.USU_id')
							  ->join('usuarios as modificador', 'tipo_contribuyente.USU_modificador', '=', 'modificador.USU_id');
					}
				   	$query->where('TCO_activo', 1);

		return $query->get();
	}


	public function tipoCreacionUsuario( $usuario = null ) {
		$detalles = DirectoriosController::verificaUsuario( $usuario );

		$query  = DB::connection('directorios')
				   	->table('tipo_creacionUsuario');
				   	if ( !$detalles ) $query->select( 'TIC_id', 'TIC_nombre', 'TIC_descripcion' );
					if ( $detalles ) {
						$query->select( 'tipo_creacionUsuario.*', 
										DB::raw( 'CONCAT( creador.USU_nombres, " ", creador.USU_apellidos ) as CRE_nombre' ),
										DB::raw( 'CONCAT( modificador.USU_nombres, " ", modificador.USU_apellidos ) as MOD_nombre' ) )
							  ->join('usuarios as creador', 'tipo_creacionUsuario.USU_creador', '=', 'creador.USU_id')
							  ->join('usuarios as modificador', 'tipo_creacionUsuario.USU_modificador', '=', 'modificador.USU_id');
					}
				   	$query->where('TIC_activo', 1);

		return $query->get();
	}


	public function tipoPaquete( $usuario = null ) {
		$detalles = DirectoriosController::verificaUsuario( $usuario );

		$query  = DB::connection('directorios')
				   	->table('tipo_paquete');
				   	if ( !$detalles ) $query->select( 'TPQ_id', 'TPQ_nombre', 'TPQ_descripcion' );
					if ( $detalles ) {
						$query->select( 'tipo_paquete.*', 
										DB::raw( 'CONCAT( creador.USU_nombres, " ", creador.USU_apellidos ) as CRE_nombre' ),
										DB::raw( 'CONCAT( modificador.USU_nombres, " ", modificador.USU_apellidos ) as MOD_nombre' ) )
							  ->join('usuarios as creador', 'tipo_paquete.USU_creador', '=', 'creador.USU_id')
							  ->join('usuarios as modificador', 'tipo_paquete.USU_modificador', '=', 'modificador.USU_id');
					}
				   	$query->where('TPQ_activo', 1);

		return $query->get();
	}


	public function tipoUnidad( $usuario = null ) {
		$detalles = DirectoriosController::verificaUsuario( $usuario );

		$query  = DB::connection('directorios')
				   	->table('tipo_unidad');
				   	if ( !$detalles ) $query->select( 'TIU_id', 'TIU_nombre', 'TIU_descripcion' );
					if ( $detalles ) {
						$query->select( 'tipo_unidad.*', 
										DB::raw( 'CONCAT( creador.USU_nombres, " ", creador.USU_apellidos ) as CRE_nombre' ),
										DB::raw( 'CONCAT( modificador.USU_nombres, " ", modificador.USU_apellidos ) as MOD_nombre' ) )
							  ->join('usuarios as creador', 'tipo_unidad.USU_creador', '=', 'creador.USU_id')
							  ->join('usuarios as modificador', 'tipo_unidad.USU_modificador', '=', 'modificador.USU_id');
					}
				   	$query->where('TIU_activo', 1);

		return $query->get();
	}


	public function unidades( $unidad = 0, $usuario = null ) {
		$detalles = DirectoriosController::verificaUsuario( $usuario );

		$query  = DB::connection('directorios')
				   	->table('unidades');
				   	if ( !$detalles ) $query->select( 'UNI_id', 'UNI_nombreComercial', 'UNI_nombreCorto', 'UNI_alias', 'UNI_horario' );
					if ( $detalles ) {
						$query->select( 'unidades.*',
										'fiscalesUnidad.*', 
										'responsablesMV.*', 
										'tipo_unidad.TIU_nombre',
										DB::raw( 'CONCAT( creador.USU_nombres, " ", creador.USU_apellidos ) as CRE_nombre' ),
										DB::raw( 'CONCAT( modificador.USU_nombres, " ", modificador.USU_apellidos ) as MOD_nombre' ) )
							  ->join('fiscalesUnidad', 'unidades.UNI_id', '=', 'fiscalesUnidad.UNI_id')
							  ->join('responsablesMV', 'unidades.RMV_id', '=', 'responsablesMV.RMV_id')
							  ->join('tipo_unidad', 'unidades.TIU_id', '=', 'tipo_unidad.TIU_id')
							  ->join('usuarios as creador', 'unidades.USU_creador', '=', 'creador.USU_id')
							  ->join('usuarios as modificador', 'unidades.USU_modificador', '=', 'modificador.USU_id');
					}
					if ( $unidad > 0 ) $query->where( 'UNI_id', $unidad );
					$query->where('UNI_activa', 1);

		return $query->get();
	}
}
