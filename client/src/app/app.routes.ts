import { RouterModule, Routes } from '@angular/router';

import { LoginComponent } from './components/login/login.component';
import { HomeComponent } from './components/home/home.component';
import { BusquedaComponent } from './components/busqueda/busqueda.component';

const APP_ROUTES: Routes = [
  {
    path: 'login',
    component: LoginComponent
  },
  {
    path: 'home',
    component: HomeComponent
  },
  {
    path: 'busqueda',
    component: BusquedaComponent
  },
  {
    path: '**',
    pathMatch:'full',
    redirectTo: 'home'
  },
  {
    path: '',
    pathMatch:'full',
    redirectTo: 'home'
  }
];

export const APP_ROUTING = RouterModule.forRoot(APP_ROUTES);
