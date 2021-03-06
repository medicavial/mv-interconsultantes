import { Component, OnInit } from '@angular/core';
import { Router, NavigationStart, NavigationEnd } from "@angular/router";
import { AuthService } from "../../services/auth.service";
import { DatosLocalesService } from '../../services/datos-locales.service';

declare var $: any;

@Component({
  selector: 'app-navegacion',
  templateUrl: './navegacion.component.html'
})
export class NavegacionComponent implements OnInit {

  usuario = [];

  constructor( private router:Router,
               public _authService:AuthService,
               public _datosLocalesService:DatosLocalesService ) {
                 this.router.events.subscribe( event => {
                  //  console.log(event instanceof NavigationEnd);
                   if (event instanceof NavigationEnd) {
                      if ( sessionStorage.getItem('session') ) {
                          this.usuario = JSON.parse(sessionStorage.getItem('session'))[0];
                          // console.log(this.usuario);
                      }
                      $('#navbarTogglerDemo01').collapse('hide');
                   }
                 });
               }

  ngOnInit() {
    // this._datosLocalesService.verificaRutaPaciente();
  }

  logout(){
    if (localStorage.getItem('session')) {
        localStorage.removeItem('session');
    }
    sessionStorage.removeItem('session');
    this._authService.auth();
    // this.usuario = [];
  }

}
