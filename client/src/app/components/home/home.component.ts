import { Component, OnInit } from '@angular/core';
import { BusquedasService } from '../../services/busquedas.service';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html'
})
export class HomeComponent implements OnInit {

  usuario = JSON.parse(sessionStorage.getItem('session'))[0];

  constructor( private _busquedasService:BusquedasService,
               private _authService:AuthService,
               private router:Router) {

               }

  ngOnInit() {
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
