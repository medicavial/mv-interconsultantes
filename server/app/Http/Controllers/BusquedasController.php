<?php namespace App\Http\Controllers;
/***** Controlador para busqueda de datos y conexion a otras APIS *****/
/***** Samuel Ramírez - Octubre 2017 *****/

use DB;
use Input;
use Response;
use Mail;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class BusquedasController extends Controller {

	public function index()
	{
		return json_encode('funciona');
	}

	public function buscaPaciente()
	{
		$folio 	= Input::get('folio');
		$nombre = Input::get('nombre');

		// if ( ($folio!=null || $folio!='') && ($nombre!=null || $nombre!='') ) {
		if ( ($folio!='') && ($nombre!='') ) {
			$porFolio = BusquedasController::buscaXfolioMV($folio);
			if ( sizeof($porFolio) > 0 ) {
			} else{
				$porFolio = BusquedasController::buscaXfolioZima($folio);
			}
			$porNombreMV = BusquedasController::buscaXnombreMV($nombre);
			$porNombreZM = BusquedasController::buscaXnombreZima($nombre);

			$porNombre = array_merge( $porNombreMV, $porNombreZM );
			$busquedaCompleta = array_merge( $porFolio, $porNombre );

			return $busquedaCompleta;
		}
		// elseif ($folio!=null || $folio!='') {
		elseif ($folio!='') {
			$porFolio = BusquedasController::buscaXfolioMV($folio);
			if ( sizeof($porFolio) > 0 ) {
				return $porFolio;
			} else{
				$porFolio = BusquedasController::buscaXfolioZima($folio);
				return $porFolio;
			}
		}	elseif ($nombre!='') {
			$porNombreMV = BusquedasController::buscaXnombreMV($nombre);
			$porNombreZM = BusquedasController::buscaXnombreZima($nombre);
			$porNombre = array_merge($porNombreMV, $porNombreZM);
			return $porNombre;
			};
	}

	public function buscaXfolioMV($folio)
	{
		$expediente = DB::table('Expediente')
										->leftJoin('Compania', 'Expediente.Cia_clave', '=', 'Compania.Cia_clave')
										->leftJoin('Producto', 'Expediente.Pro_clave', '=', 'Producto.Pro_clave')
										->leftJoin('RiesgoAfectado', 'Expediente.RIE_clave', '=', 'RiesgoAfectado.RIE_clave')
										->leftJoin('Unidad', 'Expediente.Uni_claveActual', '=', 'Unidad.Uni_clave')
										->leftJoin('Usuario', 'Expediente.Usu_registro', '=', 'Usuario.Usu_login')
										->leftJoin('RegMVZM', 'Expediente.Exp_folio', '=', 'RegMVZM.Fol_MedicaVial')
										->select('Exp_folio as folio', 'Uni_claveActual as cveUnidad', 'Exp_completo as nombre',  'Exp_edad as edad', 'Exp_sexo as sexo',
														 'Exp_siniestro as siniestro', 'Exp_poliza as poliza', 'Exp_reporte as reporte', 'Exp_RegCompania as folioElectronico',
														 'Exp_fecreg as fechaRegistro', 'Expediente.Cia_clave as cveCompania', 'Usu_registro as loginRegistro',
														 'Exp_obs as observaciones', 'Expediente.Pro_clave as cveProducto', 'Cia_nombrecorto as nombreCompania',
														 'Pro_nombre as nombreProducto', 'Expediente.RIE_clave as cveRiesgo', 'RIE_nombre as nombreRiesgo',
														 'Usu_nombre as nombreUsuario', 'Uni_nombrecorto as nombreUnidad',
														 'Fol_ZIMA', DB::raw('CONCAT("Médica Vial") as registro'), DB::raw('CONCAT(1) as id_registro'))
										->where('Exp_folio', $folio)
										->where('Exp_cancelado', '<>', 1)
										// ->where('Exp_fecreg', '>', DB::raw('DATE(DATE_SUB(NOW(), INTERVAL 6 MONTH))'))
										->orderBy(DB::raw('Exp_completo, Exp_fecreg'))
										->get();
		return $expediente;
	}

	public function buscaXnombreMV($nombre)
	{
		$expediente = DB::table('Expediente')
										->leftJoin('Compania', 'Expediente.Cia_clave', '=', 'Compania.Cia_clave')
										->leftJoin('Producto', 'Expediente.Pro_clave', '=', 'Producto.Pro_clave')
										->leftJoin('RiesgoAfectado', 'Expediente.RIE_clave', '=', 'RiesgoAfectado.RIE_clave')
										->leftJoin('Unidad', 'Expediente.Uni_claveActual', '=', 'Unidad.Uni_clave')
										->leftJoin('Usuario', 'Expediente.Usu_registro', '=', 'Usuario.Usu_login')
										->leftJoin('RegMVZM', 'Expediente.Exp_folio', '=', 'RegMVZM.Fol_MedicaVial')
										->select('Exp_folio as folio', 'Uni_claveActual as cveUnidad', 'Exp_completo as nombre',  'Exp_edad as edad', 'Exp_sexo as sexo',
														 'Exp_siniestro as siniestro', 'Exp_poliza as poliza', 'Exp_reporte as reporte', 'Exp_RegCompania as folioElectronico',
														 'Exp_fecreg as fechaRegistro', 'Expediente.Cia_clave as cveCompania', 'Usu_registro as loginRegistro',
														 'Exp_obs as observaciones', 'Expediente.Pro_clave as cveProducto', 'Cia_nombrecorto as nombreCompania',
														 'Pro_nombre as nombreProducto', 'Expediente.RIE_clave as cveRiesgo', 'RIE_nombre as nombreRiesgo',
														 'Usu_nombre as nombreUsuario', 'Uni_nombrecorto as nombreUnidad',
														 'Fol_ZIMA', DB::raw('CONCAT("Médica Vial") as registro'), DB::raw('CONCAT(1) as id_registro'))
										->Where('Exp_completo', 'like', '%' . $nombre . '%')
										->where('Exp_cancelado', '<>', 1)
										->where('Exp_fecreg', '>', DB::raw('DATE(DATE_SUB(NOW(), INTERVAL 6 MONTH))'))
										->orderBy(DB::raw('Exp_completo, Exp_fecreg'))
										->get();
		return $expediente;
	}

	public function buscaXfolioZima($folio)
	{
		$expediente = DB::connection('zima')
							 ->table('PURegistro')
							 ->leftJoin('Aseguradora', 'PURegistro.ASE_clave', '=', 'Aseguradora.ASE_clave')
							 ->leftJoin('PUProductoZima', 'PURegistro.ProdZima_Clave', '=', 'PUProductoZima.ProdZima_Clave')
							 ->leftJoin('RiesgoAfectado', 'PURegistro.RIE_clave', '=', 'RiesgoAfectado.RIE_clave')
							 ->leftJoin('PUUsuario', 'PURegistro.USU_login', '=', 'PUUsuario.PUUsu_login')
							 ->leftJoin('Unidad', 'PURegistro.UNI_clave', '=', 'Unidad.UNI_clave')
							 ->select('REG_folio as folio', 'PURegistro.UNI_clave as cveUnidad', 'REG_nombrecompleto as nombre',  'REG_edad as edad', 'REG_genero as sexo',
							 					'REG_siniestro as siniestro', 'REG_poliza as poliza', 'REG_reporte as reporte', 'REG_folioelectronico as folioElectronico',
												'REG_fechahora as fechaRegistro', 'PURegistro.ASE_clave as cveCompania', 'PURegistro.USU_login as loginRegistro',
												'REG_observaciones as observaciones', 'PURegistro.ProdZima_Clave as cveProducto', 'ASE_nomCorto as nombreCompania',
												'ProdZima_Desc as nombreProducto', 'PURegistro.RIE_clave as cveRiesgo', 'RIE_nombre as nombreRiesgo',
												DB::raw('CONCAT(PUUsu_nombre," ",PUUsu_apaterno," ",PUUsu_amaterno) as nombreUsuario'), 'UNI_nomCorto as nombreUnidad',
												'REG_folioMV', DB::raw('CONCAT("Zima") as registro'), DB::raw('CONCAT(2) as id_registro'))
							 ->Where('REG_folio',$folio)
							 ->where('REG_cancelado', '<>', 'S')
							 // ->where('REG_fechahora', '>', DB::raw('DATE(DATE_SUB(NOW(), INTERVAL 6 MONTH))'))
							 ->orderBy(DB::raw('REG_nombrecompleto, REG_fechahora'))
							 ->get();
		return $expediente;
	}

	public function buscaXnombreZima($nombre)
	{
		$expediente = DB::connection('zima')
							 ->table('PURegistro')
							 ->leftJoin('Aseguradora', 'PURegistro.ASE_clave', '=', 'Aseguradora.ASE_clave')
							 ->leftJoin('PUProductoZima', 'PURegistro.ProdZima_Clave', '=', 'PUProductoZima.ProdZima_Clave')
							 ->leftJoin('RiesgoAfectado', 'PURegistro.RIE_clave', '=', 'RiesgoAfectado.RIE_clave')
							 ->leftJoin('PUUsuario', 'PURegistro.USU_login', '=', 'PUUsuario.PUUsu_login')
							 ->leftJoin('Unidad', 'PURegistro.UNI_clave', '=', 'Unidad.UNI_clave')
							 ->select('REG_folio as folio', 'PURegistro.UNI_clave as cveUnidad', 'REG_nombrecompleto as nombre', 'REG_edad as edad', 'REG_genero as sexo',
							 					'REG_siniestro as siniestro', 'REG_poliza as poliza', 'REG_reporte as reporte', 'REG_folioelectronico as folioElectronico',
												'REG_fechahora as fechaRegistro', 'PURegistro.ASE_clave as cveCompania', 'PURegistro.USU_login as loginRegistro',
												'REG_observaciones as observaciones', 'PURegistro.ProdZima_Clave as cveProducto', 'ASE_nomCorto as nombreCompania',
												'ProdZima_Desc as nombreProducto', 'PURegistro.RIE_clave as cveRiesgo', 'RIE_nombre as nombreRiesgo',
												DB::raw('CONCAT(PUUsu_nombre," ",PUUsu_apaterno," ",PUUsu_amaterno) as nombreUsuario'), 'UNI_nomCorto as nombreUnidad',
												'REG_folioMV', DB::raw('CONCAT("Zima") as registro'), DB::raw('CONCAT(2) as id_registro'))
							 ->Where('REG_nombrecompleto', 'like', '%' . $nombre . '%')
							 ->where('REG_cancelado', '<>', 'S')
							 ->where('REG_fechahora', '>', DB::raw('DATE(DATE_SUB(NOW(), INTERVAL 6 MONTH))'))
							 ->orderBy(DB::raw('REG_nombrecompleto, REG_fechahora'))
							 ->get();
		return $expediente;
	}

	public function buscaDigitalizados()
	{
		$folio = Input::get('folio');
		$idRegistro = Input::get('id_registro');

		if ( $idRegistro == 1 ) {
			$urlTipos = 'http://medicavial.net/mvnuevo/api/api.php?funcion=listaTiposDigitales&usr=sistema';
			$datosURL = file_get_contents($urlTipos);
			$listaTipos = json_decode($datosURL, true);

			$urlDigitales = 'http://medicavial.net/mvnuevo/api/api.php?funcion=digitalizados&fol='.$folio.'&usr=sistema';
			$datosURL = file_get_contents($urlDigitales);
			$listaDigitales = json_decode($datosURL, true);

			// if ($listaDigitales == null) {
			// 	$listaDigitales[] = json_encode(array('respuesta' => 'vacio'));
			// }

			$listaNotaSoap = BusquedasController::notaSoapXpaciente($folio);

			$respuesta = array('tiposDigitales' => $listaTipos,
												 'horaConsulta' 	=> date("Y-m-d H:i:s"),
												 'listaDigitales' => $listaDigitales,
											 	 'notaSoap' 			=> $listaNotaSoap);
		} elseif ( $idRegistro == 2 ) {
			$listaDigitales = DB::connection('zima')
													 ->table('PUArchivo')
													 ->join('TipoArchivo', 'PUArchivo.Arc_tipo', '=', 'TipoArchivo.TipArc_tipo')
													 ->join('PUUsuario', 'PUUsuario.PUUsu_login', '=', 'PUArchivo.USU_login')
													 ->select('PUArchivo.Arc_cons', 'PUArchivo.Arc_clave', 'PUArchivo.REG_folio', 'PUArchivo.Arc_obs', 'PUArchivo.Arc_tipo',
													 					'PUArchivo.Arc_desde', 'PUArchivo.USU_login', 'PUArchivo.Arc_fecreg','TipoArchivo.TipArc_nombre',
																		DB::raw('SUBSTRING(Arc_archivo FROM 13) as Archivo_ruta'))
													 ->Where('REG_folio', $folio)
													 ->get();

			 $respuesta = array('tiposDigitales' 	=> [],
			 										'horaConsulta' 		=> date("Y-m-d H:i:s"),
	 											  'listaDigitales' 	=> $listaDigitales);
		}

		return $respuesta;
	}

	public function notaSoapXpaciente($folio)
	{
		$apiURL = 'http://medicavial.net/mvnuevo/api/notaSoap.php?funcion=notasSOAP&fol='.$folio;
		$datosURL = file_get_contents($apiURL);
		$respuesta = json_decode($datosURL, true);

		return $respuesta;
	}

	public function buscaUnidades()
	{
		$respuesta = DB::table('Unidad')
										->Where('Uni_propia', 'S')
										->Where('Uni_activa', 'S')
										// ->Where('Uni_clave', '<>', 8) //deberiamos quitar la unidad 8
										->orderBy('Uni_nombrecorto', 'asc')
										->get();

		$medici = array('Uni_clave'				=> 1000,
										'Uni_nombrecorto' => 'Medici');

		$particular = array(	'Uni_clave'				=> 2000,
													'Uni_nombrecorto' => 'Consultorio Propio');

		array_push($respuesta, $particular, $medici );

		return $respuesta;
	}

	public function getMedicos()
	{
		$respuesta = DB::table('redQx_usuarios')
										->select('USU_id', 'USU_login', 'USU_email', 'USU_nombreCompleto', 'redQx_usuarios.PER_clave', 'PER_nombre')
										->join('redQx_permisos', 'redQx_usuarios.PER_clave', '=', 'redQx_permisos.PER_clave')
										->where('USU_activo', 1)
										->whereNotIn('redQx_usuarios.PER_clave', [1,2])
										->orderBy(DB::raw('PER_nombre, USU_nombreCompleto'))
										->get();

		return $respuesta;
	}

	public function getUsuarios()
	{
		$respuesta = DB::table('redQx_usuarios')
										->select('USU_id', 'USU_login', 'USU_nombreCompleto','USU_email','redQx_usuarios.PER_clave',
														 'USU_fechaRegistro', 'PER_nombre', 'USU_activo')
										->join('redQx_permisos', 'redQx_usuarios.PER_clave', '=', 'redQx_permisos.PER_clave')
										// ->where('USU_activo', 1)
										// ->whereNotIn('redQx_usuarios.PER_clave', [1,2])
										->whereNotIn('redQx_usuarios.PER_clave', [1])
										// ->orderBy('USU_nombreCompleto')
										->orderBy(DB::raw('PER_nombre, USU_nombreCompleto'))
										->get();

		return $respuesta;
	}

	public function getAsignaciones( $usrLogin )
	{
		$respuesta = DB::table('redQx_asignaciones')
										->select('redQx_asignaciones.*', 'Exp_completo')
										->join('Expediente', 'redQx_asignaciones.Exp_folio', '=', 'Expediente.Exp_folio' )
										->where('ASI_terminada', 0)
										->where('ASI_cancelada', 0)
										->where('USU_loginMedico', $usrLogin)
										->orderBy('ASI_fechaRegistro')
										->get();

		return $respuesta;
	}

	public function pruebaCorreos(  )
	{
		Mail::send('emails.pruebaCorreo', ['key' => 'value'], function($message)
		{
			$message->to('samuel11rr@gmail.com', 'Samus')->subject('Welcome!');
		  $message->from('sramirez@medicavial.com.mx', 'Laravel');
		});

		$respuesta = 'funciona';

		return $respuesta;
	}

	public function getItemsBotiquin( $unidad )
	{
		//medicamentos
		$apiURL = 'http://api.medicavial.mx/api/busquedas/existencias/unidad/'.$unidad.'/1';
		$datosURL = file_get_contents($apiURL);
		$medicamentos = json_decode($datosURL, true);

		//ortesis
		$apiURL = 'http://api.medicavial.mx/api/busquedas/existencias/unidad/'.$unidad.'/2';
		$datosURL = file_get_contents($apiURL);
		$ortesis = json_decode($datosURL, true);

		$combinados = array_merge($medicamentos, $ortesis);

		//filtramos los items con stock 0
		$respuesta = array();
		foreach ($combinados as $dato) {
			if ( $dato['Stock'] > 0 ) {
				$respuesta[] = $dato;
			}
		}

		return $respuesta;
	}

	public function getRecetaAbierta( $folio )
	{
		$idReceta = DB::table('RecetaMedica')
								->where('Exp_folio', '=', $folio)
								->where('RM_terminada', '<>', 1)
								->where('tipo_receta', '=', 6)
								->max('id_receta');

		$items = DB::table('NotaSuministros')
								->where('id_receta', '=', $idReceta)
								->where('NS_surtida', '=', 0)
								->where('NS_cancelado', '=', 0)
								->orderBy(DB::raw('NS_tipoItem, NS_descripcion'))
								->get();
		return $items;
	}

	public function getIndicacionesReceta( $folio )
	{
		$tipoReceta = 6;

		$idReceta = DB::table('RecetaMedica')
									->where('Exp_folio', $folio)
									->where('RM_terminada', '<>', 1)
									->where('tipo_receta', '=', $tipoReceta)
									->max('id_receta');

		$indicaciones = DB::table('NotaIndAlternativa')
												->where('id_receta', '=', $idReceta)
												->get();

		return $indicaciones;
	}

	public function getRecetasXfolio( $folio )
	{
		$tipoReceta = 6;

		$recetasInternas = DB::table('RecetaMedica')
													->select('*', DB::raw('CONCAT(1) as Interna'))
													->where('Exp_folio', $folio)
													->where('RM_terminada', '=', 1)
													->where('tipo_receta', '=', $tipoReceta)
													->get();

		$recetasExternas = DB::table('recetaExterna')
													->select('*', DB::raw('CONCAT(0) as Interna'))
													->where('Exp_folio', $folio)
													->where('RE_terminada', '=', 1)
													->where('RE_fecreg', '>=', DB::raw('DATE(DATE_SUB(NOW(), INTERVAL 2 WEEK))'))
													->get();

		$respuesta = array_merge($recetasInternas, $recetasExternas);
		// $respuesta[] = array ('internas' => $recetasInternas,
		// 										'externas' => $recetasExternas);

		return $respuesta;
	}

	public function getPermisos()
	{
		$respuesta = DB::table('redQx_permisos')
													->select('PER_clave', 'PER_nombre')
													->where('PER_clave', '>', 1)
													->get();
		return $respuesta;
	}

	public function getListadoAsignaciones()
	{
		$respuesta = DB::table('redQx_asignaciones')
										->select('redQx_asignaciones.*', 'Exp_completo', 'Uni_nombrecorto', 'USU_nombreCompleto', 'USU_id')
										->leftJoin('Expediente', 'redQx_asignaciones.Exp_folio', '=', 'Expediente.Exp_folio')
										->leftJoin('Unidad', 'redQx_asignaciones.UNI_clave', '=', 'Unidad.Uni_clave')
										->join('redQx_usuarios', 'redQx_asignaciones.USU_loginMedico', '=', 'redQx_usuarios.USU_login')
										->where('ASI_terminada', '<>', 1)
										->where('ASI_cancelada', '<>', 1)
										->where('ASI_fechaRegistro', '>=', DB::raw('DATE(DATE_SUB(NOW(), INTERVAL 3 MONTH))'))
										->orderBy('ASI_fechaRegistro', 'desc')
										->get();
		return $respuesta;
	}

}
