import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms'; 
import { Router, RouterModule } from '@angular/router';
import { CartService } from '../../../core/services/cart.service';

@Component({
  selector: 'app-payment',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './payment.component.html',
  styleUrls: ['./payment.component.css']
})
export class PaymentComponent {
  private cartService = inject(CartService);
  private router = inject(Router);

  isProcessing = signal(false);
  totalAmount = this.cartService.totalAmount;

  // Form Data
  paymentData = {
    cardName: '',
    cardNumber: '',
    expiry: '',
    cvv: '',
    billingAddress: 'Ajax, Ontario' // Defaulting to your local area
  };

  processPayment() {
    if (this.isProcessing()) return;

    this.isProcessing.set(true);
    
    // Send to PHP backend via CartService
    this.cartService.placeOrder(this.cartService.cartItems()).subscribe({
      next: () => {
        // Clear cart signals and storage on success
        this.cartService.clearCart();
        this.router.navigate(['/checkout/success']);
      },
      error: (err) => {
        console.error('Security Breach or Network Error:', err);
        alert('Transaction failed. Please verify your payment details.');
        this.isProcessing.set(false);
      }
    });
  }
}