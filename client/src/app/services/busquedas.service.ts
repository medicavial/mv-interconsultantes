import { Injectable } from '@angular/core';
import { Http, Headers } from '@angular/http';
import 'rxjs/Rx';

@Injectable()
export class BusquedasService {
  api:string = "http://busqueda.medicavial.net/api";
  // api:string = "http://localhost/SBU/server/public";

  constructor( private _http:Http ) {}

  prueba(){
    let url = `${ this.api }/inicio`;
    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  login( datos ){
    let url = `${ this.api }/login`;
    let headers = new Headers({
      'Content-Type':'aplication/json'
    });

    return this._http.post( url, datos, {headers} )
               .map( res => {
                 return res.json();
               });
  }

  buscaPaciente(datos){
    let url = `${ this.api }/busca`;
    let headers = new Headers({
      'Content-Type':'aplication/json'
    });

    return this._http.post( url, datos, {headers} )
               .map( res => {
                 return res.json();
               });
  }

}
