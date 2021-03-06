import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';
import { LOCALE_ID } from '@angular/core';

//SERVICIOS
import { ApiConexionService } from './services/api-conexion.service';
import { BusquedasService } from "./services/busquedas.service";
import { AuthService } from "./services/auth.service";
import { DatosLocalesService } from './services/datos-locales.service';
import { RegistroDatosService } from './services/registro-datos.service';

import { APP_ROUTING } from './app.routes';

import { AppComponent } from './app.component';
import { NavegacionComponent } from './components/navegacion/navegacion.component';
import { HomeComponent } from './components/home/home.component';
import { BusquedaComponent } from './components/busqueda/busqueda.component';
import { LoginComponent } from './components/login/login.component';
import { PacienteComponent } from './components/paciente/paciente.component';
import { NotaSoapComponent } from './components/notas-medicas/nota-soap.component';
import { AsignacionPacientesComponent } from './components/asignacion-pacientes/asignacion-pacientes.component';
import { AdminUsariosComponent } from './components/admin-usarios/admin-usarios.component';
import { RecetaInternaComponent } from './components/notas-medicas/receta-interna.component';
import { ListadoAsignacionesComponent } from './components/asignacion-pacientes/listado-asignaciones.component';
import { CuentaComponent } from './components/cuenta/cuenta.component';

@NgModule({
  declarations: [
    AppComponent,
    NavegacionComponent,
    HomeComponent,
    BusquedaComponent,
    LoginComponent,
    PacienteComponent,
    NotaSoapComponent,
    AsignacionPacientesComponent,
    AdminUsariosComponent,
    RecetaInternaComponent,
    ListadoAsignacionesComponent,
    CuentaComponent,
  ],
  imports: [
    BrowserModule,
    FormsModule,
    ReactiveFormsModule,
    HttpModule,
    APP_ROUTING
  ],
  providers: [
    { provide: LOCALE_ID, useValue: "esMX" },
    ApiConexionService,
    BusquedasService,
    AuthService,
    DatosLocalesService,
    RegistroDatosService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
