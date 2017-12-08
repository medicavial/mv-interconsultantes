import { RouterModule, Routes } from '@angular/router';

import { LoginComponent } from './components/login/login.component';
import { HomeComponent } from './components/home/home.component';
import { BusquedaComponent } from './components/busqueda/busqueda.component';
import { PacienteComponent } from './components/paciente/paciente.component';
import { NotaSoapComponent } from './components/notas-medicas/nota-soap.component';
import { AsignacionPacientesComponent } from './components/asignacion-pacientes/asignacion-pacientes.component';
import { AdminUsariosComponent } from './components/admin-usarios/admin-usarios.component';
import { RecetaInternaComponent } from './components/notas-medicas/receta-interna.component';
import { ListadoAsignacionesComponent } from './components/asignacion-pacientes/listado-asignaciones.component';

import { AuthService } from "./services/auth.service";

const APP_ROUTES: Routes = [
  { path: 'login', component: LoginComponent },
  {
    path: 'home',
    component: HomeComponent,
    canActivate: [AuthService]
  },
  {
    path: 'usuarios',
    component: AdminUsariosComponent,
    canActivate: [AuthService],
    // children: [
    //   { path: 'nota-soap', component: NotaSoapComponent },
    // ]
  },
  {
    path: 'busqueda',
    component: BusquedaComponent,
    canActivate: [AuthService]
  },
  {
    path: 'asignacion',
    component: AsignacionPacientesComponent,
    canActivate: [AuthService]
  },
  {
    path: 'listadoAsignaciones',
    component: ListadoAsignacionesComponent,
    canActivate: [AuthService]
  },
  {
    path: 'paciente',
    component: PacienteComponent,
    canActivate: [AuthService],
    // children: [
    //   { path: 'nota-soap', component: NotaSoapComponent },
    // ]
  },
  {
    path: 'nota-soap',
    component: NotaSoapComponent,
    canActivate: [AuthService]
  },
  {
    path: 'recetas',
    component: RecetaInternaComponent,
    canActivate: [AuthService]
  },
  {
    path: '**',
    pathMatch:'full',
    redirectTo: 'login'
  },
  {
    path: '',
    pathMatch:'full',
    redirectTo: 'login'
  }
];

export const APP_ROUTING = RouterModule.forRoot(APP_ROUTES);
