/***** Administración de usuarios *****/
/***** Samuel Ramírez - Noviembre 2017 *****/

import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators, FormArray } from '@angular/forms';
import { Router } from '@angular/router';
import { BusquedasService } from '../../services/busquedas.service';
import { RegistroDatosService } from '../../services/registro-datos.service';
import { AuthService } from '../../services/auth.service';
// import { DatosLocalesService } from '../../services/datos-locales.service';
declare var $: any;

@Component({
  selector: 'app-admin-usarios',
  templateUrl: './admin-usarios.component.html'
})
export class AdminUsariosComponent implements OnInit {

  usuario:any = JSON.parse(sessionStorage.getItem('session'))[0];
  listadoUsuarios:any = [];
  buscando:boolean = false;
  nuevoUsuario:FormGroup;
  usrNuevo:any = {
    nombre: null,
    aPaterno: null,
    aMaterno: null,
    email: null,
    rol: null
  };

  constructor( private _busquedasService:BusquedasService,
               private _registroDatos:RegistroDatosService,
               private _authService:AuthService,
               private router:Router ) {
     if ( this.usuario.PER_clave > 2 || this.usuario.PER_clave < 1 ) {
         this.router.navigate(['home']);
         console.error('No tiene permitido entrar aquí');
     }

     // formulario para usuario nuevo
     this.nuevoUsuario = new FormGroup({
       'nombre':new FormControl( null, [
                                       Validators.minLength(2),
                                       Validators.pattern("^[a-zA-Z ]*$"),
                                       Validators.required
                                     ]),
       'aPaterno':new FormControl( null, [
                                         Validators.minLength(4),
                                         Validators.pattern("^[a-zA-Z ]*$"),
                                         Validators.required
                                       ]),
       'aMaterno':new FormControl( null, [
                                         Validators.minLength(1),
                                         Validators.pattern("^[a-zA-Z ]*$"),
                                       ]),
       'correo':new FormControl( null, [
                                     Validators.minLength(5),
                                     Validators.required
                                    ]),
       'rol':new FormControl( false, []),
     });
  }

  ngOnInit() {
    $('#crearUsuario').modal('show');
    console.log(this.usuario);
    this.getUsuarios();
  }

  getUsuarios(){
    this.buscando = true;
    this._busquedasService.getUsuarios()
                          .subscribe( data => {
                            this.listadoUsuarios = data;
                            console.log(this.listadoUsuarios);
                            this.buscando = false;
                          });
  }

  guardarUsuario(){
    console.log(this.nuevoUsuario.controls);

    this.usrNuevo.nombre    = this.nuevoUsuario.controls.nombre.value;
    this.usrNuevo.aPaterno  = this.nuevoUsuario.controls.aPaterno.value;
    this.usrNuevo.aMaterno  = this.nuevoUsuario.controls.aMaterno.value;
    this.usrNuevo.email     = this.nuevoUsuario.controls.correo.value;
    this.usrNuevo.rol       = this.nuevoUsuario.controls.rol.value;

    console.log(this.usrNuevo);

    this._registroDatos.nuevoUsuario( this.usrNuevo )
                          .subscribe( data => {
                            // this.trabajando = false;
                              console.log( data );
                          });
  }

}
