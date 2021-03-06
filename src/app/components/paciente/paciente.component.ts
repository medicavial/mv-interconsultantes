/***** Datos de paciente *****/
/***** Samuel Ramírez - Octubre 2017 *****/

import { Component, OnInit, ElementRef, ViewChild } from '@angular/core';
import { BusquedasService } from '../../services/busquedas.service';
import { AuthService } from '../../services/auth.service';
import { DatosLocalesService } from '../../services/datos-locales.service';
import { RegistroDatosService } from '../../services/registro-datos.service';
import { FormBuilder, FormGroup, Validators } from "@angular/forms";
import { Router } from '@angular/router';
declare var $: any;

@Component({
  selector: 'app-paciente',
  templateUrl: './paciente.component.html'
})
export class PacienteComponent implements OnInit {
  formUpload: FormGroup;
  cargando: boolean = false;
  trabajando: boolean = false;
  refresh: boolean = false;
  paciente:any = [];
  digitales:any = [];
  asignacion:any = [];
  observaciones:any = {
    original: null,
    edicion: null,
  };
  buscando:boolean = false;
  fechaDatos = null;
  usuario:any = JSON.parse(sessionStorage.getItem('session'))[0];

  @ViewChild('fileInput') fileInput: ElementRef;

  constructor( private _busquedasService:BusquedasService,
               private _authService:AuthService,
               private _datosLocalesService:DatosLocalesService,
               private _registroDatos:RegistroDatosService,
               private router:Router,
               private formBuilder:FormBuilder ) {
                 if (sessionStorage.getItem('paciente')) {
                     this.paciente = JSON.parse(sessionStorage.getItem('paciente'));
                     // if ( this.paciente.observaciones != undefined ) {
                     //     this.observaciones.original  = this.paciente.observaciones;
                     //     this.observaciones.edicion   = this.paciente.observaciones;
                     // }
                     console.log(this.paciente);
                 } else{
                   this.router.navigate(['busqueda']);
                 }
               }
  createForm() {
   this.formUpload = this.formBuilder.group({
     cveDocumento: ['', Validators.required],
     digital: null,
     folio: this.paciente.folio,
     usr: this.usuario.username,
     archivo: null,
     temporal: null,
     file: null
   });
  }

  ngOnInit() {
    // this.abreModal('observaciones');
    this.createForm();
    this.getAsignacion();
    // console.log(this.paciente);
    if (sessionStorage.getItem('digitales')) {
        this.digitales =JSON.parse( sessionStorage.getItem('digitales') );
        // console.log(this.digitales);
        console.log('digitales almacenados');
    } else {
        this.getDigitales();
        console.log('se ejecutó la consulta de digitales');
    }
  }

  getDigitales(){
    this.buscando = true;
    this.refresh = true;
    this._busquedasService.getDigitales( this.paciente )
                          .subscribe( data => {
                            this.buscando=false;
                            this.digitales=data;
                            this.digitales.tiposDigitales.sort(function (a, b) {
                              if (a.TID_docMV < b.TID_docMV) {return 1;}
                              if (a.TID_docMV > b.TID_docMV) {return -1;}
                              return 0;
                            });
                            sessionStorage.setItem('digitales', JSON.stringify(this.digitales));
                            console.log(this.digitales);
                            this.refresh=false;
                          });
  }

  getAsignacion(){
    this.buscando = true;
    this._busquedasService.getAsignacion( this.paciente.folio )
                          .subscribe( data => {
                            this.buscando=false;
                            this.asignacion=data;
                            if ( this.asignacion.length > 0 ) {
                              this.paciente.observaciones   = this.asignacion[0].ASI_observaciones;
                              this.observaciones.original   = this.asignacion[0].ASI_observaciones;
                              this.observaciones.edicion    = this.asignacion[0].ASI_observaciones;
                            }
                            console.log(this.asignacion);
                          });
  }

  abreModal(nombre){
    // console.log(nombre);
    $('#'+nombre).modal('show');
  }

  archivoSeleccionado(event) {
    this.cargando = true;
    let reader = new FileReader();
    if(event.target.files && event.target.files.length > 0) {
      let file = event.target.files[0];
      reader.readAsDataURL(file);
      // console.log(file);
      this._registroDatos.subetemporal( file )
                          .subscribe( data => {
                            console.log( data );
                            this.formUpload.value.archivo = data.nombre;
                            this.formUpload.value.temporal = data.temporal;
                            this.formUpload.value.file = file;
                            this.cargando=false;
                          });
    }
  }

  upload(){
    this.cargando = true;

    this._registroDatos.subeDigitales( this.formUpload.value )
                        .subscribe( data => {
                          this.cargando=false;
                          console.log(data);
                          this.getDigitales();
                          this.fileInput.nativeElement.value = "";
                          this.formUpload.reset();
                        });
  }

  irAsignacion(){
    this.router.navigate(['asignacion']);
  }

  editaAsignacion(){
    this.router.navigate(['listadoAsignaciones/'+this.asignacion[0].ASI_id]);
  }

  confirmaCierre(){
    this.trabajando = true;

    let datos = {
      idAsignacion  : this.paciente.idAsignacion,
      folio         : this.paciente.folio,
      username      : this.usuario.username,
      unidad        : this.usuario.unidad
    }

    this._registroDatos.cerrarAtencion( datos )
                        .subscribe( data => {
                          this.trabajando=false;
                          console.log(data);
                          if (data === 1) {
                            $('#cerrarAtencion').modal('hide');
                            this.router.navigate(['home']);
                          } else{
                            alert('error al cerrar la atención, intentelo nuevamente');
                          }
                        });
  }

  guardaObservaciones(){
    this.trabajando=true;

    let datos = {
      idAsignacion  : this.asignacion[0].ASI_id,
      observaciones : this.observaciones.edicion,
    }

    this._registroDatos.actualizaObs( datos )
                        .subscribe( data => {
                          this.trabajando=false;
                          console.log(data);
                          if (data === 1) {
                            this.paciente.observaciones = this.observaciones.edicion;
                            sessionStorage.removeItem('paciente');
                            sessionStorage.setItem('paciente', JSON.stringify(this.paciente));
                            this.paciente = JSON.parse(sessionStorage.getItem('paciente'));

                            $('#observaciones').modal('hide');
                          } else{
                            alert('error al guardar');
                          }
                        });
  }

}
