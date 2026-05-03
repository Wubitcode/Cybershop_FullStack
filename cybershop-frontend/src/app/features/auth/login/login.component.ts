import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {

  private authService = inject(AuthService);
  private router = inject(Router);

  credentials = {
    email: '',
    password: ''
  };

  errorMessage = '';
  loading = false;

  onLogin() {

    if (this.loading) return;

    this.loading = true;
    this.errorMessage = '';

    this.authService.login(this.credentials).subscribe({
      next: (res: any) => {

        this.loading = false;

        const user = res?.user;

        if (!user) {
          this.errorMessage = 'Invalid server response';
          return;
        }

        localStorage.setItem('user_session', JSON.stringify(user));

        const role = (user.role || '').toLowerCase();

        if (role === 'admin') {
          this.router.navigate(['/admin/dashboard']);
        } else {
          this.router.navigate(['/shop']);
        }
      },

      error: () => {
        this.loading = false;
        this.errorMessage = 'Server error. Try again later.';
      }
    });
  }
}




/**import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../../core/services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {

  private authService = inject(AuthService);
  private router = inject(Router);

  credentials = {
    email: '',
    password: ''
  };

  errorMessage = '';
  loading = false;

  onLogin() {

    this.loading = true;
    this.errorMessage = '';

    this.authService.login(this.credentials).subscribe({
      next: (res: any) => {

        this.loading = false;

        console.log('LOGIN RESPONSE:', res);

        // ✅ SAFETY CHECK (VERY IMPORTANT)
        if (!res) {
          this.errorMessage = 'No response from server';
          return;
        }

        // ✅ SUCCESS CHECK (support both formats)
        if (res.status === 'success' || res.user) {

          const user = res.user;

          if (!user) {
            this.errorMessage = 'Invalid user data';
            return;
          }

          // Save session
          localStorage.setItem('user_session', JSON.stringify(user));

          // FORCE ROLE LOWERCASE SAFETY
          const role = (user.role || '').toLowerCase();

          console.log('USER ROLE:', role);

          // 🔥 REDIRECT LOGIC
          if (role === 'admin') {
            this.router.navigate(['/admin/dashboard']);
          } else {
            this.router.navigate(['/shop']); // ✅ USER ALWAYS GO HERE
          }

        } else {
          this.errorMessage = 'Invalid email or password';
        }
      },

      error: (err) => {
        this.loading = false;
        console.error('LOGIN ERROR:', err);
        this.errorMessage = 'Server error. Try again later.';
      }
    });
  }
}*/