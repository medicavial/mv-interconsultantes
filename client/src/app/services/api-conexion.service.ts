/***** Servicio que define la url del API en uso *****/
/***** Samuel Ramírez - Octubre 2017 *****/

import { Injectable } from '@angular/core';

@Injectable()
export class ApiConexionService {
  // api:string = "http://busqueda.medicavial.net/api"; //produccion
  api:string = "http://localhost/SBU/server/public"; //local

  constructor() { }

  apiUrl(){
    return this.api;
  }
}
