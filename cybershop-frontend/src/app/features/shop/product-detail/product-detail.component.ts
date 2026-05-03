import { Component, inject, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { RouterLink } from '@angular/router';

import { Product } from '../../../shared/models/product.model';
import { CartService } from '../../../core/services/cart.service';

@Component({
  selector: 'app-product-detail',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './product-detail.component.html',
  styleUrl: './product-detail.component.css'
})
export class ProductDetailComponent implements OnInit {

  private route = inject(ActivatedRoute);
  private http = inject(HttpClient);
  private cartService = inject(CartService);

  product = signal<Product | null>(null);
  loading = signal<boolean>(true);
  error = signal<string>('');

  ngOnInit(): void {
  const id = Number(this.route.snapshot.paramMap.get('id'));

  if (!id) {
    this.error.set('Invalid product ID');
    this.loading.set(false);
    return;
  }

  this.http.get<Product[]>('http://localhost/cybershop/api/products.php')
    .subscribe({
      next: (data) => {

        const found = data.find(p => p.product_id === id);

        if (!found) {
          this.error.set('Product not found');
          this.loading.set(false);
          return;
        }

        this.product.set(found);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Server error');
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

  fixImage(img: string): string {
    if (!img) return 'images/default.png';
    if (img.startsWith('http')) return img;
    return `images/${img}`;
  }
}