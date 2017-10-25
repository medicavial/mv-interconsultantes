<?php namespace App\Http\Controllers;

use DB;
use Input;

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
														 'Fol_ZIMA', DB::raw('CONCAT("Médica Vial") as registro'))
										->where('Exp_folio', $folio)
										->orderBy('Exp_completo', 'asc')
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
														 'Fol_ZIMA', DB::raw('CONCAT("Médica Vial") as registro'))
										->Where('Exp_completo', 'like', '%' . $nombre . '%')
										->orderBy('Exp_completo', 'asc')
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
												'REG_folioMV', DB::raw('CONCAT("Zima") as registro'))
							 ->Where('REG_folio',$folio)
							 ->orderBy('REG_nombrecompleto', 'asc')
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
												'REG_folioMV', DB::raw('CONCAT("Zima") as registro'))
							 ->Where('REG_nombrecompleto', 'like', '%' . $nombre . '%')
							 ->orderBy('REG_nombrecompleto', 'asc')
							 ->get();
		return $expediente;
	}

	public function buscaDigitalizados($folio)
	{
		$urlTipos = 'http://medicavial.net/mvnuevo/api/api.php?funcion=listaTiposDigitales&usr=sistema';
		$datosURL = file_get_contents($urlTipos);
		$listaTipos = json_decode($datosURL, true);

		$urlDigitales = 'http://medicavial.net/mvnuevo/api/api.php?funcion=digitalizados&fol='.$folio.'&usr=sistema';
		$datosURL = file_get_contents($urlDigitales);
		$listaDigitales = json_decode($datosURL, true);

		$respuesta = array('tiposDigitales' => $listaTipos,
											 'listaDigitales' => $listaDigitales);
		return $respuesta;
	}

}
