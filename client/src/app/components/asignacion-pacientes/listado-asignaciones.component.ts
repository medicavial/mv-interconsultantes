/***** Modulo de asignaciones *****/
/***** Samuel Ramírez - Diciembre 2017 *****/

import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators, FormArray } from '@angular/forms';
import { Router } from '@angular/router';
import { BusquedasService } from '../../services/busquedas.service';
import { RegistroDatosService } from '../../services/registro-datos.service';
import { AuthService } from '../../services/auth.service';
declare var $: any;

@Component({
  selector: 'app-listado-asignaciones',
  templateUrl: './listado-asignaciones.component.html'
})
export class ListadoAsignacionesComponent implements OnInit {
  listadoAsignaciones:any = [];
  usuario:any  = JSON.parse(sessionStorage.getItem('session'))[0];

  constructor( private _busquedasService:BusquedasService,
               private _registroDatos:RegistroDatosService,
               private _authService:AuthService,
               private router:Router ) {
                 if ( this.usuario.PER_administracion != 1 ) {
                   this.router.navigate(['home']);
                 }
               }

  ngOnInit() {
    this.getListadoAsignaciones();
  }

  getListadoAsignaciones(){
    this._busquedasService.getListadoAsignaciones()
                          .subscribe( data => {
                            this.listadoAsignaciones = data;
                            console.log(this.listadoAsignaciones);
                          });
  }

}
