import { Component, OnInit } from '@angular/core';
import { BusquedasService } from '../../services/busquedas.service';
import { AuthService } from '../../services/auth.service';
import { DatosLocalesService } from '../../services/datos-locales.service';
import { Router } from '@angular/router';
declare var $: any;

@Component({
  selector: 'app-paciente',
  templateUrl: './paciente.component.html'
})
export class PacienteComponent implements OnInit {

  paciente:any = [];
  digitales:any = [];
  buscando:boolean = false;

  constructor( private _busquedasService:BusquedasService,
               private _authService:AuthService,
               private _datosLocalesService:DatosLocalesService,
               private router:Router) {
                 if (sessionStorage.getItem('paciente')) {
                   this.paciente = JSON.parse(sessionStorage.getItem('paciente'));
                 } else{
                   this.router.navigate(['busqueda']);
                 }
               }

  ngOnInit() {
    console.log(this.paciente);
    this.getDigitales();
  }

  getDigitales(){
    this.buscando = true;
    this._busquedasService.getDigitales( this.paciente )
                          .subscribe( data => {
                            this.buscando=false;
                            this.digitales=data;
                            // console.log(this.digitales);
                          });
  }

  abreModal(nombre){
    console.log(nombre);
    $('#'+nombre).modal('show');
  }
}
