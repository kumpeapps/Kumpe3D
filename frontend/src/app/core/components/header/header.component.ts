import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatBadgeModule } from '@angular/material/badge';
import { MatMenuModule } from '@angular/material/menu';
import { MatDividerModule } from '@angular/material/divider';
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
    MatDividerModule,
  ],
  template: `
    <header class="site-header">
      <div class="main-bar">
        <div class="container-fluid">
          <div class="header-content">
            <!-- Logo -->
            <div class="logo-header">
              <a routerLink="/">
                <img src="/assets/images/logo-white.png" alt="Kumpe3D Logo" class="logo-img">
              </a>
            </div>

            <!-- Navigation -->
            <nav class="header-nav">
              <a class="nav-link" routerLink="/products" routerLinkActive="active">Shop</a>
              <a class="nav-link" routerLink="/about" routerLinkActive="active">About</a>
              <a class="nav-link" routerLink="/contact" routerLinkActive="active">Contact</a>
            </nav>

            <!-- Right Side Icons -->
            <div class="header-right">
              @if (currentUser$ | async; as user) {
                <button mat-icon-button [matMenuTriggerFor]="userMenu" class="nav-icon">
                  <mat-icon>account_circle</mat-icon>
                </button>
                <mat-menu #userMenu="matMenu">
                  <div class="user-menu-header">
                    <mat-icon>account_circle</mat-icon>
                    <span>{{ user.email }}</span>
                  </div>
                  <mat-divider></mat-divider>
                  <a mat-menu-item routerLink="/orders">
                    <mat-icon>receipt</mat-icon>
                    <span>My Orders</span>
                  </a>
                  @if (isAdmin) {
                    <a mat-menu-item routerLink="/admin">
                      <mat-icon>admin_panel_settings</mat-icon>
                      <span>Admin Panel</span>
                    </a>
                  }
                  <mat-divider></mat-divider>
                  <button mat-menu-item (click)="logout()">
                    <mat-icon>logout</mat-icon>
                    <span>Logout</span>
                  </button>
                </mat-menu>
              } @else {
                <a mat-button routerLink="/auth/login" class="login-btn">Login</a>
              }
              
              <a routerLink="/cart" class="nav-icon cart-btn">
                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M1.08374 2.61947C1.08374 2.27429 1.36356 1.99447 1.70874 1.99447H3.29314C3.91727 1.99447 4.4722 2.39163 4.67352 2.98239L5.06379 4.1276H15.4584C17.6446 4.1276 19.4168 5.89981 19.4168 8.08593V11.5379C19.4168 13.7241 17.6446 15.4963 15.4584 15.4963H9.22182C7.30561 15.4963 5.66457 14.1237 5.32583 12.2377L4.00967 4.90953L3.49034 3.3856C3.46158 3.30121 3.3823 3.24447 3.29314 3.24447H1.70874C1.36356 3.24447 1.08374 2.96465 1.08374 2.61947ZM5.36374 5.3776L6.55614 12.0167C6.78791 13.3072 7.91073 14.2463 9.22182 14.2463H15.4584C16.9542 14.2463 18.1668 13.0337 18.1668 11.5379V8.08593C18.1668 6.59016 16.9542 5.3776 15.4584 5.3776H5.36374Z" fill="currentColor"/>
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M8.16479 17.8278C8.16479 17.1374 8.72444 16.5778 9.4148 16.5778H9.42313C10.1135 16.5778 10.6731 17.1374 10.6731 17.8278C10.6731 18.5182 10.1135 19.0778 9.42313 19.0778H9.4148C8.72444 19.0778 8.16479 18.5182 8.16479 17.8278Z" fill="currentColor"/>
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M14.8315 17.8278C14.8315 17.1374 15.3912 16.5778 16.0815 16.5778H16.0899C16.7802 16.5778 17.3399 17.1374 17.3399 17.8278C17.3399 18.5182 16.7802 19.0778 16.0899 19.0778H16.0815C15.3912 19.0778 14.8315 18.5182 14.8315 17.8278Z" fill="currentColor"/>
                </svg>
                @if (cartService.itemCount$() > 0) {
                  <span class="badge badge-circle">{{ cartService.itemCount$() }}</span>
                }
              </a>
            </div>
          </div>
        </div>
      </div>
    </header>
  `,
  styles: [`
    .site-header {
      background-color: var(--kumpe-primary);
      color: white;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .main-bar {
      padding: 0;
    }

    .container-fluid {
      max-width: 100%;
      padding: 0 2rem;
    }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      height: 70px;
      gap: 2rem;
    }

    .logo-header {
      flex-shrink: 0;
      
      a {
        display: block;
        line-height: 0;
      }
      
      .logo-img {
        height: 45px;
        width: auto;
      }
    }

    .header-nav {
      display: flex;
      gap: 0.5rem;
      flex-grow: 1;
      
      .nav-link {
        color: white;
        text-decoration: none;
        padding: 0.75rem 1.25rem;
        font-size: 1rem;
        font-weight: 500;
        border-radius: 6px;
        transition: background-color 0.3s ease;
        
        &:hover, &.active {
          background-color: rgba(255, 255, 255, 0.1);
        }
      }
    }

    .header-right {
      display: flex;
      align-items: center;
      gap: 1rem;
      
      .login-btn {
        color: white;
        font-weight: 500;
      }
      
      .nav-icon {
        color: white;
        cursor: pointer;
        position: relative;
        padding: 8px;
        border-radius: 6px;
        transition: background-color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        
        &:hover {
          background-color: rgba(255, 255, 255, 0.1);
        }
        
        mat-icon {
          font-size: 24px;
          width: 24px;
          height: 24px;
        }
      }
      
      .cart-btn {
        svg {
          width: 21px;
          height: 21px;
        }
        
        .badge {
          position: absolute;
          top: 0;
          right: 0;
          background-color: var(--kumpe-secondary);
          font-size: 0.7rem;
          min-width: 18px;
          height: 18px;
          padding: 0 4px;
          display: flex;
          align-items: center;
          justify-content: center;
        }
      }
    }

    ::ng-deep .user-menu-header {
      padding: 12px 16px;
      display: flex;
      align-items: center;
      gap: 12px;
      background-color: var(--kumpe-bg-light);
      
      mat-icon {
        color: var(--kumpe-primary);
      }
      
      span {
        font-weight: 500;
        color: var(--kumpe-title-color);
      }
    }

    @media (max-width: 768px) {
      .container-fluid {
        padding: 0 1rem;
      }
      
      .header-content {
        gap: 1rem;
      }
      
      .header-nav {
        display: none; // Hide nav on mobile, can add hamburger menu later
      }
      
      .logo-header .logo-img {
        height: 35px;
      }
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
