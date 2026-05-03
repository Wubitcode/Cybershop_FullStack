import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-products',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './products.component.html',
  styleUrls: ['./products.component.css']
})
// 🛡️ Ensure this says 'ProductsComponent' and has 'export'
export class ProductsComponent {
  // Products management logic
}