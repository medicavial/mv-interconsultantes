/***** Modulo de asignaciones *****/
/***** Samuel Ramírez - Diciembre 2017 *****/

import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators, FormArray } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { BusquedasService } from '../../services/busquedas.service';
import { RegistroDatosService } from '../../services/registro-datos.service';
import { AuthService } from '../../services/auth.service';
declare var $: any;

@Component({
  selector: 'app-listado-asignaciones',
  templateUrl: './listado-asignaciones.component.html'
})
export class ListadoAsignacionesComponent implements OnInit {
  id:number;
  listadoAsignaciones:any = [];
  listadoMedicos:any = [];
  usuario:any = JSON.parse(sessionStorage.getItem('session'))[0];
  editaDatos:any = {
    original: {
      id: null,
      medico: null,
      motivo: null,
      obs: null,
      usuario:null,
    },
    nuevo: {
      id: null,
      medico: null,
      motivo: null,
      obs: null,
      usuario: null,
    },
  };
  trabajando: boolean = false;
  buscando:boolean = false;
  eliminar:boolean = false;

  constructor( private _busquedasService:BusquedasService,
               private _registroDatos:RegistroDatosService,
               private _authService:AuthService,
               private router:Router,
               private parametro: ActivatedRoute) {
                 if ( this.usuario.PER_administracion != 1 ) {
                   this.router.navigate(['home']);
                 }
               }

  ngOnInit() {
    if ( this.parametro.params ) {
      this.parametro.params.subscribe( params => {
        this.id = params['id'];
      });

      if ( this.id < 0 ) {
          this.router.navigate['home'];
          console.error('Incorrecto');
      }

      if ( this.id != undefined && this.id > 0 ) {
          console.log('se ingresó el id '+ this.id);
          this.asignacionXid( this.id );
      }
    }

    this.getListadoAsignaciones();
    this.getListadoMedicos();
    // console.log(this.usuario);
    this.editaDatos.original.usuario = this.usuario.username;
    this.editaDatos.nuevo.usuario = this.usuario.username;
  }

  asignacionXid( id ){
    this.buscando = true;
    this._busquedasService.getListadoAsignaciones()
                          .subscribe( data => {
                            this.listadoAsignaciones = data;
                            this.buscando = false;
                            // console.log(this.listadoAsignaciones);
                            for (let i = 0; i < this.listadoAsignaciones.length; i++) {
                                if ( parseInt(id) === this.listadoAsignaciones[i].ASI_id ) {
                                    // console.log(this.listadoAsignaciones[i]);
                                    this.seleccionaDatos( this.listadoAsignaciones[i] );
                                };
                            }


                          });
  }

  getListadoAsignaciones(){
    this.buscando = true;
    this._busquedasService.getListadoAsignaciones()
                          .subscribe( data => {
                            this.listadoAsignaciones = data;
                            this.buscando = false;
                            console.log(this.listadoAsignaciones);
                          });
  }

  getListadoMedicos(){
    this._busquedasService.getListadoMedicos()
                          .subscribe( data => {
                            this.listadoMedicos = data;
                            // console.log(this.listadoMedicos);
                          });
  }

  verificaEdicion(){
    let verificacion = false;
    if ( this.editaDatos.original.id != null ) {
        verificacion = false;
        if ( JSON.stringify( this.editaDatos.nuevo ) != JSON.stringify( this.editaDatos.original ) ) {
            verificacion = true;
        }
        return verificacion;
    }
  }

  seleccionaDatos( asignacion ){
    this.editaDatos.original.id     = asignacion.ASI_id;
    this.editaDatos.original.medico = asignacion.USU_loginMedico;
    this.editaDatos.original.motivo = asignacion.ASI_motivo;
    this.editaDatos.original.obs    = asignacion.ASI_observaciones;

    this.editaDatos.nuevo.id     = asignacion.ASI_id;
    this.editaDatos.nuevo.medico = asignacion.USU_loginMedico;
    this.editaDatos.nuevo.motivo = asignacion.ASI_motivo;
    this.editaDatos.nuevo.obs    = asignacion.ASI_observaciones;

    this.verificaEdicion();

    $('#editaAsignacion').modal({
      backdrop: 'static',
      keyboard: false,
    });
    console.log( this.editaDatos );
  }

  cancelaEdicion(){
    this.editaDatos.original.id     = null;
    this.editaDatos.original.medico = null;
    this.editaDatos.original.motivo = null;
    this.editaDatos.original.obs    = null;

    this.editaDatos.nuevo.id     = null;
    this.editaDatos.nuevo.medico = null;
    this.editaDatos.nuevo.motivo = null;
    this.editaDatos.nuevo.obs    = null;
    // this.editaDatos.nuevo = this.editaDatos.original;

    $('#editaAsignacion').modal('hide');

    if ( this.id > 0 ) {
      this.router.navigate(['paciente']);
    }
    // console.log( this.editaDatos );
  }

  guardaDatos(){
    this.trabajando = true;

    console.log( this.editaDatos );

    this._registroDatos.editaAsignacion( this.editaDatos.nuevo )
                          .subscribe( data => {
                            this.trabajando = false;
                            console.log(data);
                            if ( data === 1 ) {
                                this.cancelaEdicion();

                                if ( this.id > 0 ) {
                                  this.router.navigate(['paciente']);
                                } else {
                                  this.getListadoAsignaciones();
                                  // this.cancelaEdicion();
                                }
                            } else {
                              alert('Error al actualizar los datos');
                            }
                          });
  }

  eliminaAsignacion(){
    this.trabajando = true;

    console.log( this.editaDatos );

    this._registroDatos.eliminaAsignacion( this.editaDatos.original )
                          .subscribe( data => {
                            this.trabajando = false;
                            this.eliminar = false;
                            console.log(data);
                            if ( data === 1 ) {
                                this.getListadoAsignaciones();
                                this.cancelaEdicion();
                            } else {
                              alert('Error al eliminar');
                            }
                          });
  }

}
