import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';

// Corrected path based on your folder structure
import { ProductService } from '../../core/services/product.service'; 
import { Product } from '../../shared/models/product.model';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  featuredProducts = signal<Product[]>([]);
  systemStatus = signal<string>('SECURE');
  currentTime = signal<string>(new Date().toLocaleTimeString());

  constructor(private productService: ProductService) {}

  ngOnInit(): void {
    this.fetchFeaturedProducts();
    
    // Updates the status bar clock every second
    setInterval(() => {
      this.currentTime.set(new Date().toLocaleTimeString());
    }, 1000);
  }

  fetchFeaturedProducts(): void {
  this.productService.getProducts().subscribe({
    next: (data: Product[]) => {
      // The IDs you want to show
      const targetIds = [15, 5, 8];
      
      // We use a Map to ensure only ONE instance of each ID is allowed
      const uniqueItems = new Map<number, Product>();
      
      data.forEach(item => {
        if (targetIds.includes(item.product_id)) {
          uniqueItems.set(item.product_id, item);
        }
      });

      const filtered = Array.from(uniqueItems.values());
      
      // Update the signal with unique items only
      this.featuredProducts.set(filtered);
    },
    error: (err: any) => {
      console.error('Core Log Error:', err);
      this.systemStatus.set('OFFLINE');
    }
  });
}
}