/***** Acceso al sistema *****/
/***** Samuel Ramírez - Octubre 2017 *****/

import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators, FormArray } from '@angular/forms';
import { AuthService } from "../../services/auth.service";
import { BusquedasService } from "../../services/busquedas.service";
import { DatosLocalesService } from '../../services/datos-locales.service';
import { Router } from "@angular/router";


@Component({
  selector: 'app-login',
  templateUrl: './login.component.html'
})
export class LoginComponent implements OnInit {

  login:FormGroup;

  usuario:any = {
    username: null,
    password: null,
    remember: false
  };

  trabajando:boolean = false;
  error:boolean = false;

  constructor( private _authService:AuthService,
               private _busquedasService:BusquedasService,
               private _datosLocalesService:DatosLocalesService,
               private router:Router ) {
                 this.login = new FormGroup({
                   'username': new FormControl( '', [
                                                          Validators.minLength(3),
                                                          Validators.required
                                                         ]),
                    'password': new FormControl( '', [
                                                          Validators.minLength(3),
                                                          Validators.required
                                                         ]),
                    'remember': new FormControl(false),
                 });
               }

  ngOnInit() {
    this._authService.auth();
    this._datosLocalesService.verificaRutaPaciente();
  }

  enviaCredenciales(){
    this.trabajando=true;

    this.usuario.username = this.login.controls.username.value;
    this.usuario.password = this.login.controls.password.value;
    this.usuario.remember = this.login.controls.remember.value;

    this._busquedasService.login( this.usuario )
                          .subscribe( data =>{
                            // console.log(data);
                            if (data.length > 0 ) {
                                sessionStorage.setItem('session',JSON.stringify(data));
                                if (this.usuario.remember === true) {
                                    localStorage.setItem('session',JSON.stringify(data));
                                }
                                this.error = false;
                            } else {
                              console.error("Acceso denegado");
                              this.error = true;
                            }
                            this._authService.auth();
                            this.trabajando=false;
                            this.login.markAsUntouched();
                          })
  }
}
