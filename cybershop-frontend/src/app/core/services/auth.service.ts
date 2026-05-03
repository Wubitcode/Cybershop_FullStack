import { Injectable, signal, inject } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { tap } from 'rxjs/operators';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private http = inject(HttpClient);

  currentUser = signal<any | null>(null);

  private apiUrl = 'http://localhost/cybershop/api/auth.php';

  // ================= REGISTER =================
  register(userData: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}?action=register`, userData);
  }

  // ================= LOGIN =================
  login(credentials: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}?action=login`, credentials).pipe(
      tap(res => {
        if (res.status === 'success') {
          this.currentUser.set(res.user);

          // ✅ store BOTH
          localStorage.setItem('user_session', JSON.stringify(res.user));
          localStorage.setItem('token', res.token);
        }
      })
    );
  }

  // ================= PASSWORD RESET =================
  requestPasswordReset(email: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}?action=forgot_password`, { email });
  }

  // ================= LOGOUT =================
  logout() {
    this.currentUser.set(null);
    localStorage.removeItem('user_session');
    localStorage.removeItem('token');
  }

  // ================= GET TOKEN =================
  getToken(): string | null {
    return localStorage.getItem('token');
  }

  // ================= CHECK LOGIN =================
  isLoggedIn(): boolean {
    return !!this.getToken();
  }
}