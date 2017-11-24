import { Component, OnInit } from '@angular/core';
import { BusquedasService } from "../../services/busquedas.service";
import { RegistroDatosService } from "../../services/registro-datos.service";
import { FormGroup, FormControl, Validators, FormArray } from '@angular/forms';
import { Router } from '@angular/router';
import { Observable } from 'rxjs/Rx';

declare var $: any;

@Component({
  selector: 'app-receta-interna',
  templateUrl: './receta-interna.component.html',
  styles: []
})
export class RecetaInternaComponent implements OnInit {
  recetaInterna:FormGroup;
  paciente:any = JSON.parse(sessionStorage.getItem('paciente'));
  datosReceta:any = {
  };
  usuario:any = JSON.parse(sessionStorage.getItem('session'))[0];
  trabajando:boolean = false;
  botiquin:any = {};
  datosItem:any = {
    claveItem:0,
    cantidad:null,
    stock: 0,
    presentacion:null,
    indicaciones:null,
    almacen: null,
    tipoItem: null
  };
  datosInvalidos:boolean = false;
  buscando:boolean = false;
  itemsReceta:any = [];

  constructor( private _busquedasService:BusquedasService,
               private _registroService:RegistroDatosService,
               private router:Router ) {

     this.recetaInterna = new FormGroup({
       'item': new FormControl( 0, [
          Validators.required,
          Validators.min(1),
       ]),
       'cantidad': new FormControl( null, [
         Validators.required,
         Validators.min(1),
         // Validators.max( this.datosItem.stock )
       ]),
       'presentacion': new FormControl( {value: null, disabled: true}),
       'indicaciones': new FormControl( null, [
         Validators.required,
       ]),
     });

     this.recetaInterna.controls['item'].valueChanges
          .subscribe( data => {
            if (this.recetaInterna.controls['item'].value > 0) {
                this.recetaInterna.controls['cantidad'].setValue(null);
                this.recetaInterna.controls['indicaciones'].setValue(null);
                this.buscaItem( parseInt(data) );
            }
          })

      this.recetaInterna.controls['cantidad'].valueChanges
           .subscribe( data => {
             console.log(data);
             if ( data > this.datosItem.stock) {
                 this.datosInvalidos = true;
             }
           })
  }

  ngOnInit() {
    console.log(this.usuario)
    if (sessionStorage.getItem('paciente')) {
      this.paciente = JSON.parse(sessionStorage.getItem('paciente'));
      console.log(this.paciente);
    } else{
      this.router.navigate(['busqueda']);
    }

    this.buscando = true;

    this.traeItemsReceta();

    this._busquedasService.getExistenciasBotiquin( this.usuario )
                          .subscribe(data =>{
                            this.botiquin = data;
                            console.log( this.botiquin );
                            this.buscando = false;
                          });
  }

  traeItemsReceta(){
    this._busquedasService.getRecetaAbierta( this.paciente.folio )
                          .subscribe(data =>{
                            this.itemsReceta = data;
                            console.log( this.itemsReceta );
                          });
  }

  buscaItem( item ){
    for (let i = 0; i < this.botiquin.length; i++) {
        if ( parseInt( this.botiquin[i].Clave_producto ) === item ) {
            this.datosItem.claveItem = parseInt( this.botiquin[i].Clave_producto );
            this.datosItem.stock = parseInt(this.botiquin[i].Stock);
            this.datosItem.presentacion = this.botiquin[i].presentacion;
            this.datosItem.indicaciones = this.botiquin[i].posologia;
            this.datosItem.almacen = parseInt( this.botiquin[i].almacen );
            this.datosItem.tipoItem = parseInt( this.botiquin[i].tipoItem );
            this.datosItem.descripcion = this.botiquin[i].Descripcion;
            this.datosItem.idExistencia = this.botiquin[i].id;
            console.log(this.datosItem);

            this.recetaInterna.controls['presentacion'].setValue(this.datosItem.presentacion);
            this.recetaInterna.controls['indicaciones'].setValue(this.datosItem.indicaciones);
        }
    }
  }

  guardaItem(){
    let reserva;
    this.trabajando = true;
    this.datosItem.cantidad = this.recetaInterna.controls.cantidad.value;

    this._registroService.agregaItemReceta( this.datosItem )
        .subscribe(data =>{
          console.log(data);
          this.itemsReceta = data;
        });

    this.trabajando = false;
  }

  eliminaItem( item ){
    console.log('reserva: ' + item.id_reserva);

    this._registroService.eliminaItem( item )
        .subscribe(data =>{
          console.log(data);
          this.traeItemsReceta();
        });

  }

}
