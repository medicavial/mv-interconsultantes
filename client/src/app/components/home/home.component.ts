/***** Pantalla Home *****/
/***** Samuel Ramírez - Octubre 2017 *****/

import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators, FormArray } from '@angular/forms';
import { Router } from '@angular/router';
import { BusquedasService } from '../../services/busquedas.service';
import { AuthService } from '../../services/auth.service';
import { DatosLocalesService } from '../../services/datos-locales.service';
declare var $: any;


@Component({
  selector: 'app-home',
  templateUrl: './home.component.html'
})
export class HomeComponent implements OnInit {

  usuario = JSON.parse(sessionStorage.getItem('session'))[0];
  unidades:any = [];
  seleccionUnidad:FormGroup;

  constructor( private _busquedasService:BusquedasService,
               private _authService:AuthService,
               private _datosLocalesService:DatosLocalesService,
               private router:Router) {
                 this.seleccionUnidad = new FormGroup({
                   'uniClave': new FormControl( 0, [
                                                    Validators.required,
                                                    Validators.min(1)
                                                   ])
                 });
               }

  ngOnInit() {
    this._datosLocalesService.verificaRutaPaciente();
    if ( this.usuario.PER_clave === 3 && this.usuario.unidad === '0') {
        this.modalUnidad();
    }
    this.consultaUnidades();
    // console.log(this.usuario);
  }

  modalUnidad(){
    $('#seleccionaUnidad').modal({
      show: true,
      keyboard: false,
      backdrop: 'static'
    });
  }

  consultaUnidades(){
    this._busquedasService.getUnidades()
                          .subscribe( data => {
                            this.unidades = data;
                            console.log(this.unidades);
                          });
  }

  confirmaUnidad(){
    console.log( this.usuario );
    this.usuario.unidad = parseInt(this.seleccionUnidad.value.uniClave);

    sessionStorage.setItem('session', JSON.stringify([this.usuario]));
    if ( localStorage.getItem('session') ) {
        localStorage.setItem('session', JSON.stringify([this.usuario]))
    };

    this.usuario = JSON.parse(sessionStorage.getItem('session'))[0];
    console.log( this.usuario );

    $('#seleccionaUnidad').modal('hide');
  }

  irAsignacion(){
    this.router.navigate(['asignacion']);
  }


}
