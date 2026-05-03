import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent {

  private authService = inject(AuthService);
  private router = inject(Router);

  userData = {
    name: '',
    email: '',
    password: '',
    confirmPassword: ''
  };

  errorMessage: string = '';
  loading: boolean = false;

  onRegister() {
    this.errorMessage = '';

    // ✅ Validation
    if (!this.userData.name || !this.userData.email || !this.userData.password) {
      this.errorMessage = 'All fields are required.';
      return;
    }

    if (this.userData.password.length < 6) {
      this.errorMessage = 'Password must be at least 6 characters.';
      return;
    }

    if (this.userData.password !== this.userData.confirmPassword) {
      this.errorMessage = 'Passwords do not match.';
      return;
    }

    const payload = {
      name: this.userData.name.trim(),
      email: this.userData.email.toLowerCase().trim(),
      password: this.userData.password.trim()
    };

    this.loading = true;

    this.authService.register(payload).subscribe({
      next: (res: any) => {
        this.loading = false;

        // ✅ flexible handling (safe)
        if (res.status === 'success' || res.message) {
          alert("REGISTRATION COMPLETE");
          this.router.navigate(['/auth/login']);
        }
      },
      error: (err: any) => {
        this.loading = false;
        this.errorMessage = err.error?.message || "SERVER ERROR";
      }
    });
  }
}