import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router'; // For the return link
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-forgot-password',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './forgot-password.component.html',
  styleUrl: './forgot-password.component.css' // 🛡️ Changed to singular 'styleUrl'
})
export class ForgotPasswordComponent {
  private authService = inject(AuthService);
  
  email: string = '';
  message: string = '';
  isError: boolean = false;
  isProcessing: boolean = false;

  onResetRequest() {
    if (!this.email || !this.email.includes('@')) {
      this.isError = true;
      this.message = 'VALID_TARGET_EMAIL_REQUIRED';
      return;
    }

    this.isError = false;
    this.message = '';
    this.isProcessing = true;

    this.authService.requestPasswordReset(this.email).subscribe({
      next: (res: any) => {
        this.isError = false;
        this.message = 'RECOVERY_UPLINK_SENT_CHECK_INBOX';
        this.isProcessing = false;
      },
      error: (err: any) => {
        this.isError = true;
        this.message = err.error?.message || 'UPLINK_TIMEOUT_RETRY_LATER';
        this.isProcessing = false;
      }
    });
  }
}