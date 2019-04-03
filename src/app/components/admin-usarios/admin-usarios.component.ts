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
  tiposPermisos:any = [];
  buscando:boolean = false;
  trabajando:boolean = false;
  ver = {
    inactivos: false,
    admin: false
  }
  nuevoUsuario:FormGroup;
  usrNuevo:any = {
    nombre: null,
    aPaterno: null,
    aMaterno: null,
    email: null,
    rol: null,
    creador: JSON.parse(sessionStorage.getItem('session'))[0].username,
    emailCreador: JSON.parse(sessionStorage.getItem('session'))[0].USU_email
  };

  usrEditado: any = {
    id: null,
    permiso: null,
    status: null
  }

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
      'tipoUsuario':new FormControl( '', [
                                     Validators.min(2),
                                     Validators.required,
                                   ]),
       'nombre':new FormControl( null, [
                                       Validators.minLength(2),
                                       Validators.pattern("^[a-zA-Z ]*$"),
                                       Validators.required,
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
       // 'rol':new FormControl( false, []),
     });
  }

  ngOnInit() {
    // $('#crearUsuario').modal('show');
    console.log(this.usuario);
    this.getUsuarios();
    this.getPermisos();
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

  getPermisos(){
    this.buscando = true;
    this._busquedasService.getTiposPermisos()
                          .subscribe( data => {
                            this.tiposPermisos = data;
                            console.log(this.tiposPermisos);
                            this.buscando = false;
                          });
  }

  guardarUsuario(){
    this.trabajando = true;
    console.log(this.nuevoUsuario.controls);

    this.usrNuevo.nombre    = this.nuevoUsuario.controls.nombre.value;
    this.usrNuevo.aPaterno  = this.nuevoUsuario.controls.aPaterno.value;
    this.usrNuevo.aMaterno  = this.nuevoUsuario.controls.aMaterno.value;
    this.usrNuevo.email     = this.nuevoUsuario.controls.correo.value;
    this.usrNuevo.rol       = this.nuevoUsuario.controls.tipoUsuario.value;

    console.log(this.usrNuevo);

    this._registroDatos.nuevoUsuario( this.usrNuevo )
                          .subscribe( data => {
                            // this.trabajando = false;
                            console.log( data );
                            if ( data.usrGenerado === true ) {
                              this.trabajando = false;
                                console.log('usuario Generado correctamente');
                                this.getUsuarios();
                                $('#crearUsuario').modal('hide');
                            }
                          });
  }

  inactivos(){
    this.ver.inactivos = !this.ver.inactivos;
  }

  administradores(){
    this.ver.admin = !this.ver.admin;
  }

  editaUsuario(datos){
    console.log(datos);
    this.usrEditado.id      = datos.USU_id;
    this.usrEditado.permiso = datos.PER_clave;
    this.usrEditado.status  = datos.USU_activo;
    this.usrEditado.permisoActual = datos.PER_clave;
    this.usrEditado.statusActual  = datos.USU_activo;

    $('#editaUsuario').modal('show');
  }

  verificaCambios(){
    if ( this.usrEditado.id != null ) {
        // console.log(this.usrEditado);
        if ( parseInt(this.usrEditado.permiso) === this.usrEditado.permisoActual
            && this.usrEditado.status === this.usrEditado.statusActual ) {
            return false;
        }
        else{
          return true;
        }
    } else {
      return false;
    }
  }

  cancelaEdicion(){
    $('#editaUsuario').modal('hide');

    this.usrEditado = {
      id: null,
      permiso: null,
      status: null,
      permisoActual: null,
      statusActual: null
    }
  }

  guardaEdicion(){
    this.trabajando = true;

    this._registroDatos.usuarioEditado( this.usrEditado )
        .subscribe( data => {
          console.log( data );
          if ( data === 1 ) {
            this.trabajando = false;
              console.log('Usuario actualizado');
              this.getUsuarios();
              $('#editaUsuario').modal('hide');
          } else{
            alert('Error al actualizar datos. Intentelo nuevamente.');
          }
        });
  }

}
