import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CartService } from '../../../core/services/cart.service';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './cart.component.html',
  styleUrls: ['./cart.component.css']
})
export class CartComponent {

  private cartService = inject(CartService);

  cartItems = this.cartService.cartItems;
  totalAmount = this.cartService.totalAmount;
  cartCount = this.cartService.cartCount;

  updateQuantity(productId: number, qty: number) {
    this.cartService.updateQty(productId, qty);
  }

  removeItem(productId: number) {
    this.cartService.removeFromCart(productId);
  }

  clearCart() {
    this.cartService.clearCart();
  }

  checkout() {
    console.log(this.cartItems());
    alert('Processing order...');
  }

  trackById(index: number, item: any) {
    return item.product_id;
  }
}