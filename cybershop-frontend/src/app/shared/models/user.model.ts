export interface User {
  user_id: number;
  name: string;
  email: string;
  password?: string; // Optional because we don't store passwords on the frontend
  role: 'admin' | 'user';
  last_login?: string;
  failed_attempts: number;
  is_locked: boolean;
  is_active: boolean;
}