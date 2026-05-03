import { Component, inject, signal, OnInit } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { CartService } from '../../../core/services/cart.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './header.component.html',
  styleUrl: './header.component.css'
})
export class HeaderComponent implements OnInit {

  private cartService = inject(CartService);
  private router = inject(Router);

  // 🛒 CART
  cartCount = this.cartService.cartCount;

  // 📱 UI STATE (FIXED)
  isMenuOpen = signal(false);
  pagesOpen = signal(false);
  accountOpen = signal(false);

  // 🔐 AUTH STATE
  isLoggedIn = signal(false);
  isAdmin = signal(false);

  constructor() {
    this.router.events.subscribe(() => {
      this.syncAuth();
    });
  }

  ngOnInit(): void {
    this.syncAuth();
  }

  syncAuth(): void {
    const user = localStorage.getItem('user_session');

    if (!user) {
      this.isLoggedIn.set(false);
      this.isAdmin.set(false);
      return;
    }

    try {
      const parsedUser = JSON.parse(user);
      this.isLoggedIn.set(true);
      this.isAdmin.set(parsedUser?.role === 'admin');
    } catch {
      this.isLoggedIn.set(false);
      this.isAdmin.set(false);
    }
  }

  // ✅ UI METHODS
  toggleMenu() {
    this.isMenuOpen.update(v => !v);
  }

  togglePages() {
    this.pagesOpen.update(v => !v);
    this.accountOpen.set(false);
  }

  toggleAccount() {
    this.accountOpen.update(v => !v);
    this.pagesOpen.set(false);
  }

  closeMenu() {
    this.isMenuOpen.set(false);
    this.pagesOpen.set(false);
    this.accountOpen.set(false);
  }

  // 🔐 LOGOUT
  logout() {
    localStorage.removeItem('user_session');
    this.syncAuth();
    this.closeMenu();
    this.router.navigate(['/auth/login']);
  }
}