import { Routes } from '@angular/router';
import { ProductListComponent } from './product-list/product-list.component';
import { ProductDetailComponent } from './product-detail/product-detail.component';
import { CartComponent } from './cart/cart.component';

export const shopRoutes: Routes = [
  { 
    path: '', 
    loadComponent: () => import('./product-list/product-list.component').then(m => m.ProductListComponent) 
  },
  { 
    path: 'product/:id', 
    loadComponent: () => import('./product-detail/product-detail.component').then(m => m.ProductDetailComponent) 
  },
  
];