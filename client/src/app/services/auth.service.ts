import { Injectable } from '@angular/core';
import { Router, ActivatedRouteSnapshot, RouterStateSnapshot, CanActivate, CanDeactivate } from "@angular/router";
import { Http, Headers } from '@angular/http';
import 'rxjs/Rx';

@Injectable()
export class AuthService implements CanActivate {

  api:string = "http://busqueda.medicavial.net/api";
  // api:string = "http://localhost/SBU/server/public";

  constructor( private router:Router,
               private _http:Http) {}

  canActivate( next:ActivatedRouteSnapshot, state:RouterStateSnapshot ){
    if (sessionStorage.getItem('session')  || localStorage.getItem('session')) {
        if (localStorage.getItem('session')) {
            sessionStorage.setItem('session',JSON.stringify( JSON.parse(localStorage.getItem('session'))));
        }
        // console.log("Está autenticado");
        return true;
    } else {
      // console.log("no puede entrar");
      this.router.navigate(['login']);
      return false;
    }
  }

  auth(){
    if (sessionStorage.getItem('session')  || localStorage.getItem('session')) {
        this.router.navigate(['home']);
        if (localStorage.getItem('session')) {
            sessionStorage.setItem('session',JSON.stringify( JSON.parse(localStorage.getItem('session'))));
        }
        // return JSON.parse(localStorage.getItem('userSession'))
        return true;
    } else{
      // console.log("no ha iniciado sesion");
      this.router.navigate(['login']);
      return false;
    }
  }

  checkSession(){
    if (sessionStorage.getItem('session')  || localStorage.getItem('session')) {
        if (localStorage.getItem('session')) {
            sessionStorage.setItem('session',JSON.stringify( JSON.parse(localStorage.getItem('session'))));
        }
        return true;
    } else{
        return false;
    }
  }

}
