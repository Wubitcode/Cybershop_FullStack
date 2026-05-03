import { Routes } from '@angular/router';
import { HomeComponent } from './features/home/home.component';
import { AuthGuard } from './core/guards/auth.guard';
import { AdminGuard } from './core/guards/admin.guard';
import { AboutComponent } from './features/about/about.component'; 
import { ContactComponent } from './features/contact/contact.component';
import { ServicesComponent } from './features/services/services.component';

export const routes: Routes = [
  {
    path: '',
    component: HomeComponent,
    pathMatch: 'full'
  },
  { 
    path: 'home', 
    component: HomeComponent 
  },
  { 
    path: 'about', 
    component: AboutComponent 
  }, 
  { path: 'services', component: ServicesComponent },
  { 
    path: 'contact', 
    component: ContactComponent 
  },
  {
    path: 'auth',
    loadChildren: () =>
      import('./features/auth/auth.routes').then(m => m.AUTH_ROUTES)
  },
  {
    path: 'shop',
    canActivate: [AuthGuard],
    // Change this to load children so detail and list both work
    loadChildren: () =>
      import('./features/shop/shop.routes').then(m => m.shopRoutes)
  },
  {
    path: 'checkout',
    canActivate: [AuthGuard],
    loadChildren: () =>
      import('./features/checkout/checkout.routes').then(m => m.checkoutRoutes)
  },
  {
    path: 'admin',
    canActivate: [AuthGuard, AdminGuard],
    loadChildren: () =>
      import('./features/admin/admin.routes').then(m => m.adminRoutes)
  },

  
  {
    path: 'cart',
    loadComponent: () =>
      import('./features/shop/cart/cart.component')
        .then(m => m.CartComponent)
  },
  // This "wildcard" catch-all handles any 404/wrong URLs
  { path: '**', redirectTo: '' }
];