import { Injectable } from '@angular/core';
import { Router, ActivatedRouteSnapshot, RouterStateSnapshot, CanActivate, CanDeactivate } from "@angular/router";

@Injectable()
export class DatosLocalesService {

  constructor( private router:Router ) { }

  verificaRutaPaciente(){
    if ( this.router.url != 'paciente' && sessionStorage.getItem('paciente')) {
      sessionStorage.removeItem('paciente');
    }
  }
}
