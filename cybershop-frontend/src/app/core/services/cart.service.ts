import { Injectable, signal, computed, effect } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Product } from '../../shared/models/product.model';
import { Observable } from 'rxjs';

export interface CartItem {
  product_id: number;
  name: string;
  price: number;
  qty: number;
  
}

@Injectable({
  providedIn: 'root',
})
export class CartService {

  private apiUrl = 'http://localhost/cybershop/api';

  // 🛒 CART STATE
  cartItems = signal<CartItem[]>([]);

  // 🧮 CART TOTAL COUNT
  cartCount = computed(() =>
    this.cartItems().reduce((sum, item) => sum + item.qty, 0)
  );

  // 💰 TOTAL AMOUNT
  totalAmount = computed(() =>
    this.cartItems().reduce((sum, item) => sum + item.qty * item.price, 0)
  );

  constructor(private http: HttpClient) {

    // 🔁 LOAD CART FROM STORAGE
    const savedCart = localStorage.getItem('cybershop_cart');

    if (savedCart) {
      try {
        this.cartItems.set(JSON.parse(savedCart));
      } catch (e) {
        console.error('Cart parse error:', e);
      }
    }

    // 💾 AUTO SAVE CART
    effect(() => {
      localStorage.setItem(
        'cybershop_cart',
        JSON.stringify(this.cartItems())
      );
    });
  }

  // ======================================================
  // 🛒 ADD TO CART
  // ======================================================
  addToCart(product: Product) {

    this.cartItems.update(items => {

      const id = product.product_id;

      const existing = items.find(i => i.product_id === id);

      // If already exists → increase qty
      if (existing) {

        if (existing.qty >= product.stock) {
          alert('Maximum stock reached for this product');
          return items;
        }

        return items.map(i =>
          i.product_id === id
            ? { ...i, qty: i.qty + 1 }
            : i
        );
      }

      // New item
      return [
        ...items,
        {
          product_id: id,
          name: product.name,
          price: Number(product.price),
          qty: 1
        }
      ];
    });
  }

  // ======================================================
  // ❌ REMOVE ITEM
  // ======================================================
  removeFromCart(product_id: number) {
    this.cartItems.update(items =>
      items.filter(i => i.product_id !== product_id)
    );
  }

  // ======================================================
  // 🔢 UPDATE QUANTITY
  // ======================================================
  updateQty(product_id: number, qty: number) {

    if (qty <= 0) {
      this.removeFromCart(product_id);
      return;
    }

    this.cartItems.update(items =>
      items.map(i =>
        i.product_id === product_id
          ? { ...i, qty }
          : i
      )
    );
  }

  // ======================================================
  // 🧹 CLEAR CART
  // ======================================================
  clearCart() {
    this.cartItems.set([]);
    localStorage.removeItem('cybershop_cart');
  }

  // ======================================================
  // 📦 PLACE ORDER
  // ======================================================
  placeOrder(items: CartItem[]): Observable<any> {

    const token = localStorage.getItem('auth_token');

    const headers = new HttpHeaders({
      Authorization: `Bearer ${token}`,
      'Content-Type': 'application/json'
    });

    return this.http.post(
      `${this.apiUrl}/order_create.php`,
      {
        items,
        location: 'Ajax, Ontario'
      },
      { headers }
    );
  }
}