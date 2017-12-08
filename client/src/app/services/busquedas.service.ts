/***** Servicio que conecta con el API para hacer busquedas de datos *****/
/***** Samuel Ramírez - Octubre 2017 *****/

import { Injectable } from '@angular/core';
import { ApiConexionService } from './api-conexion.service';
import { Http, Headers } from '@angular/http';
import 'rxjs/Rx';


@Injectable()
export class BusquedasService {

  constructor( private _http:Http,
               private _api:ApiConexionService ) {}

  api: string = this._api.apiUrl();

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

  getDigitales(datos){
    let url = `${ this.api }/paciente/digitales`;
    let headers = new Headers({
      'Content-Type':'aplication/json'
    });

    return this._http.post( url, datos, {headers} )
               .map( res => {
                 return res.json();
               });
  }

  getListadoSoap( folio ){
    let url = `${ this.api }/paciente/listadosoap-`+folio;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getUnidades(){
    let url = `${ this.api }/busquedas/listadoUnidades`;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getListadoMedicos(){
    let url = `${ this.api }/busquedas/listadoMedicos`;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getUsuarios(){
    let url = `${ this.api }/busquedas/listadoUsuarios`;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getPacientesAsignados( datos ){
    let url = `${ this.api }/busquedas/asignaciones-`+datos.username;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getExistenciasBotiquin( datos ){
    let url = `${ this.api }/busquedas/botiquin-`+datos.unidad;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getRecetaAbierta( folio ){
    let url = `${ this.api }/paciente/receta-`+folio;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getRecetaExterna( folio ){
    let url = 'http://medicavial.net/mvnuevo/api/notaMedica.php?funcion=getItemsRecetaExterna&fol='+folio;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getCatalogoIndicaciones(){
    let url = 'http://medicavial.net/mvnuevo/api/api.php?funcion=getListIndicaciones';

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getIndicacionesReceta(){
    let folio = JSON.parse(sessionStorage.getItem('paciente')).folio;

    let url = `${ this.api }/busquedas/indicaciones-`+folio;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getRecetasXfolio( folio ){
    let url = `${ this.api }/paciente/recetas-`+folio;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getTiposPermisos(){
    let url = `${ this.api }/busquedas/listadoPermisos`;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

  getListadoAsignaciones(){
    let url = `${ this.api }/administracion/listadoAsignaciones`;

    return this._http.get( url )
               .map( res => {
                 return res.json();
               });
  }

}
