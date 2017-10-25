import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { BusquedasService } from '../../services/busquedas.service';
import { AuthService } from '../../services/auth.service';
import { DatosLocalesService } from '../../services/datos-locales.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html'
})
export class HomeComponent implements OnInit {

  usuario = JSON.parse(sessionStorage.getItem('session'))[0];
  paciente:any = [];

  constructor( private _busquedasService:BusquedasService,
               private _authService:AuthService,
               private _datosLocalesService:DatosLocalesService,
               private router:Router) {

               }

  ngOnInit() {
    this._datosLocalesService.verificaRutaPaciente();
    // this.consultaPrueba();
    // console.log(this.usuario);
  }

  consultaPrueba(){
    this._busquedasService.prueba()
                          .subscribe( data => {
                            console.log(data);
                          });
  }

}
