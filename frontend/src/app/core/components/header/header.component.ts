import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatBadgeModule } from '@angular/material/badge';
import { MatMenuModule } from '@angular/material/menu';
import { AuthService } from '../../services/auth.service';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    RouterLinkActive,
    MatToolbarModule,
    MatButtonModule,
    MatIconModule,
    MatBadgeModule,
    MatMenuModule,
  ],
  template: `
    <mat-toolbar color="primary">
      <div class="container toolbar-container">
        <a routerLink="/" class="logo">
          <mat-icon>view_in_ar</mat-icon>
          <span>Kumpe3D</span>
        </a>

        <nav class="nav-links">
          <a mat-button routerLink="/products" routerLinkActive="active">Products</a>
          <a mat-button routerLink="/cart" routerLinkActive="active">
            <mat-icon [matBadge]="cartService.itemCount$()" matBadgeColor="accent">shopping_cart</mat-icon>
          </a>
          
          @if (currentUser$ | async; as user) {
            <button mat-button [matMenuTriggerFor]="userMenu">
              <mat-icon>account_circle</mat-icon>
              {{ user.email }}
            </button>
            <mat-menu #userMenu="matMenu">
              <a mat-menu-item routerLink="/orders">
                <mat-icon>receipt</mat-icon>
                <span>My Orders</span>
              </a>
              @if (isAdmin) {
                <a mat-menu-item routerLink="/admin">
                  <mat-icon>admin_panel_settings</mat-icon>
                  <span>Admin</span>
                </a>
              }
              <button mat-menu-item (click)="logout()">
                <mat-icon>logout</mat-icon>
                <span>Logout</span>
              </button>
            </mat-menu>
          } @else {
            <a mat-button routerLink="/auth/login">Login</a>
            <a mat-raised-button color="accent" routerLink="/auth/register">Sign Up</a>
          }
        </nav>
      </div>
    </mat-toolbar>
  `,
  styles: [`
    .toolbar-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      color: inherit;
      font-size: 1.5rem;
      font-weight: 500;
    }

    .nav-links {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .nav-links a.active {
      background-color: rgba(255, 255, 255, 0.1);
    }
  `]
})
export class HeaderComponent {
  private authService = inject(AuthService);
  cartService = inject(CartService);
  
  currentUser$ = this.authService.currentUser$;
  
  get isAdmin(): boolean {
    return this.authService.isAdmin();
  }

  logout(): void {
    this.authService.logout();
  }
}
