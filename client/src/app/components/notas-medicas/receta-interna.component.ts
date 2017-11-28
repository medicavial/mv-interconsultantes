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
  recetaAbierta:FormGroup;
  indicacionesReceta:FormGroup;

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
  listadoRecetaExterna: any = [];
  catalogoIndicaciones: any = [];
  indicaciones: any = [];

  instruccionesAdicionales: string = '';

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

      //Receta Abierta
       this.recetaAbierta = new FormGroup({
         'itemRecetaAbierta': new FormControl( null, [
            Validators.required,
            Validators.minLength(5),
         ]),
         'indicacionesRecetaAbierta': new FormControl( null, [
           Validators.required,
           Validators.minLength(5),
         ]),
       });

       //Indicaciones adicionales
        this.indicacionesReceta = new FormGroup({
          'idIndicacion': new FormControl( 0, [
          ]),
          'complementoIndicacion': new FormControl( '', [
            Validators.required,
            Validators.minLength(5),
          ]),
        });

        this.indicacionesReceta.controls['idIndicacion'].valueChanges
             .subscribe( data => {
               for (let i = 0; i < this.catalogoIndicaciones.length; i++) {
                   if (this.indicacionesReceta.controls['idIndicacion'].value === this.catalogoIndicaciones[i].Ind_clave) {
                        if ( this.instruccionesAdicionales === '') {
                            this.instruccionesAdicionales = this.catalogoIndicaciones[i].Ind_nombre;
                            this.indicacionesReceta.controls['complementoIndicacion'].setValue(this.instruccionesAdicionales);
                        }else{
                          this.instruccionesAdicionales = this.instruccionesAdicionales+' '+ this.catalogoIndicaciones[i].Ind_nombre;
                          this.indicacionesReceta.controls['complementoIndicacion'].setValue( this.instruccionesAdicionales );
                        }
                   }
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

    this.traeItemsReceta();
    this.getRecetaExterna();
    this.getIndicacionesReceta();
    this.getBotiquin();
    this.getCatalogoIndicaciones();


  }

  getBotiquin(){
    this.buscando = true;

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
                            // console.log( this.itemsReceta );
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

          if ( data.length > 0 && data[0].id_receta) {
              this.itemsReceta = data;
              this.recetaInterna.reset();
              this.datosItem.stock = 0;
          }
        });

    this.trabajando = false;
  }

  eliminaItem( item ){
    this._registroService.eliminaReserva( item )
        .subscribe(data =>{
          console.log(data);
          this._registroService.eliminaSuministrosReceta( item )
              .subscribe( data => {
                  console.log(data);
              })
          this.traeItemsReceta();
        });
  }

  getRecetaExterna(){
    this._busquedasService.getRecetaExterna( this.paciente.folio )
                          .subscribe(data =>{
                            this.listadoRecetaExterna = data;
                            console.log( this.listadoRecetaExterna );
                          });
  }

  itemRecetaExterna(){
    let datosItem = {
      idReceta: null,
      item: this.recetaAbierta.controls.itemRecetaAbierta.value,
      indicacion: this.recetaAbierta.controls.indicacionesRecetaAbierta.value
    }

    console.log( datosItem );

    this._registroService.guardaItemExterno( datosItem )
        .subscribe( data => {
            console.log(data);
            this.recetaAbierta.reset();

            this.getRecetaExterna();
        })
  }

  eliminaItemExterno( item ){
    this._registroService.eliminaItemExterno( item )
        .subscribe( data => {
            console.log(data);
            this.recetaAbierta.reset();

            this.getRecetaExterna();
        })
  }

  getCatalogoIndicaciones(){
    this._busquedasService.getCatalogoIndicaciones()
        .subscribe( data => {
            console.log(data);
            this.catalogoIndicaciones = data;
        })
  }

  guardaIndicacion(){
    let datos = {
      obs: this.indicacionesReceta.controls.complementoIndicacion.value,
      tipoReceta: 6,
      folio: JSON.parse(sessionStorage.getItem('paciente')).folio,
    }

    console.log(datos);

    this._registroService.guardaIndicacion( datos )
        .subscribe( data => {
            console.log(data);
            this.getIndicacionesReceta();
        })
  }

  getIndicacionesReceta(){
    this._busquedasService.getIndicacionesReceta()
        .subscribe( data => {
            console.log(data);
            this.indicaciones = data;
        })
  }

  eliminaIndicacion(indicacion){
    this._registroService.eliminaIndicacionReceta( indicacion )
        .subscribe( data => {
            console.log(data);
            this.getIndicacionesReceta();
        })
  }

}
