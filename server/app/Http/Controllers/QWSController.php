<?php namespace App\Http\Controllers;

use stdClass;
use SoapClient;
use SoapHeader;
use SimpleXMLElement;
use asXML;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class QWSController extends Controller {
 public function index(){
    error_reporting(0);
    // header('Content-Type: text/xml');

    $string ='<?xml version="1.0" encoding="UTF-8"?>
                <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://qualitas/appcr/expediente/xsd">
                <soapenv:Header/>
                <soapenv:Body>
                  <xsd:expediente id_proveedor="07370" no_factura="431413" no_reporte="04180339901" no_siniestro="04180297613" tipoProveedor="07">
                    <xsd:imagenes>
                      <xsd:imagen>
                        <nombreImagen>TA06.pdf</nombreImagen>
                        <tipo>TA06</tipo>
                        <binaryData>cid:TA06</binaryData>
                      </xsd:imagen>
                      <xsd:imagen>
                        <nombreImagen>QS07.pdf</nombreImagen>
                        <tipo>QS07</tipo>
                        <binaryData>cid:QS07</binaryData>
                      </xsd:imagen>
                      <xsd:imagen>
                        <nombreImagen>ME02.pdf</nombreImagen>
                        <tipo>ME02</tipo>
                        <binaryData>cid:ME02</binaryData>
                      </xsd:imagen>
                      <xsd:imagen>
                        <nombreImagen>FE05.pdf</nombreImagen>
                        <tipo>FE05</tipo>
                        <binaryData>cid:FE05</binaryData>
                      </xsd:imagen>
                      <xsd:imagen>
                        <nombreImagen>QS56.pdf</nombreImagen>
                        <tipo>QS56</tipo>
                        <binaryData>cid:QS56</binaryData>
                      </xsd:imagen>
                    </xsd:imagenes>
                  </xsd:expediente>
                </soapenv:Body>
                </soapenv:Envelope>';
    $xml = simplexml_load_string($string)->asXML();

    print_r($xml);

    $wsdl = 'http://201.151.239.105:8081/wl_wsRecepcionPagos/services/webServiceExpedienteSiniestrosPort?wsdl';
    $soap = new SoapClient($wsdl);

    $data = $soap->executeSequence($xml);//llamamos al método que nos interesa con los parámetros
    // return $data;
    $response =  $soap->__getLastResponse();
    print_r( $response );
    return $response;
/*

		$parametros = array('pid'=>'1','token'=>'ad8787769969818cc9f4f9a656298f58');
		$wsdl = 'http://201.151.239.105:8081/wl_wsRecepcionPagos/services/webServiceExpedienteSiniestrosPort?wsdl';
    // $soap = new SoapClient($wsdl);

		$soap = new SoapClient($wsdl, $parametros);
    $data = $soap->__getFunctions();
    // header('Content-Type: text/xml');
    $xml= new SimpleXMLElement ('
    <?xml version="1.0" encoding="UTF-8" ?>
      <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://qualitas/appcr/expediente/xsd">
        <soapenv:Header/>
        <soapenv:Body>
          <xsd:expediente id_proveedor="07370" no_factura="431413" no_reporte="04180339901" no_siniestro="04180297613" tipoProveedor="07">
            <xsd:imagenes>
              <!--1 to 30 repetitions:-->
              <xsd:imagen>
                <nombreImagen>TA06.pdf</nombreImagen>
                <tipo>TA06</tipo>
                <binaryData>cid:TA06</binaryData>
              </xsd:imagen>
              <xsd:imagen>
                <nombreImagen>QS07.pdf</nombreImagen>
                <tipo>QS07</tipo>
                <binaryData>cid:QS07</binaryData>
              </xsd:imagen>
              <xsd:imagen>
                <nombreImagen>ME02.pdf</nombreImagen>
                <tipo>ME02</tipo>
                <binaryData>cid:ME02</binaryData>
              </xsd:imagen>
              <xsd:imagen>
                <nombreImagen>FE05.pdf</nombreImagen>
                <tipo>FE05</tipo>
                <binaryData>cid:FE05</binaryData>
              </xsd:imagen>
              <xsd:imagen>
                <nombreImagen>QS56.pdf</nombreImagen>
                <tipo>QS56</tipo>
                <binaryData>cid:QS56</binaryData>
              </xsd:imagen>
            </xsd:imagenes>
          </xsd:expediente>
        </soapenv:Body>
      </soapenv:Envelope>
      ');

      return $xml;

    // $data = $soap->executeSequence($xml);//llamamos al método que nos interesa con los parámetros
    // $data =  $soap->__getLastResponse();
    */
 }

}
