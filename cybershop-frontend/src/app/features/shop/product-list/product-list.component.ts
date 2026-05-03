import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { RouterLink } from '@angular/router';


import { CartService } from '../../../core/services/cart.service';
import { Product } from '../../../shared/models/product.model';

@Component({
  selector: 'app-product-list',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './product-list.component.html',
  styleUrls: ['./product-list.component.css'] // ✅ FIXED
})
export class ProductListComponent implements OnInit {

  private http = inject(HttpClient);
  private cartService = inject(CartService);

  products = signal<Product[]>([]);
  errorMessage = signal<string>('');
  loading = signal<boolean>(false);

  ngOnInit(): void {
    this.loadProducts();
  }

  loadProducts(): void {
    this.loading.set(true);

    this.http.get<Product[]>('http://localhost/cybershop/api/products.php')
      .subscribe({
        next: (data) => {
          this.products.set(data);
          this.loading.set(false);
        },
        error: (err) => {
          console.error(err);
          this.errorMessage.set('Failed to load products');
          this.loading.set(false);
        }
      });
  }

  addToCart(product: Product) {
    const current = this.cartService.cartItems();

    this.cartService.cartItems.set([
      ...current,
      {
        product_id: product.product_id,
        name: product.name,
        price: Number(product.price),
        qty: 1
      }
    ]);
  }

  // ✅ KEEP THIS (use in HTML)
  fixImage(image: string): string {
    if (!image) return 'images/default.png';

    if (image.startsWith('http')) return image;

    return `images/${image}`;
  }
}