/***** NOTA SOAP *****/
/***** Samuel Ramírez - Octubre 2017 *****/

import { Component, OnInit } from '@angular/core';
import { BusquedasService } from "../../services/busquedas.service";
import { RegistroDatosService } from "../../services/registro-datos.service";
import { FormGroup, FormControl, Validators, FormArray } from '@angular/forms';
import { Router } from '@angular/router';

declare var $: any;

@Component({
  selector: 'app-nota-soap',
  templateUrl: './nota-soap.component.html'
})
export class NotaSoapComponent implements OnInit {

  notaSoap:FormGroup;

  paciente:any = [];

  datosNota:any = {
    app: null,
    apx: null,
    subjetivos: null,
    objetivos: null,
    analisis: null,
    plan: null,
    folioPaciente: null,
    idRegistro: null
  };

  usuario:any = JSON.parse(sessionStorage.getItem('session'))[0];

  notasGeneradas:any = [];

  trabajando:boolean = false;

  constructor( private _busquedasService:BusquedasService,
               private _registroService:RegistroDatosService,
               private router:Router ) {

    this.notaSoap = new FormGroup({
      'app': new FormControl( '', [
                                   Validators.minLength(9),
                                   Validators.required
                                  ]),
      'apx': new FormControl( '', [
                                   Validators.minLength(9),
                                   Validators.required
                                  ]),
      'subjetivos': new FormControl( '', [
                                          Validators.minLength(9),
                                          Validators.required
                                         ]),
      'objetivos': new FormControl( '', [
                                          Validators.minLength(9),
                                          Validators.required
                                         ]),
      'analisis': new FormControl( '', [
                                         Validators.minLength(9),
                                         Validators.required
                                        ]),
      'plan': new FormControl( '', [
                                     Validators.minLength(9),
                                     Validators.required
                                    ]),
    });
  }

  ngOnInit() {
    console.log(this.usuario)
    if (sessionStorage.getItem('paciente')) {
      this.paciente = JSON.parse(sessionStorage.getItem('paciente'));
    } else{
      this.router.navigate(['busqueda']);
    }

    if ( this.usuario.PER_clave === 2 ) {
        this.notaSoap.controls['app'].disable();
        this.notaSoap.controls['apx'].disable();
        this.notaSoap.controls['subjetivos'].disable();
        this.notaSoap.controls['objetivos'].disable();
        this.notaSoap.controls['analisis'].disable();
        this.notaSoap.controls['plan'].disable();
    }

    this.trabajando=true;
    // console.log(this.paciente);
    this.getNotasGeneradas();
  }

  getNotasGeneradas(){
    this._busquedasService.getListadoSoap(this.paciente.folio)
                          .subscribe(data =>{
                            this.notasGeneradas = data;
                            // console.log(this.notasGeneradas);
                            this.trabajando=false;
                            if (data.length > 0) {
                              $('#soapGuardadas').modal('show');
                            }
                          });
  }

  guardaNota(){
    this.trabajando = true;

    this.datosNota.app = this.notaSoap.value.app;
    this.datosNota.apx = this.notaSoap.value.apx;
    this.datosNota.subjetivos = this.notaSoap.value.subjetivos;
    this.datosNota.objetivos = this.notaSoap.value.objetivos;
    this.datosNota.analisis = this.notaSoap.value.analisis;
    this.datosNota.plan = this.notaSoap.value.plan;
    this.datosNota.folioPaciente = this.paciente.folio;
    this.datosNota.username = this.usuario.username;
    this.datosNota.idRegistro = this.paciente.id_registro;

    this._registroService.notaSoap( this.datosNota )
                          .subscribe( data =>{
                            console.log(data);
                            this.trabajando = false;
                            this.getNotasGeneradas();
                            if (data.respuesta === "Nota Creada Correctamente" ) {
                              $('#avisoCorrecto').modal({
                                backdrop: 'static',
                                show: true,
                                keyboard: false
                              });

                              setTimeout(() => {
                                this.notaSoap.reset();
                                $('#avisoCorrecto').modal('hide');
                                $('#soapGuardadas').modal('show');
                                // this.router.navigate(['paciente']);
                              }, 1500);

                            }
                          })
  }

}
