
import { Routes } from '@angular/router';
import { PaymentComponent } from './payment/payment.component';
import { OrderSuccessComponent } from './order-success/order-success.component';
import { AuthGuard } from '../../core/guards/auth.guard';

export const checkoutRoutes: Routes = [
  { 
    path: 'payment', 
    component: PaymentComponent,
    canActivate: [AuthGuard] 
  },
  { 
    path: 'success', 
    component: OrderSuccessComponent 
  },
  { 
    path: '', 
    redirectTo: 'payment', 
    pathMatch: 'full' 
  }
];





/**import { Routes } from '@angular/router'; // 🛡️ Fixes 'Cannot find name Routes'
import { PaymentComponent } from './payment/payment.component'; // 🛡️ Fixes 'Cannot find name PaymentComponent'
import { OrderSuccessComponent } from './order-success/order-success.component';
import { AuthGuard } from '../../core/guards/auth.guard'; // 🛡️ Remember the Capital 'A'!

export const checkoutRoutes: Routes = [
  { 
    path: 'payment', 
    component: PaymentComponent,
    canActivate: [AuthGuard] 
  },
  { 
    path: 'success', 
    component: OrderSuccessComponent 
  },
  { 
    path: '', 
    redirectTo: 'payment', 
    pathMatch: 'full' 
  }
];*/