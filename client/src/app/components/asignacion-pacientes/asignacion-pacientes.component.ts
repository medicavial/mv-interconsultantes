/***** Modulo de asignación de pacientes *****/
/***** Samuel Ramírez - Noviembre 2017 *****/

import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators, FormArray } from '@angular/forms';
import { Router } from '@angular/router';
import { BusquedasService } from '../../services/busquedas.service';
import { RegistroDatosService } from '../../services/registro-datos.service';
import { AuthService } from '../../services/auth.service';
declare var $: any;

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
  confirmacion:any;

  asignacion = {
    folio: null,
    cveMedico: null,
    username: JSON.parse(sessionStorage.getItem('session'))[0].username,
    fechaAtencion: null,
    cveUnidad: null,
    medicoReg: false,
    loginmedico: null,
    motivo:null,
    observaciones:null
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
                   'motivo': new FormControl( '', [
                                                 Validators.required,
                                                 Validators.minLength(3)
                                               ]),
                   'observaciones': new FormControl( '', []),
                   // 'unidad': new FormControl( 0, [
                   //                               Validators.required,
                   //                               Validators.min(1)
                   //                             ]),
                   // 'fechaHora': new FormControl( 0, [
                   //                               Validators.required
                   //                             ]),
                 });
               }

  ngOnInit() {
    this.getMedicos();
    // this.getUnidades();
    // this.getUsuarios();
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
    console.log(cveMedico);
    for (let i = 0; i < this.listadoMedicos.length; i++) {

      if ( this.listadoMedicos[i].USU_id === cveMedico ) {
        console.log(this.listadoMedicos[i]);
          let username = this.listadoMedicos[i].USU_login;
          return username;
      }
    }
  }

  guardaAsignacion(){
    this.trabajando = true;
    this.asignacion.folio = this.paciente.folio;
    this.asignacion.cveMedico = parseInt(this.asignacionForm.value.medico);
    // this.asignacion.cveUnidad = this.asignacionForm.value.unidad;
    // this.asignacion.fechaAtencion = this.asignacionForm.value.fechaHora;
    this.asignacion.loginmedico = this.buscaUsuarioMedico( this.asignacion.cveMedico );
    this.asignacion.motivo = this.asignacionForm.value.motivo;
    this.asignacion.observaciones = this.asignacionForm.value.observaciones;

    this._registroDatos.guardaAsignacion( this.asignacion )
                        .subscribe( data => {
                          this.trabajando = false;
                          if (data.respuesta.base > 0) {
                            this.confirmacion = data;
                            console.log(this.confirmacion);
                            // this.getUsuarios();
                            $('#avisoCorrecto').modal('show');

                            setTimeout(() => {
                              this.asignacionForm.reset();
                              $('#avisoCorrecto').modal('hide');
                              this.router.navigate(['paciente']);
                            }, 3000);
                          }
                        });
  }


}
