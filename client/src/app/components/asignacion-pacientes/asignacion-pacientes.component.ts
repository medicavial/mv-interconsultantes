/***** Modulo de asignación de pacientes *****/
/***** Samuel Ramírez - Noviembre 2017 *****/

import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators, FormArray } from '@angular/forms';
import { Router } from '@angular/router';
import { BusquedasService } from '../../services/busquedas.service';
import { RegistroDatosService } from '../../services/registro-datos.service';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-asignacion-pacientes',
  templateUrl: './asignacion-pacientes.component.html'
})
export class AsignacionPacientesComponent implements OnInit {

  asignacionForm:FormGroup;
  paciente:any = [];
  listadoMedicos:any;
  listadoUsuarios:any;
  listadoUnidades:any;
  trabajando:boolean = false;

  asignacion = {
    folio: null,
    cveMedico: null,
    username: JSON.parse(sessionStorage.getItem('session'))[0].username,
    fechaAtencion: null,
    cveUnidad: null
  }

  constructor( private _busquedasService:BusquedasService,
               private _registroDatos:RegistroDatosService,
               private _authService:AuthService,
               private router:Router) {

                 if (sessionStorage.getItem('paciente')) {
                   this.paciente = JSON.parse(sessionStorage.getItem('paciente'));
                   console.log(this.paciente);
                 } else{
                   this.router.navigate(['busqueda']);
                 }

                 this.asignacionForm = new FormGroup({
                   'medico': new FormControl( 0, [
                                                  Validators.required,
                                                  Validators.min(1)
                                                ]),
                   'unidad': new FormControl( 0, [
                                                 Validators.required,
                                                 Validators.min(1)
                                               ]),
                   'fechaHora': new FormControl( 0, [
                                                 Validators.required
                                               ]),
                 });
               }

  ngOnInit() {
    this.getMedicos();
    this.getUnidades();
    this.getUsuarios();
    console.log(this.asignacion);
  }

  getUnidades(){
    this._busquedasService.getUnidades()
                          .subscribe( data => {
                            this.listadoUnidades = data;
                            console.log(this.listadoUnidades);
                          });
  }

  getUsuarios(){
    this._busquedasService.getUsuarios()
                          .subscribe( data => {
                            this.listadoUsuarios = data;
                            console.log(this.listadoUsuarios);
                          });
  }

  getMedicos(){
    this._busquedasService.getListadoMedicos()
                          .subscribe( data => {
                            this.listadoMedicos = data;
                            console.log(this.listadoMedicos);
                          });
  }

  buscaUsuarioMedico( cveMedico ){
    let credenciales = false;
    console.log(cveMedico);
    for (let i = 0; i < this.listadoMedicos.length; i++) {

      if ( this.listadoMedicos[i].Med_clave === cveMedico ) {
        console.log(this.listadoMedicos[i]);
          let nombreMedico = this.listadoMedicos[i].nombreCompleto.toLowerCase();

          for (let i = 0; i < this.listadoUsuarios.length; i++) {
              if (this.listadoUsuarios[i].USU_nombreCompleto.toLowerCase() === nombreMedico) {
                  console.log( this.listadoUsuarios[i] );
                  credenciales = true;
                  return credenciales;
              }
          }
      }
    }
    return credenciales;
  }

  guardaAsignacion(){
    this.trabajando = true;
    this.asignacion.folio = this.paciente.folio;
    this.asignacion.cveMedico = parseInt(this.asignacionForm.value.medico);
    this.asignacion.cveUnidad = this.asignacionForm.value.unidad;
    this.asignacion.fechaAtencion = this.asignacionForm.value.fechaHora;

    //buscamos el medico por Clave de medico
    if ( this.buscaUsuarioMedico( this.asignacion.cveMedico ) === false ) {
        console.log('se genera usuario y contraseña');
    } else{
      console.log('ya hay credenciales');
    };



    // this._registroDatos.guardaAsignacion( this.asignacion )
    //                       .subscribe( data => {
    //                         console.log( data );
    //                         this.trabajando = false;
    //                       });
  }


}
