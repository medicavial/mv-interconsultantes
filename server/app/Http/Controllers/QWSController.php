<?php namespace App\Http\Controllers;

use SoapClient;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class QWSController extends Controller {
 public function index(){
	 	// return 'entra';

		$apiauth =array('UserName'=>'username','Password'=>'password');
		$wsdl = 'http://yoururl.com/service.asmx?WSDL';

		$soap = new SoapClient($wsdl);
		$header = new SoapHeader('http://tempuri.org/', 'AuthHeader', $apiauth);
		$soap->__setSoapHeaders($header);
		$data = $soap->mymethodname($my_method_parameter,$header);

		print_r($data);


 }

}
