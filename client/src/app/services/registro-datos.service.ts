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

}
