<?php namespace App\Http\Controllers;
/***** Controlador para creación de reportes *****/
/***** Samuel Ramírez - Julio 2018 *****/
// setlocale(LC_ALL,"es_MX");
use DB;
use View;
use Input;
use Mail;
use Response;
use Excel;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ReportesController extends Controller {
	public function index()
	{
		Excel::create('Filename', function($excel) {
			//creación de hoja
			$excel->sheet('prueba 1', function($sheet) {

				// Manipulate first row
				$sheet->row(1, array(
				     'test1', 'test2'
				));
				$sheet->row(1, function($row) {
				    $row->setBackground('#ff0000');
				});
				//creacion de segunda fila
				$sheet->row(2, array(
				    'test3', 'test4'
				));
				// Append multiple rows
				$sheet->rows(array(
				    array('test5', 'test6'),
				    array('test7', 'test8')
				));
				// Append row after row 2
				$sheet->appendRow(2, array(
				    'reemplaza', 'reemplaza'
				));
				// Append row as very last
				$sheet->appendRow(array(
				    'alFinal', 'alFinal'
				));
				// Add before first row
				$sheet->prependRow(1, array(
				    'determinado', 'determinado'
				));
				// Add as very first
				$sheet->prependRow(array(
				    'principio', 'principio'
				));
				$sheet->cell('C1', function($cell) {

				    // manipulate the cell
				    $cell->setValue('data1');

				});

				$sheet->cells('A1:A5', function($cells) {
						$cells->setBackground('#007777');
						$cells->setFontColor('#ffffff');
						// $cells->setFontSize(16);
						// $cells->setFontFamily('Calibri');
						$cells->setFont(array(
						    'family'    => 'Calibri',
						    'size'      => '16',
						    'bold'      =>  true
						));
						// Set all borders (top, right, bottom, left)
						$cells->setBorder('none', 'solid', 'solid', 'none');
						// $cells->setBorder(array(
						//     'bottom'   => array(
						//         'style' => 'solid'
						//     ),
						// ));
						$cells->setAlignment('center');
						$cells->setValignment('center');
				});

	    });
			//creación de hoja
			$excel->sheet('Otra', function($sheet) {

				// Set black background
				$sheet->row(1, function($row) {

				    // call cell manipulation methods
				    $row->setBackground('#ff0000');

				});

	    });
		})->download('xlsx');
		// ->export('xls');
		// ->export('pdf');
		// ->download('xls');
	}

	public function getDatos(){
		$idUsuario = 1;
		$listado = DB::connection('externos')
							 ->table('pases')
							 ->select('pases.*', DB::raw( 'CONCAT( PAS_nombre, " ", PAS_aPaterno, " ", PAS_aMaterno ) as nombreCompleto' ),
							 					'EAT_nombre', 'EAT_alias', 'EAD_nombre', 'EAD_alias',
												DB::raw( 'if( PAS_id < 10, concat(PAS_clave,"00", PAS_id), if( PAS_id < 100, concat(PAS_clave, "0", PAS_id), concat(PAS_clave, PAS_id) ) ) as claveOrden' ),
							 					DB::raw( 'DATEDIFF( now(), PAS_fechaAlta ) as dias' ),
												DB::raw( 'DATE_FORMAT(PAS_fechaAlta, "%d-%m-%Y") as fechaFacil' ),
												DB::raw( 'if( datediff( now(), PAS_fechaAlta)<5, "0 a 5 días", if( datediff( now(), PAS_fechaAlta) between 5 and 10, "5 a 10 días", if( datediff( now(), PAS_fechaAlta) between 10 and 30, "10 a 30 días", if( datediff( now(), PAS_fechaAlta) between 30 and 90, "30 a 90 días", if( datediff( now(), PAS_fechaAlta) > 90, "mas de 90 días", "N/A" ) ) ) ) ) as estatusDias' ))
							 ->leftJoin('estatusAdministrativo', 'pases.EAD_id', '=', 'estatusAdministrativo.EAD_id')
							 ->leftJoin('estatusAtencion', 'pases.EAT_id', '=', 'estatusAtencion.EAT_id')
							 ->orderBy('PAS_fechaAlta', 'desc');
							 if ( $idUsuario > 1 && $idUsuario != 13 ) {
							 	$listado->where('USU_id', $idUsuario);
								$listado->where('PAS_fechaMod', '>', DB::raw('DATE(DATE_SUB(NOW(), INTERVAL 2 MONTH))'));
								$listado->limit(100);
							} elseif ( $idUsuario == 1 || $idUsuario == 13 ) {
								$listado->limit(1500);
							}
		$datos = $listado->get();
		return $datos;
	}

	public function repDatos(){
		$datos = ReportesController::getDatos();

		Excel::create('datos', function($excel) use( $datos ){
			$excel->sheet('pases', function($sheet) use($datos) {

				// // CREAMOS EL CUERPO
				foreach ($datos as $fila) {
					$variables = (array) get_object_vars($fila);
					$sheet->prependRow($variables);
					$sheet->row(1, function($row) {
					    $row->setBackground('#FFFFFF');
							$row->setBorder('thin', 'thin', 'thin', 'thin');
							$row->setFont(array(
							    'family'    => 'Arial',
							    'size'      => '10'
							));
					});
					$sheet->cells('A1',function($cells) {
					    $cells->setBorder('thin', 'thin', 'thin', 'thin');
					});
				}

				// CREAMOS LOS ENCABEZADOS
				foreach ($datos as $fila) {
					// DATOS
					$variables = array_keys(get_object_vars($fila));
					$sheet->prependRow($variables);
					// ESTILOS
					$sheet->row(1, function($row) {
					    $row->setBackground('#0082cd');
							$row->setFontColor('#ffffff');
							$row->setFont(array(
							    'family'    => 'Arial',
							    'size'      => '12',
							    'bold'      =>  true
							));
							$row->setAlignment('center');
							$row->setValignment('center');
							$row->setBorder('thin', 'thin', 'thin', 'thin');
					});
					break;
				}
			});
		})->download('xlsx');
	}

	public function connectionTest(){
		$newLocale = setlocale(LC_TIME, 'Spanish');

		$query = "select A.Cliente, count(Cliente) as 'CantidadCliente',";

		//TOTALES POR CLIENTE AÑO ANTERIOR
		$query.= "ISNULL(
							 (SELECT count(Cliente)
							 from MVReportes.dbo.ListadoOperativo B
							 where B.Cliente = A.Cliente
							 and FCaptura < '".date('Y')."'
							 group by Cliente),0
							) as 'y". (date('Y')-1) ."',";

		//TOTALES POR CLIENTE MES - AÑO ACTUAL
		for ($i=1; $i <= 12 ; $i++) {
			if ($i < 12 ) {
				$query.="
					ISNULL(
					 (SELECT count(Cliente)
					 from MVReportes.dbo.ListadoOperativo C
					 where C.Cliente = A.Cliente
					 and FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121)
					 group by Cliente), 0
					) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."',
				";
			} elseif($i == 12)
				$query.="
					ISNULL(
					 (SELECT count(Cliente)
					 from MVReportes.dbo.ListadoOperativo C
					 where C.Cliente = A.Cliente
					 and FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121)
					 group by Cliente), 0
					) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."',
				";
		}

		//TOTALES POR CLIENTE AÑO ACTUAL
		$query.= "ISNULL(
							 (SELECT count(Cliente)
							 from MVReportes.dbo.ListadoOperativo B
							 where B.Cliente = A.Cliente
							 and FCaptura >= '".date('Y')."'
							 group by Cliente),0
							) as 'y". date('Y') ."'";

		$query.=" from MVReportes.dbo.ListadoOperativo A
							 group by Cliente
							 order by Cliente asc";

		$clientes =  DB::connection( 'mvlocal' )->select( DB::raw($query) );
		// return $clientes;

		$queryTotales ="SELECT ISNULL(COUNT(*), 0) as 'TOTALES',
										ISNULL((SELECT count(*) from MVReportes.dbo.ListadoOperativo where FCaptura < '".(date('Y'))."' ), 0) as 'total".(date('Y')-1)."',";

		for ($i=1; $i <= 12 ; $i++) {
			if ($i < 12 ) {
				$queryTotales.="ISNULL(
													( SELECT count(*)
														from MVReportes.dbo.ListadoOperativo
														where FCaptura between CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121)
														and CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121) ), 0
													) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."',";
			} elseif($i == 12){
				$queryTotales.="ISNULL(
													( SELECT count(*)
														from MVReportes.dbo.ListadoOperativo
														where FCaptura between CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121)
														and CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121) ), 0
													) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."',";
			}
		}
		$queryTotales.="ISNULL((SELECT count(*) from MVReportes.dbo.ListadoOperativo where FCaptura >= '".(date('Y'))."' ), 0) as 'total".(date('Y'))."',
										ISNULL(CEILING(count(*) * 0.001), 0) as 'minimo'
										from MVReportes.dbo.ListadoOperativo";

		$totales = DB::connection( 'mvlocal' )
									 ->select( DB::raw($queryTotales));

		$queryTop ="SELECT A.Cliente, B.Producto, '1' as Prioridad,";
		/*totales por producto año anterior*/
		$queryTop.="ISNULL(
							   (SELECT count(Producto)
									from MVReportes.dbo.ListadoOperativo
									where Producto = B.Producto
									and Cliente = A.Cliente
									and FCaptura < '".(date('Y'))."'),0
						   	) as 'y". (date('Y')-1) ."',";
		/*totales por producto por mes*/
	  for ($i=1; $i <= 12 ; $i++) {
			if ($i < 12 ) {
				$queryTop.="ISNULL(
									   (SELECT count(Producto)
											from MVReportes.dbo.ListadoOperativo
											where Producto = B.Producto
											and Cliente = A.Cliente
											and FCaptura between CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121)
											and CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121) ), 0
										) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."',";
			}elseif($i == 12){
				$queryTop.="ISNULL(
									   (SELECT count(Producto)
											from MVReportes.dbo.ListadoOperativo
											where Producto = B.Producto
											and Cliente = A.Cliente
											and FCaptura between CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121)
											and CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121) ), 0
										) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."',";
			}
		}
		/*totales por producto año actual*/
		$queryTop.="ISNULL(
								 (SELECT count(Producto)
									from MVReportes.dbo.ListadoOperativo
									where Producto = B.Producto
									and Cliente = A.Cliente
									and FCaptura >= '".(date('Y'))."'),0
						   	) as 'y". date('Y') ."',";
		/*totales por producto total*/
		$queryTop.="count(B.Producto) as total ";
		$queryTop.="from MVReportes.dbo.ListadoOperativo A
								inner join MVReportes.dbo.ListadoOperativo B on A.Id = B.Id
								where A.Cliente in ( SELECT top 5 Cliente from MVReportes.dbo.ListadoOperativo B group by Cliente order by count(Cliente) desc )
								group by B.Producto, A.Cliente ";
		$queryTop.="union ";
		$queryTop.="SELECT A.Cliente, 'total' as Producto, '2' as Prioridad,";
		/*totales por compania año anterior*/
		$queryTop.="ISNULL(
							   (SELECT count(Cliente)
									from MVReportes.dbo.ListadoOperativo
									where Cliente = A.Cliente
									and FCaptura < '".(date('Y'))."'),0
						   	) as 'y". (date('Y')-1) ."',";
		/*totales por compania por mes*/
		for ($i=1; $i <= 12 ; $i++) {
			if ($i < 12 ) {
				$queryTop.="ISNULL(
									   (SELECT count(Cliente)
											from MVReportes.dbo.ListadoOperativo
											where Cliente = A.Cliente
											and FCaptura between CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121)
											and CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121) ), 0
										) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."',";
			}elseif($i == 12){
				$queryTop.="ISNULL(
									   (SELECT count(Cliente)
											from MVReportes.dbo.ListadoOperativo
											where Cliente = A.Cliente
											and FCaptura between CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121)
											and CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121) ), 0
										) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."',";
			}
		}
		/*totales por compañia año actual*/
		$queryTop.="ISNULL(
								 (SELECT count(Cliente)
									from MVReportes.dbo.ListadoOperativo
									where Cliente = A.Cliente
									and FCaptura >= '".(date('Y'))."'),0
						   	) as 'y". date('Y') ."',";
		$queryTop.="count(B.Producto) as total
								from MVReportes.dbo.ListadoOperativo A
								inner join MVReportes.dbo.ListadoOperativo B on A.Id = B.Id
								where A.Cliente in ( SELECT top 5 Cliente from MVReportes.dbo.ListadoOperativo B group by Cliente order by count(Cliente) desc )
								group by  A.Cliente
								order by A.Cliente desc, Prioridad asc, count(B.Producto) desc";

		$top =  DB::connection( 'mvlocal' )->select( DB::raw($queryTop) );

		// return $top;
		// return ReportesController::totalesXlocalidades();
		return array('totales'			=> $totales,
								 'clientes' 		=> $clientes,
							 	 'top5' 				=> $top);
	}

	public function totalesXlocalidades(){
		$newLocale = setlocale(LC_TIME, 'Spanish');

		$query = "SELECT A.Localidad, '1' as prioridad, count(Localidad) as Total, ";
		//TOTALES POR COMPAÑIA POR LOCALIDAD AÑO ANTERIOR
		$query.="ISNULL(
					   (SELECT count(Localidad)
							from MVReportes.dbo.ListadoOperativo B
							where B.Localidad = A.Localidad
							and FCaptura < '".date('Y')."'
							group by Localidad),0
				   	) as 'y". (date('Y')-1) ."', ";
		/*TOTALES POR COMPAÑIA POR LOCALIDAD POR MES*/
		for ($i=1; $i <= 12 ; $i++) {
			if ($i < 12 ) {
				$query.="ISNULL(
										 (SELECT count(Localidad)
							  			from MVReportes.dbo.ListadoOperativo C
							  			where C.Localidad = A.Localidad
											and FCaptura between CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121)
											and CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121)
										 	group by Localidad), 0
										) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
			}elseif($i == 12){
				$query.="ISNULL(
										 (SELECT count(Localidad)
										 	from MVReportes.dbo.ListadoOperativo C
										 	where C.Localidad = A.Localidad
											and FCaptura between CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121)
											and CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121)
										 	group by Localidad), 0
										) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
			}
		}
		//TOTALES POR COMPAÑIA POR LOCALIDAD AÑO ACTUAL
		$query.="ISNULL(
					   (SELECT count(Localidad)
							from MVReportes.dbo.ListadoOperativo B
							where B.Localidad = A.Localidad
							and FCaptura >= '".date('Y')."'
							group by Localidad),0
				   	) as 'y". date('Y') ."' ";
		$query.="from MVReportes.dbo.ListadoOperativo A
						 group by Localidad
						 union ";
		$query.="SELECT 'TOTAL GENERAL' as Localidad, '2' as prioridad, count(*) as Total, ";
		//TOTAL GENERAL POR LOCALIDAD AÑO ANTERIOR
		$query.="ISNULL(
						 (SELECT count(Localidad) from MVReportes.dbo.ListadoOperativo B
							where FCaptura < '".date('Y')."'
						 ), 0
						) as 'y". (date('Y')-1) ."', ";
		/*TOTALES GENERALES POR LOCALIDAD POR MES*/
		for ($i=1; $i <= 12 ; $i++) {
			if ($i < 12 ) {
				$query.="ISNULL(
										 (SELECT count(Localidad) from MVReportes.dbo.ListadoOperativo C
							  			where FCaptura between CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121)
											and CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121)
										 ), 0
										) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
			}elseif($i == 12){
				$query.="ISNULL(
										 (SELECT count(Localidad) from MVReportes.dbo.ListadoOperativo C
										 	where FCaptura between CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121)
											and CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121)
										 ), 0
										) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
			}
		}
		//TOTALES GENERALES POR LOCALIDAD AÑO ACTUAL
		$query.="ISNULL(
						 (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
							where FCaptura >= '".date('Y')."'
						 ),0
				   	) as 'y". date('Y') ."' ";
		$query.="from MVReportes.dbo.ListadoOperativo A
						order by prioridad asc, count(Localidad) desc";

		return DB::connection( 'mvlocal' )->select( DB::raw($query) );
	}

	public function localidadClinicaPropia(){
		$newLocale = setlocale(LC_TIME, 'Spanish');
		//listado ordenado de zonas
		$query = "SELECT A.Localidad
							from MVReportes.dbo.ListadoOperativo A
							where A.Localidad in (SELECT top 1 Localidad
									from MVReportes.dbo.ListadoOperativo C
									where C.Unidad like '%mv%'
									and C.Localidad = A.Localidad)
							group by A.Localidad
							order by count(A.Localidad) desc";
		$resQuery = DB::connection( 'mvlocal' )->select( DB::raw($query) );

		$queryDatos = "";
		$contadorLoc = 1;
		$localidades = "";

		foreach ($resQuery as $locNombre) {
			if ( $locNombre->Localidad != $resQuery[sizeof($resQuery)-1]->Localidad ) {
				$localidades .= "'".$locNombre->Localidad."',";
			}elseif( $locNombre->Localidad == $resQuery[sizeof($resQuery)-1]->Localidad ){
				$localidades .= "'".$locNombre->Localidad."'";
			}
		}

		foreach ($resQuery as $dato) {
			//select para localidades con unidades propias
			if ( $dato->Localidad == 'Cd. Mex. (Z. Metro)') {
				$queryDatos .= "SELECT '".$dato->Localidad."' as loc, A.Unidad, '".$contadorLoc."' as priLoc, '1' as prioridad, count(Unidad) as CantidadUnidad, ";
			}elseif( $dato->Localidad != 'Cd. Mex. (Z. Metro)' ){
				$queryDatos .= "SELECT '".$dato->Localidad."' as loc, 'Propias foráneas' as Unidad, '".$contadorLoc."' as priLoc, '1' as prioridad, count(Unidad) as CantidadUnidad, ";
			}
							$queryDatos.= "ISNULL( (SELECT count(Unidad) from MVReportes.dbo.ListadoOperativo B
																			where B.Unidad = A.Unidad
																			and FCaptura < '". date('Y') ."'
																			group by Unidad), 0 ) as 'y". (date('Y')-1) ."',
													   ISNULL( (SELECT count(Unidad) from MVReportes.dbo.ListadoOperativo B
																			where B.Unidad = A.Unidad
																			and FCaptura between CONVERT(datetime, '".(date('Y')-1)."-12-01 00:00:00', 121) and CONVERT(datetime, '".date('Y')."-01-01 00:00:00', 121)
																			group by Unidad), 0 ) as 'dic".(date('Y')-1)."', ";
					for ($i=1; $i <= 12 ; $i++) {
						if ($i < 12 ) {
							$queryDatos .="ISNULL( (SELECT count(Unidad) from MVReportes.dbo.ListadoOperativo C
																			where C.Unidad = A.Unidad
																			and FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121)
																			group by Unidad), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
						}elseif($i == 12){
							$queryDatos .="ISNULL( (SELECT count(Unidad) from MVReportes.dbo.ListadoOperativo C
																			where C.Unidad = A.Unidad
																			and FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121)
																			group by Unidad), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
						}
					}
							$queryDatos.= "ISNULL( (SELECT count(Unidad) from MVReportes.dbo.ListadoOperativo C
																			where C.Unidad = A.Unidad
																			and FCaptura >= '". date('Y') ."'
																			group by Unidad), 0 ) as 'y". date('Y') ."'
											from MVReportes.dbo.ListadoOperativo A
											where Unidad like '%mv%'
											and A.Localidad = '". $dato->Localidad ."'
											group by Unidad

											UNION

											SELECT '".$dato->Localidad."' as loc, 'Red' as Unidad, '".$contadorLoc."' as priLoc, '2' as prioridad, count(*) as CantidadUnidad,
													   ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo B
																			where FCaptura < '". date('Y') ."'
																			and Localidad = '". $dato->Localidad ."'
																			and Unidad NOT LIKE '%mv%'), 0 ) as 'y". (date('Y')-1) ."',
													   ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo B
																			where FCaptura between CONVERT(datetime, '".(date('Y')-1)."-12-01 00:00:00', 121) and CONVERT(datetime, '".date('Y')."-01-01 00:00:00', 121)
																			and Localidad = '". $dato->Localidad ."'
																			and Unidad NOT LIKE '%mv%'), 0 ) as 'dic".(date('Y')-1)."', ";
					for ($i=1; $i <= 12 ; $i++) {
						if ($i < 12 ) {
							$queryDatos .="ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
																			where FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121)
																			and Localidad = '". $dato->Localidad ."'
																			and Unidad NOT LIKE '%mv%'), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
						}elseif($i == 12){
							$queryDatos .="ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
																			where FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121)
																			and Localidad = '". $dato->Localidad ."'
																			and Unidad NOT LIKE '%mv%'), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
						}
					}
							$queryDatos.= "ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
																			where FCaptura >= '". date('Y') ."'
																			and Localidad = '". $dato->Localidad ."'
																			and Unidad NOT LIKE '%mv%'), 0 ) as 'y". date('Y') ."'
											from MVReportes.dbo.ListadoOperativo A
											where Unidad NOT LIKE '%mv%'
											and Localidad = '". $dato->Localidad ."'

											UNION

											SELECT '".$dato->Localidad."' as loc, 'total' as Unidad, '".$contadorLoc."' as priLoc, '3' as prioridad, count(*) as CantidadUnidad,
													   ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo B
																			where FCaptura < '". date('Y') ."'
																			and Localidad = '". $dato->Localidad ."'), 0 ) as 'y". (date('Y')-1) ."',
													   ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo B
																			where FCaptura between CONVERT(datetime, '".(date('Y')-1)."-12-01 00:00:00', 121) and CONVERT(datetime, '".date('Y')."-01-01 00:00:00', 121)
																			and Localidad = '". $dato->Localidad ."'), 0 ) as 'dic".(date('Y')-1)."', ";
					for ($i=1; $i <= 12 ; $i++) {
						if ($i < 12 ) {
							$queryDatos .="ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
																			where FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121)
																			and Localidad = '". $dato->Localidad ."'), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
						}elseif($i == 12){
							$queryDatos .="ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
																			where FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121)
																			and Localidad = '". $dato->Localidad ."'), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
						}
					}
							$queryDatos.= "ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
																			where FCaptura >= '". date('Y') ."'
																			and Localidad = '". $dato->Localidad ."'), 0 ) as 'y". date('Y') ."'
											from MVReportes.dbo.ListadoOperativo A
											where Localidad = '". $dato->Localidad ."'
											";

					if ( $resQuery[ sizeof($resQuery)-1 ]->Localidad != $dato->Localidad ) {
						$queryDatos.="UNION
												 ";
					}
					$contadorLoc++;
		} // aqui termina foreach por cada localidad

		// totales de propias
		$queryDatos.="UNION

									SELECT 'totalPropias' as loc, 'totalPropias' as Unidad, '".$contadorLoc."' as priLoc, '1' as prioridad, count(*) as CantidadUnidad,
												 ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo B
																	where FCaptura < '". date('Y') ."'
																	and Unidad like '%mv%'), 0 ) as 'y". (date('Y')-1) ."',
												 ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo B
																	where FCaptura between CONVERT(datetime, '".(date('Y')-1)."-12-01 00:00:00', 121) and CONVERT(datetime, '".date('Y')."-01-01 00:00:00', 121)
																	and Unidad like '%mv%'), 0 ) as 'dic".(date('Y')-1)."', ";
				for ($i=1; $i <= 12 ; $i++) {
					if ($i < 12 ) {
					$queryDatos .="ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
															where FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121)
															and Unidad like '%mv%'), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
					}elseif($i == 12){
					$queryDatos .="ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
															where FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121)
															and Unidad like '%mv%'), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
					}
				}
					$queryDatos.= "ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
															where FCaptura >= '". date('Y') ."'
															and Unidad like '%mv%'), 0 ) as 'y". date('Y') ."'
							from MVReportes.dbo.ListadoOperativo A
							where Unidad like '%mv%'
							";

		// totales de red
		$queryDatos.="UNION

									SELECT 'totalRed' as loc, 'totalRed' as Unidad, '".$contadorLoc."' as priLoc, '2' as prioridad, count(*) as CantidadUnidad,
												 ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo B
																	where FCaptura < '". date('Y') ."'
																	and Unidad NOT LIKE '%mv%'
																	and Localidad in (".$localidades.")), 0 ) as 'y". (date('Y')-1) ."',
												 ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo B
																	where FCaptura between CONVERT(datetime, '".(date('Y')-1)."-12-01 00:00:00', 121) and CONVERT(datetime, '".date('Y')."-01-01 00:00:00', 121)
																	and Localidad in (".$localidades.")
																), 0 ) as 'dic".(date('Y')-1)."', ";
				for ($i=1; $i <= 12 ; $i++) {
					if ($i < 12 ) {
					$queryDatos .="ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
															where FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121)
															and Localidad in (".$localidades.")
															and Unidad NOT LIKE '%mv%'), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
					}elseif($i == 12){
					$queryDatos .="ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
															where FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121)
															and Localidad in (".$localidades.")
															and Unidad NOT LIKE '%mv%'), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
					}
				}
					$queryDatos.= "ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
															where FCaptura >= '". date('Y') ."'
															and Localidad in (".$localidades.")
															and Unidad NOT LIKE '%mv%'), 0 ) as 'y". date('Y') ."'
							from MVReportes.dbo.ListadoOperativo A
							where Localidad in (".$localidades.") and Unidad NOT LIKE '%mv%'
							";

		// totales generales
		$queryDatos.="UNION

									SELECT 'totalGeneral' as loc, 'totalGeneral' as Unidad, '".$contadorLoc."' as priLoc, '3' as prioridad, count(*) as CantidadUnidad,
												 ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo B
																	where FCaptura < '". date('Y') ."'
																	and Localidad in (".$localidades.")), 0 ) as 'y". (date('Y')-1) ."',
												 ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo B
																	where FCaptura between CONVERT(datetime, '".(date('Y')-1)."-12-01 00:00:00', 121) and CONVERT(datetime, '".date('Y')."-01-01 00:00:00', 121)
																	and Localidad in (".$localidades.")
																), 0 ) as 'dic".(date('Y')-1)."', ";
				for ($i=1; $i <= 12 ; $i++) {
					if ($i < 12 ) {
					$queryDatos .="ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
															where FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".date('Y')."-".($i+1)."-01 00:00:00', 121)
															and Localidad in (".$localidades.")), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
					}elseif($i == 12){
					$queryDatos .="ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
															where FCaptura >= CONVERT(datetime, '".date('Y')."-".$i."-01 00:00:00', 121) and FCaptura < CONVERT(datetime, '".(date('Y')+1)."-".($i-11)."-01 00:00:00', 121)
															and Localidad in (".$localidades.")), 0 ) as '". strtoupper( strftime('%b', strtotime(date('Y')."-".$i)) ) ."', ";
					}
				}
					$queryDatos.= "ISNULL( (SELECT count(*) from MVReportes.dbo.ListadoOperativo C
															where FCaptura >= '". date('Y') ."'
															and Localidad in (".$localidades.")), 0 ) as 'y". date('Y') ."'
							from MVReportes.dbo.ListadoOperativo A
							where Localidad in (".$localidades.")
							";

		$queryDatos.="ORDER BY priLoc ASC, prioridad ASC, COUNT(Unidad) DESC";

		// return $queryDatos;
		$resDatos = DB::connection( 'mvlocal' )->select( DB::raw($queryDatos) );
		// return $resDatos;
		return array('localidades' => $resQuery, 'datos' => $resDatos);
	}

	public function repTemplate(){
		// return ReportesController::localidadClinicaPropia();
		$todo = ReportesController::connectionTest();
		$datos = $todo['clientes'];
		$variables = array_keys(get_object_vars($datos[0]));

		$data = array('datos' 		=> $datos,
									'variables'	=> $variables,
									'totales' 	=> $todo['totales'],
									'top'				=> $todo['top5']);

		Excel::create('template', function($excel) use($data) {
		    $excel->sheet('1', function($sheet)  use($data){
					//configuramos hoja
					$sheet->setPaperSize('letter');
					$sheet->setOrientation('landscape');

					$sheet->mergeCells('A1:O1');
					$sheet->cells('A1:O1', function($cells) {
							$cells->setBackground('#ffffff');
					});

					$sheet->mergeCells('A2:O2');
					$sheet->cells('A2:O2', function($cells) {
							$cells->setBackground('#ffffff');
							$cells->setFontColor('#000000');
							$cells->setFont(array(
							    'family'    => 'Arial',
							    'size'      => '26',
							    'bold'      =>  true
							));
							// $cells->setBorder('none', 'solid', 'solid', 'none');
							$cells->setAlignment('center');
							$cells->setValignment('center');
					});
					$sheet->row(2, array(
					     'Lesionados por aseguradora / Mes captura (Solo 1ra Atención)',
					));

					//encabezados de la tabla
					// $sheet->setWidth('A', 68);
					$sheet->setWidth(array( 'A'=>60, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20, 'H'=>20,
																	'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20, 'O'=>20 ));
					$sheet->mergeCells('A3:A4');
					$sheet->cell('A3', function($cell) {
					    $cell->setValue('Cliente');
					});
					$sheet->cell('B3', function($cell) {
					    $cell->setValue('Total');
					});
					$sheet->mergeCells('C3:N3');
					$sheet->cell('C3', function($cell) {
					    $cell->setValue(date('Y'));
					});
					$sheet->cell('O3', function($cell) {
					    $cell->setValue('Total');
					});
					$sheet->cells('A3:O3', function($cells) {
							$cells->setBackground('#AAAAAA');
							$cells->setFontColor('#000000');
							$cells->setFont(array(
							    'family'    => 'Arial',
							    'size'      => '20',
							    'bold'      =>  true
							));
							// $cells->setBorder('none', 'solid', 'solid', 'none');
							$cells->setAlignment('center');
							$cells->setValignment('center');
					});
					$sheet->cells('A4:O4', function($cells) {
							$cells->setBackground('#AAAAAA');
							$cells->setFontColor('#000000');
							$cells->setFont(array(
									'family'    => 'Arial',
									'size'      => '20',
									'bold'      =>  true
							));
							$cells->setAlignment('center');
							$cells->setValignment('center');
					});
					$sheet->cell('B4', function($cell) { $cell->setValue( (date('Y')-1) ); });
					$sheet->cell('C4', function($cell) { $cell->setValue('ENE'); });
					$sheet->cell('D4', function($cell) { $cell->setValue('FEB'); });
					$sheet->cell('E4', function($cell) { $cell->setValue('MAR'); });
					$sheet->cell('F4', function($cell) { $cell->setValue('ABR'); });
					$sheet->cell('G4', function($cell) { $cell->setValue('MAY'); });
					$sheet->cell('H4', function($cell) { $cell->setValue('JUN'); });
					$sheet->cell('I4', function($cell) { $cell->setValue('JUL'); });
					$sheet->cell('J4', function($cell) { $cell->setValue('AGO'); });
					$sheet->cell('K4', function($cell) { $cell->setValue('SEP'); });
					$sheet->cell('L4', function($cell) { $cell->setValue('OCT'); });
					$sheet->cell('M4', function($cell) { $cell->setValue('NOV'); });
					$sheet->cell('N4', function($cell) { $cell->setValue('DIC'); });
					$sheet->cell('O4', function($cell) { $cell->setValue( date('Y') ); });
					// $sheet->loadView('excel.prueba', $data);
					// $sheet->loadView('reportes.operativo', $data);

					//datos
					$fila = 5;
					$anioActual = 'y'.date('Y');
					$anioAnterior = 'y'.((date('Y'))-1);

					foreach ($data['datos'] as $dato) {
						// if ($dato->CantidadCliente > $data['totales'][0]->minimo || $dato->Cliente == 'PARTICULARES') {
							//formato de fila
							$sheet->row($fila, function($row) use($fila) {
									if ($fila%2 == 0 ) $row->setBackground('#FFFFFF');
									if ($fila%2 != 0 ) $row->setBackground('#E0E0E0');
							});
							$sheet->cells('A'.$fila.':'.'O'.$fila, function($cells) use($fila){
								if($fila == 5) $cells->setBorder('thin', 'none', 'none', 'none');
								$cells->setAlignment('center');
								$cells->setValignment('center');
								$cells->setFontFamily('Arial');
								$cells->setFontSize(20);
							});
							$sheet->cell('A'.$fila, function($cell) use($dato) { $cell->setAlignment('left'); $cell->setValue($dato->Cliente); });
							$sheet->cell('B'.$fila, function($cell) use($dato, $anioAnterior) { $cell->setValue($dato->$anioAnterior); });
							$sheet->cell('C'.$fila, function($cell) use($dato) { if($dato->ENE > 0) $cell->setValue($dato->ENE); });
							$sheet->cell('D'.$fila, function($cell) use($dato) { if($dato->FEB > 0) $cell->setValue($dato->FEB); });
							$sheet->cell('E'.$fila, function($cell) use($dato) { if($dato->MAR > 0) $cell->setValue($dato->MAR); });
							$sheet->cell('F'.$fila, function($cell) use($dato) { if($dato->ABR > 0) $cell->setValue($dato->ABR); });
							$sheet->cell('G'.$fila, function($cell) use($dato) { if($dato->MAY > 0) $cell->setValue($dato->MAY); });
							$sheet->cell('H'.$fila, function($cell) use($dato) { if($dato->JUN > 0) $cell->setValue($dato->JUN); });
							$sheet->cell('I'.$fila, function($cell) use($dato) { if($dato->JUL > 0) $cell->setValue($dato->JUL); });
							$sheet->cell('J'.$fila, function($cell) use($dato) { if($dato->AGO > 0) $cell->setValue($dato->AGO); });
							$sheet->cell('K'.$fila, function($cell) use($dato) { if($dato->SEP > 0) $cell->setValue($dato->SEP); });
							$sheet->cell('L'.$fila, function($cell) use($dato) { if($dato->OCT > 0) $cell->setValue($dato->OCT); });
							$sheet->cell('M'.$fila, function($cell) use($dato) { if($dato->NOV > 0) $cell->setValue($dato->NOV); });
							$sheet->cell('N'.$fila, function($cell) use($dato) { if($dato->DIC > 0) $cell->setValue($dato->DIC); });
							$sheet->cell('O'.$fila, function($cell) use($dato, $anioActual) { $cell->setFontWeight('bold'); $cell->setValue($dato->$anioActual); });

							$fila++;
						// } // cierra if
					}
					$totalAnioActual = 'total'.date('Y');
					$totalAnioAnterior = 'total'.((date('Y'))-1);
					$sheet->row($fila, function($row) use($fila) {
							if ($fila%2 == 0 ) $row->setBackground('#FFFFFF');
							if ($fila%2 != 0 ) $row->setBackground('#E0E0E0');
					});
					$sheet->cells('A'.$fila.':'.'O'.$fila, function($cells) use($fila){
						$cells->setBorder('thin', 'none', 'none', 'none');
						$cells->setAlignment('center');
						$cells->setValignment('center');
						$cells->setFontFamily('Arial');
						$cells->setFontWeight('bold');
						$cells->setFontSize(20);
					});
					$sheet->cell('A'.$fila, function($cell) use($data) { $cell->setAlignment('left'); $cell->setValue( 'TOTAL GENERAL' ); });
					$sheet->cell('B'.$fila, function($cell) use($data, $totalAnioAnterior) { $cell->setValue( $data['totales'][0]->$totalAnioAnterior); });
					$sheet->cell('C'.$fila, function($cell) use($data) { if($data['totales'][0]->ENE > 0) $cell->setValue($data['totales'][0]->ENE); });
					$sheet->cell('D'.$fila, function($cell) use($data) { if($data['totales'][0]->FEB > 0) $cell->setValue($data['totales'][0]->FEB); });
					$sheet->cell('E'.$fila, function($cell) use($data) { if($data['totales'][0]->MAR > 0) $cell->setValue($data['totales'][0]->MAR); });
					$sheet->cell('F'.$fila, function($cell) use($data) { if($data['totales'][0]->ABR > 0) $cell->setValue($data['totales'][0]->ABR); });
					$sheet->cell('G'.$fila, function($cell) use($data) { if($data['totales'][0]->MAY > 0) $cell->setValue($data['totales'][0]->MAY); });
					$sheet->cell('H'.$fila, function($cell) use($data) { if($data['totales'][0]->JUN > 0) $cell->setValue($data['totales'][0]->JUN); });
					$sheet->cell('I'.$fila, function($cell) use($data) { if($data['totales'][0]->JUL > 0) $cell->setValue($data['totales'][0]->JUL); });
					$sheet->cell('J'.$fila, function($cell) use($data) { if($data['totales'][0]->AGO > 0) $cell->setValue($data['totales'][0]->AGO); });
					$sheet->cell('K'.$fila, function($cell) use($data) { if($data['totales'][0]->SEP > 0) $cell->setValue($data['totales'][0]->SEP); });
					$sheet->cell('L'.$fila, function($cell) use($data) { if($data['totales'][0]->OCT > 0) $cell->setValue($data['totales'][0]->OCT); });
					$sheet->cell('M'.$fila, function($cell) use($data) { if($data['totales'][0]->NOV > 0) $cell->setValue($data['totales'][0]->NOV); });
					$sheet->cell('N'.$fila, function($cell) use($data) { if($data['totales'][0]->DIC > 0) $cell->setValue($data['totales'][0]->DIC); });
					$sheet->cell('O'.$fila, function($cell) use($data, $totalAnioActual) { $cell->setFontWeight('bold'); $cell->setValue( $data['totales'][0]->$totalAnioActual ); });

					$fila++;

					$sheet->row($fila, function($row) use($fila) { $row->setBackground('#FFFFFF'); });
					$fila++;
					$sheet->row($fila, function($row) use($fila) { $row->setBackground('#FFFFFF'); });
					$fila++;

					//totales producto/compania
					$sheet->cells('A'.$fila.':'.'O'.$fila, function($cells) {
							$cells->setBackground('#AAAAAA');
							$cells->setFontColor('#000000');
							$cells->setFont(array(
									'family'    => 'Arial',
									'size'      => '20',
									'bold'      =>  true
							));
							$cells->setAlignment('center');
							$cells->setValignment('center');
					});
					$sheet->cell('A'.$fila, function($cell) use($data) { $cell->setValue( 'Cliente/Producto' ); });
					$sheet->cell('B'.$fila, function($cell) use($data) { $cell->setValue( 'Total' ); });
					$sheet->mergeCells('C'.$fila.':'.'N'.$fila);
					$sheet->cell('C'.$fila, function($cell) {
							$cell->setAlignment('center');
							$cell->setValue(date('Y'));
					});
					$sheet->cell('O'.$fila, function($cell) {
					    $cell->setValue('Total');
					});
					$fila++;

					$cliente="";
					$subFila=0;
					foreach ($data['top'] as $dato) {
						if ($cliente != $dato->Cliente) {
							if ($cliente != "") {
								$sheet->row($fila, function($row) use($fila) { $row->setBackground('#FFFFFF'); });
								$fila++;
							}

							$sheet->cell('A'.$fila, function($cell) use($dato) { $cell->setAlignment('center'); $cell->setValue($dato->Cliente); });
							$sheet->cell('B'.$fila, function($cell) { $cell->setValue( (date('Y')-1) ); });
							$sheet->cell('C'.$fila, function($cell) { $cell->setValue('ENE'); });
							$sheet->cell('D'.$fila, function($cell) { $cell->setValue('FEB'); });
							$sheet->cell('E'.$fila, function($cell) { $cell->setValue('MAR'); });
							$sheet->cell('F'.$fila, function($cell) { $cell->setValue('ABR'); });
							$sheet->cell('G'.$fila, function($cell) { $cell->setValue('MAY'); });
							$sheet->cell('H'.$fila, function($cell) { $cell->setValue('JUN'); });
							$sheet->cell('I'.$fila, function($cell) { $cell->setValue('JUL'); });
							$sheet->cell('J'.$fila, function($cell) { $cell->setValue('AGO'); });
							$sheet->cell('K'.$fila, function($cell) { $cell->setValue('SEP'); });
							$sheet->cell('L'.$fila, function($cell) { $cell->setValue('OCT'); });
							$sheet->cell('M'.$fila, function($cell) { $cell->setValue('NOV'); });
							$sheet->cell('N'.$fila, function($cell) { $cell->setValue('DIC'); });
							$sheet->cell('O'.$fila, function($cell) { $cell->setValue( date('Y') ); });

							$sheet->cells('A'.$fila.':'.'O'.$fila, function($cells) {
									$cells->setBackground('#AAAAAA');
									$cells->setFontColor('#000000');
									$cells->setFont(array(
											'family'    => 'Arial',
											'size'      => '20',
											'bold'      =>  true
									));
									$cells->setAlignment('center');
									$cells->setValignment('center');
									$cells->setBorder('thin', 'none', 'none', 'none');
							});

							$cliente = $dato->Cliente;
							$fila++;
							$subFila=0;
						}

						$sheet->row($fila, function($row) use($subFila) {
								if ($subFila%2 == 0 ) $row->setBackground('#FFFFFF');
								if ($subFila%2 != 0 ) $row->setBackground('#E0E0E0');
						});
						$sheet->cells('A'.$fila.':'.'O'.$fila, function($cells) use($fila){
							$cells->setAlignment('center');
							$cells->setValignment('center');
							$cells->setFontFamily('Arial');
							$cells->setFontSize(20);
						});
						$sheet->cell('A'.$fila, function($cell) use($dato) {
							$cell->setAlignment('left');
							if ($dato->Producto != 'total') $cell->setValue($dato->Producto);
							if ($dato->Producto == 'total') $cell->setValue( strtoupper( $dato->Producto.' '.$dato->Cliente ) );
						});
						$sheet->cell('B'.$fila, function($cell) use($dato, $anioAnterior) { if($dato->$anioAnterior > 0) $cell->setValue($dato->$anioAnterior); });
						$sheet->cell('C'.$fila, function($cell) use($dato) { if($dato->ENE > 0) $cell->setValue($dato->ENE); });
						$sheet->cell('D'.$fila, function($cell) use($dato) { if($dato->FEB > 0) $cell->setValue($dato->FEB); });
						$sheet->cell('E'.$fila, function($cell) use($dato) { if($dato->MAR > 0) $cell->setValue($dato->MAR); });
						$sheet->cell('F'.$fila, function($cell) use($dato) { if($dato->ABR > 0) $cell->setValue($dato->ABR); });
						$sheet->cell('G'.$fila, function($cell) use($dato) { if($dato->MAY > 0) $cell->setValue($dato->MAY); });
						$sheet->cell('H'.$fila, function($cell) use($dato) { if($dato->JUN > 0) $cell->setValue($dato->JUN); });
						$sheet->cell('I'.$fila, function($cell) use($dato) { if($dato->JUL > 0) $cell->setValue($dato->JUL); });
						$sheet->cell('J'.$fila, function($cell) use($dato) { if($dato->AGO > 0) $cell->setValue($dato->AGO); });
						$sheet->cell('K'.$fila, function($cell) use($dato) { if($dato->SEP > 0) $cell->setValue($dato->SEP); });
						$sheet->cell('L'.$fila, function($cell) use($dato) { if($dato->OCT > 0) $cell->setValue($dato->OCT); });
						$sheet->cell('M'.$fila, function($cell) use($dato) { if($dato->NOV > 0) $cell->setValue($dato->NOV); });
						$sheet->cell('N'.$fila, function($cell) use($dato) { if($dato->DIC > 0) $cell->setValue($dato->DIC); });
						$sheet->cell('O'.$fila, function($cell) use($dato, $anioActual) { $cell->setFontWeight('bold'); $cell->setValue($dato->$anioActual); });
						if ($dato->Producto=='total') {
								$sheet->cells('A'.$fila.':'.'O'.$fila, function($cells) {
										$cells->setBackground('#AAAAAA');
										$cells->setFontColor('#000000');
										$cells->setFont(array(
												'family'    => 'Arial',
												'size'      => '20',
												'bold'      =>  true
										));
										$cells->setValignment('center');
										$cells->setBorder('thin', 'none', 'none', 'none');
								});
						}
						$fila++;
						$subFila++;
					}
		    });

				//Lesionados por localidad
				$excel->sheet('2', function($sheet) {
					$datosTotalesLocalidad = ReportesController::totalesXlocalidades();

					//inicializamos las filas
					$fila2 = 1;
					//configuramos hoja
					$sheet->setPaperSize('letter');
					$sheet->setOrientation('landscape');

					$sheet->mergeCells('A'.$fila2.':'.'O'.$fila2);
					$sheet->cells('A'.$fila2.':'.'O'.$fila2, function($cells) {
							$cells->setBackground('#ffffff');
					});
					$fila2++;

					$sheet->mergeCells('A'.$fila2.':'.'O'.$fila2);
					$sheet->cells('A'.$fila2.':'.'O'.$fila2, function($cells) {
							$cells->setBackground('#ffffff');
							$cells->setFontColor('#000000');
							$cells->setFont(array(
							    'family'    => 'Arial',
							    'size'      => '26',
							    'bold'      =>  true
							));
							// $cells->setBorder('none', 'solid', 'solid', 'none');
							$cells->setAlignment('center');
							$cells->setValignment('center');
					});
					$sheet->row($fila2, array( 'Lesionados  por Localidad / Mes captura (Solo 1ra Atención)', ));
					$fila2++;

					//ENCABEZADOS
					$sheet->setWidth(array( 'A'=>50, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20, 'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20, 'O'=>20 ));
					$sheet->mergeCells('A'.$fila2.':'.'A'.($fila2+1));
					$sheet->mergeCells('C'.$fila2.':'.'N'.$fila2);

					$sheet->cell('A'.$fila2, function($cell) { $cell->setBorder('none', 'none', 'thin', 'none'); $cell->setValue('Localidad'); });
					$sheet->cell('B'.$fila2, function($cell) { $cell->setValue('Total'); });
					$sheet->cell('C'.$fila2, function($cell) { $cell->setValue(date('Y')); });
					$sheet->cell('O'.$fila2, function($cell) { $cell->setValue('Total'); });

					$sheet->cells('A'.$fila2.':'.'O'.$fila2, function($cells) {
							$cells->setBackground('#AAAAAA');
							$cells->setFontColor('#000000');
							$cells->setFont(array(
							    'family'    => 'Arial',
							    'size'      => '20',
							    'bold'      =>  true
							));
							// $cells->setBorder('none', 'solid', 'solid', 'none');
							$cells->setAlignment('center');
							$cells->setValignment('center');
					});
					$fila2++;

					$sheet->cell('B'.$fila2, function($cell) { $cell->setValue( (date('Y')-1) ); });
					$sheet->cell('C'.$fila2, function($cell) { $cell->setValue('ENE'); });
					$sheet->cell('D'.$fila2, function($cell) { $cell->setValue('FEB'); });
					$sheet->cell('E'.$fila2, function($cell) { $cell->setValue('MAR'); });
					$sheet->cell('F'.$fila2, function($cell) { $cell->setValue('ABR'); });
					$sheet->cell('G'.$fila2, function($cell) { $cell->setValue('MAY'); });
					$sheet->cell('H'.$fila2, function($cell) { $cell->setValue('JUN'); });
					$sheet->cell('I'.$fila2, function($cell) { $cell->setValue('JUL'); });
					$sheet->cell('J'.$fila2, function($cell) { $cell->setValue('AGO'); });
					$sheet->cell('K'.$fila2, function($cell) { $cell->setValue('SEP'); });
					$sheet->cell('L'.$fila2, function($cell) { $cell->setValue('OCT'); });
					$sheet->cell('M'.$fila2, function($cell) { $cell->setValue('NOV'); });
					$sheet->cell('N'.$fila2, function($cell) { $cell->setValue('DIC'); });
					$sheet->cell('O'.$fila2, function($cell) { $cell->setValue( date('Y') ); });

					$sheet->cells('A'.$fila2.':'.'O'.$fila2, function($cells) {
							$cells->setBackground('#AAAAAA');
							$cells->setFontColor('#000000');
							$cells->setFont(array(
									'family'    => 'Arial',
									'size'      => '20',
									'bold'      =>  true
							));
							$cells->setBorder('none', 'none', 'thin', 'none');
							$cells->setAlignment('center');
							$cells->setValignment('center');
					});
					$fila2++;

					$anioActual = 'y'.date('Y');
					$anioAnterior = 'y'.((date('Y'))-1);

					foreach ($datosTotalesLocalidad as $dato) {
							//formato de fila
							$sheet->row($fila2, function($row) use($fila2) {
									if ($fila2%2 == 0 ) $row->setBackground('#E0E0E0');
									if ($fila2%2 != 0 ) $row->setBackground('#FFFFFF');
							});
							$sheet->cells('A'.$fila2.':'.'O'.$fila2, function($cells) use($fila2){
								$cells->setAlignment('center');
								$cells->setValignment('center');
								$cells->setFontFamily('Arial');
								$cells->setFontSize(20);
							});
							$sheet->cell('A'.$fila2, function($cell) use($dato) { $cell->setAlignment('left'); $cell->setValue($dato->Localidad); });
							$sheet->cell('B'.$fila2, function($cell) use($dato, $anioAnterior) { $cell->setValue($dato->$anioAnterior); });
							$sheet->cell('C'.$fila2, function($cell) use($dato) { if($dato->ENE > 0) $cell->setValue($dato->ENE); });
							$sheet->cell('D'.$fila2, function($cell) use($dato) { if($dato->FEB > 0) $cell->setValue($dato->FEB); });
							$sheet->cell('E'.$fila2, function($cell) use($dato) { if($dato->MAR > 0) $cell->setValue($dato->MAR); });
							$sheet->cell('F'.$fila2, function($cell) use($dato) { if($dato->ABR > 0) $cell->setValue($dato->ABR); });
							$sheet->cell('G'.$fila2, function($cell) use($dato) { if($dato->MAY > 0) $cell->setValue($dato->MAY); });
							$sheet->cell('H'.$fila2, function($cell) use($dato) { if($dato->JUN > 0) $cell->setValue($dato->JUN); });
							$sheet->cell('I'.$fila2, function($cell) use($dato) { if($dato->JUL > 0) $cell->setValue($dato->JUL); });
							$sheet->cell('J'.$fila2, function($cell) use($dato) { if($dato->AGO > 0) $cell->setValue($dato->AGO); });
							$sheet->cell('K'.$fila2, function($cell) use($dato) { if($dato->SEP > 0) $cell->setValue($dato->SEP); });
							$sheet->cell('L'.$fila2, function($cell) use($dato) { if($dato->OCT > 0) $cell->setValue($dato->OCT); });
							$sheet->cell('M'.$fila2, function($cell) use($dato) { if($dato->NOV > 0) $cell->setValue($dato->NOV); });
							$sheet->cell('N'.$fila2, function($cell) use($dato) { if($dato->DIC > 0) $cell->setValue($dato->DIC); });
							$sheet->cell('O'.$fila2, function($cell) use($dato, $anioActual) { $cell->setFontWeight('bold'); $cell->setValue($dato->$anioActual); });

							if ($dato->Localidad == "TOTAL GENERAL") {
								$sheet->cells('A'.$fila2.':'.'O'.$fila2, function($cells) {
										$cells->setBackground('#AAAAAA');
										$cells->setFontColor('#000000');
										$cells->setFont(array(
												'family'    => 'Arial',
												'size'      => '20',
												'bold'      =>  true
										));
										$cells->setValignment('center');
										$cells->setBorder('thin', 'none', 'none', 'none');
								});
							}

							$fila2++;
					}
		    });

				//Lesionados por localidad
				$excel->sheet('2c', function($sheet) {
					$datosTotalesLocalidad = ReportesController::localidadClinicaPropia();

					//inicializamos las filas
					$fila3 = 1;
					//configuramos hoja
					$sheet->setPaperSize('letter');
					$sheet->setOrientation('landscape');

					$sheet->mergeCells('A'.$fila3.':'.'P'.$fila3);
					$sheet->cells('A'.$fila3.':'.'P'.$fila3, function($cells) {
							$cells->setBackground('#ffffff');
					});
					$fila3++;

					$sheet->mergeCells('A'.$fila3.':'.'P'.$fila3);
					$sheet->cells('A'.$fila3.':'.'P'.$fila3, function($cells) {
							$cells->setBackground('#ffffff');
							$cells->setFontColor('#000000');
							$cells->setFont(array(
							    'family'    => 'Arial',
							    'size'      => '24',
							    'bold'      =>  true
							));
							$cells->setAlignment('center');
							$cells->setValignment('center');
					});
					$sheet->row($fila3, array( 'Lesionados En Plaza Con Clínica Propia', ));
					$fila3++;

					//ENCABEZADOS
					$sheet->setWidth(array( 'A'=>30, 'B'=>10, 'C'=>10, 'D'=>10, 'E'=>10, 'F'=>10, 'G'=>10, 'H'=>10, 'I'=>10, 'J'=>10, 'K'=>10, 'L'=>10, 'M'=>10, 'N'=>10, 'O'=>10, 'P'=>10 ));
					$sheet->mergeCells('A'.$fila3.':'.'A'.($fila3+1));
					$sheet->mergeCells('D'.$fila3.':'.'O'.$fila3);

					$sheet->cell('A'.$fila3, function($cell) { $cell->setBorder('none', 'none', 'thin', 'none'); $cell->setValue('Localidad'); });
					$sheet->cell('B'.$fila3, function($cell) { $cell->setValue('2017'); });
					$sheet->cell('C'.$fila3, function($cell) { $cell->setValue('Total'); });
					$sheet->cell('D'.$fila3, function($cell) { $cell->setValue(date('Y')); });
					$sheet->cell('P'.$fila3, function($cell) { $cell->setValue('Total'); });

					$sheet->cells('A'.$fila3.':'.'P'.$fila3, function($cells) {
							$cells->setBackground('#AAAAAA');
							$cells->setFontColor('#000000');
							$cells->setFont(array(
							    'family'    => 'Arial',
							    'size'      => '14',
							    'bold'      =>  true
							));
							// $cells->setBorder('none', 'solid', 'solid', 'none');
							$cells->setAlignment('center');
							$cells->setValignment('center');
					});
					$fila3++;

					$sheet->cell('B'.$fila3, function($cell) { $cell->setValue('DIC'); });
					$sheet->cell('C'.$fila3, function($cell) { $cell->setValue( (date('Y')-1) ); });
					$sheet->cell('D'.$fila3, function($cell) { $cell->setValue('ENE'); });
					$sheet->cell('E'.$fila3, function($cell) { $cell->setValue('FEB'); });
					$sheet->cell('F'.$fila3, function($cell) { $cell->setValue('MAR'); });
					$sheet->cell('G'.$fila3, function($cell) { $cell->setValue('ABR'); });
					$sheet->cell('H'.$fila3, function($cell) { $cell->setValue('MAY'); });
					$sheet->cell('I'.$fila3, function($cell) { $cell->setValue('JUN'); });
					$sheet->cell('J'.$fila3, function($cell) { $cell->setValue('JUL'); });
					$sheet->cell('K'.$fila3, function($cell) { $cell->setValue('AGO'); });
					$sheet->cell('L'.$fila3, function($cell) { $cell->setValue('SEP'); });
					$sheet->cell('M'.$fila3, function($cell) { $cell->setValue('OCT'); });
					$sheet->cell('N'.$fila3, function($cell) { $cell->setValue('NOV'); });
					$sheet->cell('O'.$fila3, function($cell) { $cell->setValue('DIC'); });
					$sheet->cell('P'.$fila3, function($cell) { $cell->setValue( date('Y') ); });

					$sheet->cells('A'.$fila3.':'.'P'.$fila3, function($cells) {
							$cells->setBackground('#AAAAAA');
							$cells->setFontColor('#000000');
							$cells->setFont(array(
									'family'    => 'Arial',
									'size'      => '14',
									'bold'      =>  true
							));
							$cells->setBorder('none', 'none', 'thin', 'none');
							$cells->setAlignment('center');
							$cells->setValignment('center');
					});
					$fila3++;

					$anioAnterior = 'y'.(date('Y')-1);
					$dicAnterior = 'dic'.(date('Y')-1);
					$anioActual = 'y'.date('Y');

					$totalRed = 0;
					$totalPropias = 0;
					$totalGeneral = 0;

					// array('localidades' => $resQuery, 'datos' => $resDatos);
					foreach ($datosTotalesLocalidad['datos'] as $dato) {
						//formato de fila
						$sheet->row($fila3, function($row) use($fila3) {
								if ($fila3%2 == 0 ) $row->setBackground('#E0E0E0');
								if ($fila3%2 != 0 ) $row->setBackground('#FFFFFF');
						});
						$sheet->cells('A'.$fila3, function($cells) use($fila3){
							$cells->setAlignment('left');
							$cells->setValignment('center');
							$cells->setFontFamily('Arial');
							$cells->setFontSize(14);
						});
						$sheet->cells('B'.$fila3.':'.'P'.$fila3, function($cells) use($fila3){
							$cells->setAlignment('center');
							$cells->setValignment('center');
							$cells->setFontFamily('Arial');
							$cells->setFontSize(14);
						});
						if ($dato->Unidad == 'total') {
							$sheet->cells('A'.$fila3.':'.'P'.$fila3, function($cells) use($fila3){
								$cells->setFont(array(
										'family'    => 'Arial',
										'size'      => '14',
										'bold'      =>  true
								));
								$cells->setBackground('#AAAAAA');
								$cells->setBorder('thin', 'none', 'none', 'none');
							});
						}

						//datos
						$sheet->cell('A'.$fila3, function($cell) use($dato) {
							if ($dato->Unidad == 'total') $cell->setValue( $dato->loc );
							if ($dato->Unidad != 'total') $cell->setValue( $dato->Unidad );
						});
						$sheet->cell('B'.$fila3, function($cell) use($dato, $dicAnterior) { if($dato->$dicAnterior > 0) $cell->setValue( $dato->$dicAnterior ); });
						$sheet->cell('C'.$fila3, function($cell) use($dato, $anioAnterior) { $cell->setFontWeight('bold'); if($dato->$anioAnterior > 0) $cell->setValue( $dato->$anioAnterior ); });
						$sheet->cell('D'.$fila3, function($cell) use($dato) { if($dato->ENE > 0) $cell->setValue( $dato->ENE ); });
						$sheet->cell('E'.$fila3, function($cell) use($dato) { if($dato->FEB > 0) $cell->setValue( $dato->FEB ); });
						$sheet->cell('F'.$fila3, function($cell) use($dato) { if($dato->MAR > 0) $cell->setValue( $dato->MAR ); });
						$sheet->cell('G'.$fila3, function($cell) use($dato) { if($dato->ABR > 0) $cell->setValue( $dato->ABR ); });
						$sheet->cell('H'.$fila3, function($cell) use($dato) { if($dato->MAY > 0) $cell->setValue( $dato->MAY ); });
						$sheet->cell('I'.$fila3, function($cell) use($dato) { if($dato->JUN > 0) $cell->setValue( $dato->JUN ); });
						$sheet->cell('J'.$fila3, function($cell) use($dato) { if($dato->JUL > 0) $cell->setValue( $dato->JUL ); });
						$sheet->cell('K'.$fila3, function($cell) use($dato) { if($dato->AGO > 0) $cell->setValue( $dato->AGO ); });
						$sheet->cell('L'.$fila3, function($cell) use($dato) { if($dato->SEP > 0) $cell->setValue( $dato->SEP ); });
						$sheet->cell('M'.$fila3, function($cell) use($dato) { if($dato->OCT > 0) $cell->setValue( $dato->OCT ); });
						$sheet->cell('N'.$fila3, function($cell) use($dato) { if($dato->NOV > 0) $cell->setValue( $dato->NOV ); });
						$sheet->cell('O'.$fila3, function($cell) use($dato) { if($dato->DIC > 0) $cell->setValue( $dato->DIC ); });
						$sheet->cell('P'.$fila3, function($cell) use($dato, $anioActual) { $cell->setFontWeight('bold'); if($dato->$anioActual > 0) $cell->setValue( $dato->$anioActual ); });

						if ($dato->Unidad == 'total') {
							$fila3++;
							$sheet->cells('A'.$fila3.':'.'P'.$fila3, function($cells) use($fila3){
								$cells->setBackground('#FFFFFF');
							});
						}

						// //contadores
						// for ($i=0; $i < ; $i++) {
						// 	// code...
						// }
						// if ( $dato->Unidad == 'Red' ) $totalRed = $totalRed+$dato->CantidadUnidad;
						// $totalRed = 0;
						// $totalPropias = 0;
						// $totalGeneral = 0;

						$fila3++;
					}

				});
		})
		->download('xlsx');

		return view('excel.prueba', $data);
	}

}
