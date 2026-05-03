import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-inventory',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './inventory.component.html',
  styleUrls: ['./inventory.component.css']
})
export class InventoryComponent implements OnInit {

  // =========================
  // Inject Angular services
  // =========================
  private http = inject(HttpClient); // for API calls
  private router = inject(Router);   // for navigation

  // =========================
  // API URL (your PHP backend)
  // =========================
  private apiUrl = 'http://localhost/cybershop/api/products.php';

  // =========================
  // STATE VARIABLES
  // =========================
  products: any[] = [];   // holds all products from backend
  isLoading = true;       // controls loading message
  showModal = false;      // controls modal visibility
  isEditMode = false;     // determines add or edit mode

  // =========================
  // SEARCH + FILTER (STEP 3)
  // =========================
  searchTerm: string = '';
  selectedCategory: string = '';

  // =========================
  // FORM MODEL (ADD / EDIT)
  // =========================
  currentProduct: any = {
    product_id: null,
    name: '',
    sku: '',
    price: 0,
    stock: 0,
    category: '',
    image: '',
    description: '',
    low_stock_threshold: 5
  };

  // =========================
  // ON LOAD
  // =========================
  ngOnInit(): void {
    this.fetchInventory();
  }

  // =========================
  // NAVIGATION
  // =========================
  goBack() {
    this.router.navigate(['/admin/dashboard']);
  }

  // =========================
  // FETCH PRODUCTS FROM PHP
  // =========================
  fetchInventory(): void {

    this.isLoading = true;

    this.http.get<any[]>(this.apiUrl).subscribe({
      next: (data) => {

        console.log("🔥 API DATA:", data);

        this.products = data || [];
        this.isLoading = false;
      },
      error: (err) => {
        console.error('API Error:', err);
        this.isLoading = false;
      }
    });
  }

  // =========================
  // FILTERED PRODUCTS (SEARCH)
  // =========================
  get filteredProducts() {
    return this.products.filter(p => {

      const matchesSearch =
        p.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        (p.sku || '').toLowerCase().includes(this.searchTerm.toLowerCase());

      const matchesCategory =
        this.selectedCategory === '' ||
        p.category === this.selectedCategory;

      return matchesSearch && matchesCategory;
    });
  }

  // =========================
  // OPEN ADD MODAL
  // =========================
  openAddModal() {

    this.isEditMode = false;

    // reset form
    this.currentProduct = {
      product_id: null,
      name: '',
      sku: '',
      price: 0,
      stock: 0,
      category: '',
      image: '',
      description: '',
      low_stock_threshold: 5
    };

    this.showModal = true;
  }

  // =========================
  // EDIT PRODUCT
  // =========================
  editAsset(product: any) {

    this.isEditMode = true;

    // clone product into form
    this.currentProduct = { ...product };

    this.showModal = true;
  }

  // =========================
  // SAVE PRODUCT (ADD + EDIT)
  // =========================
  saveProduct() {

    // =====================
    // UPDATE
    // =====================
    if (this.isEditMode) {

      this.http.post(this.apiUrl, {
        action: 'update',
        ...this.currentProduct
      }).subscribe({
        next: () => {
          console.log("✅ Product Updated");
          this.fetchInventory();
        },
        error: (err) => console.error('Update Error:', err)
      });

    } 

    // =====================
    // CREATE
    // =====================
    else {

      this.http.post(this.apiUrl, {
        action: 'create',
        ...this.currentProduct
      }).subscribe({
        next: () => {
          console.log("✅ Product Added");
          this.fetchInventory();
        },
        error: (err) => console.error('Add Error:', err)
      });
    }

    // close modal
    this.showModal = false;
  }

  // =========================
  // DELETE PRODUCT
  // =========================
  deleteAsset(id: number) {

    if (confirm("Are you sure you want to delete this asset?")) {

      this.http.delete(`${this.apiUrl}?id=${id}`).subscribe({
        next: () => {
          console.log("🗑️ Product Deleted");
          this.fetchInventory();
        },
        error: (err) => console.error("Delete Error:", err)
      });
    }
  }
}