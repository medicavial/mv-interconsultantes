<?php namespace App\Http\Controllers;
/***** Controlador para creación de reportes *****/
/***** Samuel Ramírez - Julio 2018 *****/

use DB;
use View;
use Input;
use Mail;
use Response;
use Excel;
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

	public function repTemplate(){
		$datos = ReportesController::getDatos();
		$variables = array_keys(get_object_vars($datos[0]));

		$data = array('datos' 		=> $datos,
									'variables'	=> $variables);

		Excel::create('template', function($excel) use($data) {
		    $excel->sheet('primera', function($sheet)  use($data){
						$sheet->loadView('excel.prueba', $data);
		    });
		})->download('xlsx');

		return view('excel.prueba', $data);
	}
}
