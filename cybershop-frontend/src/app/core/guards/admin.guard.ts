import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';

@Injectable({ providedIn: 'root' })
export class AdminGuard implements CanActivate {

  constructor(private router: Router) {}

  canActivate(): boolean {
    const user = localStorage.getItem('user_session');

    if (!user) {
      this.router.navigate(['/auth/login']);
      return false;
    }

    const parsedUser = JSON.parse(user);

    if (parsedUser.role !== 'admin') {
      this.router.navigate(['/shop']); // redirect normal users
      return false;
    }

    return true;
  }
}