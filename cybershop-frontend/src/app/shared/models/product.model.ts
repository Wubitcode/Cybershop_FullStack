export interface Product {
  product_id: number;
  sku: string;
  name: string;
  category: string;
  image: string;
  price: string | number; // JSON often returns decimals as strings
  description: string;
  stock: number;
  low_stock_threshold: number;
  isActive: number;
  // Optional: Add these if you plan to use the timestamps from your JSON
  createdAt?: string;
  updatedAt?: string | null;
}