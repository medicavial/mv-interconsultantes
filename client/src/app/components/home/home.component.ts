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
  pacientesAsignados:any =[];
  buscando:boolean = false;

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

    //si se trata de un medico
    if ( this.usuario.PER_clave === 3 ) {
        if (this.usuario.unidad === '0') {
          this.consultaUnidades();
          this.modalUnidad();
        }
        this.getAsignaciones();
    }
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

  getAsignaciones(){
    this.buscando = true;
    this._busquedasService.getPacientesAsignados( this.usuario )
                          .subscribe( data => {
                            this.pacientesAsignados = data;
                            console.log(this.pacientesAsignados);
                            this.buscando = false;
                          });
  }

  confirmaUnidad(){
    // console.log( this.usuario );
    this.usuario.unidad = parseInt(this.seleccionUnidad.value.uniClave);

    for (let i = 0; i < this.unidades.length; i++) {
        if ( this.usuario.unidad === this.unidades[i].Uni_clave ) {
            this.usuario.uniNombre = this.unidades[i].Uni_nombrecorto;
        }
    }
    // console.log(this.usuario);
    sessionStorage.setItem('session', JSON.stringify([this.usuario]));
    if ( localStorage.getItem('session') ) {
        localStorage.setItem('session', JSON.stringify([this.usuario]))
    };

    this.usuario = JSON.parse(sessionStorage.getItem('session'))[0];
    // console.log( this.usuario );

    $('#seleccionaUnidad').modal('hide');
  }

  irAsignacion(){
    this.router.navigate(['asignacion']);
  }

  irUsuarios(){
    this.router.navigate(['usuarios']);
  }

  irlistadoAsignaciones(){
    this.router.navigate(['listadoAsignaciones']);
  }

  seleccionPaciente( asignacion ){
    console.log( asignacion );
    let busca = {
      folio: asignacion.Exp_folio,
      nombre: null
    }

    let paciente:any;

    this._busquedasService.buscaPaciente( busca )
                          .subscribe( data => {
                            paciente = data[0];
                            paciente.idAsignacion = asignacion.ASI_id;
                            paciente.observaciones = asignacion.ASI_observaciones;
                            console.log(paciente);
                            sessionStorage.setItem('paciente', JSON.stringify( paciente ));
                            this.router.navigate(['paciente']);
                          });
  }

}
