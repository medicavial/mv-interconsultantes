/***** Administración de usuarios *****/
/***** Samuel Ramírez - Febrero 2019 *****/

import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../services/auth.service';
declare var $: any;

@Component({
  selector: 'app-cuenta',
  templateUrl: './cuenta.component.html'
})
export class CuentaComponent implements OnInit {
  usuario: any = JSON.parse(sessionStorage.getItem('session'))[0];

  constructor() { }

  ngOnInit() {
    console.log(this.usuario);
  }

  modal( data ){
    console.log(data.toLowerCase());

    // $('#exampleModal').modal('show');
    $(`#modal-${ data.toLowerCase() }`).modal('show');
  }

}

