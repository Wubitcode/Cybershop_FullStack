import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Product } from '../../shared/models/product.model';

@Injectable({
  providedIn: 'root'
})
export class ProductService {

  private apiUrl = 'http://localhost/cybershop/api/products.php';

  constructor(private http: HttpClient) {}

  // =========================
  // GET ALL PRODUCTS
  // =========================
  getProducts(): Observable<Product[]> {
    return this.http.get<Product[]>(this.apiUrl);
  }

  // =========================
  // GET ONE PRODUCT
  // =========================
  getProductById(id: number): Observable<Product> {
    return this.http.get<Product>(`${this.apiUrl}?id=${id}`);
  }

  // =========================
  // FEATURED PRODUCTS
  // =========================
  getFeaturedProducts(): Observable<Product[]> {
    return this.http.get<Product[]>(`${this.apiUrl}?featured=true`);
  }

  // =========================
  // ADD PRODUCT (POST)
  // =========================
  addProduct(product: Product): Observable<any> {
    return this.http.post(this.apiUrl, product);
  }

  // =========================
  // UPDATE PRODUCT (PUT)
  // =========================
  updateProduct(product: Product): Observable<any> {
    return this.http.put(this.apiUrl, product);
  }

  // =========================
  // DELETE PRODUCT (DELETE)
  // =========================
  deleteProduct(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}?id=${id}`);
  }
}