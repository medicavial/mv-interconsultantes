/***** Servicio que conecta con el API para hacer solo busquedas de datos *****/
/***** Samuel Ramírez - Octubre 2017 *****/

import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormGroup, FormControl, Validators, FormArray } from '@angular/forms';
import { BusquedasService } from '../../services/busquedas.service';
import { AuthService } from '../../services/auth.service';
import { DatosLocalesService } from '../../services/datos-locales.service';

@Component({
  selector: 'app-busqueda',
  templateUrl: './busqueda.component.html'
})
export class BusquedaComponent implements OnInit {
  pacientes:any[] = [];

  buscador:FormGroup;

  buscando:boolean = false;

  paciente:any = {
    nombre: '',
    folio: ''
  };

  constructor( private _busquedasService:BusquedasService,
               private _authService:AuthService,
               private _datosLocalesService:DatosLocalesService,
               private router:Router) {

    this.buscador = new FormGroup({
      'folio':new FormControl( '', [
                                    Validators.pattern("([a-zA-Z]{4}[0-9]{4,6}[a-zA-Z0-9]{1,2})$")
                                  ]),
      'nombre':new FormControl( '', [
                                    Validators.minLength(3),
                                    Validators.pattern("^[a-zA-Z ]*$")
                                  ]),
    });

  }

  ngOnInit() {
    this._datosLocalesService.verificaRutaPaciente();
  }

  busquedaFolio(){
    this.buscando=true;
    this.paciente.folio=this.buscador.value.folio;
    this.paciente.nombre=this.buscador.value.nombre;

    // console.log(this.buscador);
    this._busquedasService.buscaPaciente( this.paciente )
                          .subscribe( data => {
                            this.pacientes = data;
                            this.buscando=false;
                            // console.log(this.pacientes);
                          });
  }

  borrarParametros(){
    // console.log(this.paciente.folio);
    // console.log(this.paciente.nombre);
    this.buscador.reset({
        nombre: '',
        correo: ''
    });

    this.paciente.folio = '';
    this.paciente.nombre = '';

    this.pacientes = [];
  }

  pacienteSeleccionado(paciente){
    sessionStorage.setItem('paciente', JSON.stringify(paciente));

    this.router.navigate(['paciente']);
  }

}
