export interface Order {
  order_id: number;
  user_id: number;
  total: number;
  order_date: string;
  status: string;
  shipping_address?: string;
  postal_code?: string;
  phone_number?: string;
}

export interface OrderItem {
  item_id: number;
  order_id: number;
  product_id: number;
  price?: number;
  quantity: number;
  price_at_purchase?: number;
}