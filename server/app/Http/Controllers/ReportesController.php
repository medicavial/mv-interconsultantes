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

}
