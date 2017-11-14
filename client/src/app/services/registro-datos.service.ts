/***** Servicio que conecta con el API para hacer altas, bajas y actualizaciones *****/
/***** Samuel Ramírez - Octubre 2017 *****/

import { Injectable } from '@angular/core';
import { ApiConexionService } from './api-conexion.service';
import { Http, Headers } from '@angular/http';
import 'rxjs/Rx';

@Injectable()
export class RegistroDatosService {

  constructor( private _http:Http,
               private _api:ApiConexionService) {}

  api:string = this._api.apiUrl();

  notaSoap( datos ){
    // let url = `${ this.api }/paciente/digitales-`+datos.folio;
    let url = `${ this.api }/paciente/nota-soap`;
    let headers = new Headers({
      'Content-Type':'aplication/json'
    });

    return this._http.post( url, datos, { headers } )
               .map( res => {
                 return res.json();
               });
  }

  subetemporal( archivo ){
    let url = 'http://medicavial.net/mvnuevo/api/api.php?funcion=archivo_temporal';
    // let url = 'http://localhost/mvnuevo/api/api.php?funcion=archivo_temporal';
    let datos = new FormData();
    datos.append("file", archivo, archivo.name);

    // NO FUNCIONA CUANDO MANDAMOS LOS HEADERS
    // let headers = new Headers({
    //   'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8'
    // })
    return this._http.post( url, datos, /*{ headers }*/)
                      .map( res => {
                        return res.json();
                      });
  }

  subeDigitales( datos ){
    console.log(datos);
    let url = 'http://medicavial.net/mvnuevo/api/api.php?funcion=guardaDigital&fol='+datos.folio+'&tipo='+datos.cveDocumento+'&usr='+datos.usr;

    let data = new FormData(datos);
        data.append("archivo", datos.archivo);
        data.append("temporal", datos.temporal);
        data.append("tipo", datos.cveDocumento);
        data.append("file", datos.file);

    // NO FUNCIONA CUANDO MANDAMOS LOS HEADERS
    // let headers = new Headers({
    //   'Content-Type':'aplication/json'
    //   'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8'
    // });

    return this._http.post( url, data, /*{ headers }*/ )
               .map( res => {
                 return res.json();
               });
  }

  guardaAsignacion( datos ){
    // let url = `${ this.api }/paciente/digitales-`+datos.folio;
    let url = `${ this.api }/paciente/guardaAsignacion`;
    let headers = new Headers({
      'Content-Type':'aplication/json'
    });

    return this._http.post( url, datos, { headers } )
               .map( res => {
                 return res.json();
               });
  }

}
