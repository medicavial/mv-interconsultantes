import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

//SERVICIOS
import { BusquedasService } from "./services/busquedas.service";
import { AuthService } from "./services/auth.service";
import { DatosLocalesService } from './services/datos-locales.service';

import { APP_ROUTING } from './app.routes';

import { AppComponent } from './app.component';
import { NavegacionComponent } from './components/navegacion/navegacion.component';
import { HomeComponent } from './components/home/home.component';
import { BusquedaComponent } from './components/busqueda/busqueda.component';
import { LoginComponent } from './components/login/login.component';
import { PacienteComponent } from './components/paciente/paciente.component';

@NgModule({
  declarations: [
    AppComponent,
    NavegacionComponent,
    HomeComponent,
    BusquedaComponent,
    LoginComponent,
    PacienteComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    ReactiveFormsModule,
    HttpModule,
    APP_ROUTING
  ],
  providers: [
    BusquedasService,
    AuthService,
    DatosLocalesService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
