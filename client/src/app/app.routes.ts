import { RouterModule, Routes } from '@angular/router';

import { LoginComponent } from './components/login/login.component';
import { HomeComponent } from './components/home/home.component';
import { BusquedaComponent } from './components/busqueda/busqueda.component';

import { AuthService } from "./services/auth.service";

const APP_ROUTES: Routes = [
  {
    path: 'login',
    component: LoginComponent
  },
  {
    path: 'home',
    component: HomeComponent,
    canActivate: [AuthService]
  },
  {
    path: 'busqueda',
    component: BusquedaComponent,
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
