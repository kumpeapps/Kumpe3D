import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive, RouterOutlet, Router } from '@angular/router';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatListModule } from '@angular/material/list';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatToolbarModule } from '@angular/material/toolbar';

@Component({
  selector: 'app-admin-layout',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    RouterLinkActive,
    RouterOutlet,
    MatSidenavModule,
    MatListModule,
    MatIconModule,
    MatButtonModule,
    MatToolbarModule,
  ],
  template: `
    <mat-sidenav-container class="admin-container">
      <mat-sidenav mode="side" opened class="admin-sidenav">
        <div class="sidenav-header">
          <h2>Admin Panel</h2>
        </div>
        
        <mat-nav-list>
          <a mat-list-item routerLink="/admin" [routerLinkActive]="['active']" [routerLinkActiveOptions]="{exact: true}">
            <mat-icon>dashboard</mat-icon>
            <span>Dashboard</span>
          </a>
          
          <a mat-list-item routerLink="/admin/products" [routerLinkActive]="['active']">
            <mat-icon>inventory_2</mat-icon>
            <span>Products</span>
          </a>
          
          <a mat-list-item routerLink="/admin/orders" [routerLinkActive]="['active']">
            <mat-icon>receipt</mat-icon>
            <span>Orders</span>
          </a>
          
          <a mat-list-item routerLink="/admin/users" [routerLinkActive]="['active']">
            <mat-icon>people</mat-icon>
            <span>Users</span>
          </a>
          
          <a mat-list-item routerLink="/admin/parts" [routerLinkActive]="['active']">
            <mat-icon>build</mat-icon>
            <span>Inventory</span>
          </a>

          <mat-divider class="divider"></mat-divider>
          
          <a mat-list-item routerLink="/" class="back-to-site">
            <mat-icon>arrow_back</mat-icon>
            <span>Back to Site</span>
          </a>
        </mat-nav-list>
      </mat-sidenav>

      <mat-sidenav-content>
        <router-outlet></router-outlet>
      </mat-sidenav-content>
    </mat-sidenav-container>
  `,
  styles: [`
    .admin-container {
      height: 100vh;
    }

    .admin-sidenav {
      width: 260px;
      background: var(--kumpe-secondary);
      color: white;

      .sidenav-header {
        padding: 24px 16px;
        background: linear-gradient(135deg, var(--kumpe-primary) 0%, var(--kumpe-primary-dark) 100%);
        
        h2 {
          margin: 0;
          font-size: 20px;
          font-weight: 600;
        }
      }

      mat-nav-list {
        padding-top: 16px;

        a {
          color: rgba(255, 255, 255, 0.7) !important;
          margin: 4px 8px;
          border-radius: 8px;
          transition: all 0.2s;

          mat-icon {
            margin-right: 16px;
            color: rgba(255, 255, 255, 0.7) !important;
          }

          span {
            color: rgba(255, 255, 255, 0.7) !important;
          }

          &:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white !important;

            mat-icon {
              color: white !important;
            }

            span {
              color: white !important;
            }
          }

          &.active {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;

            mat-icon {
              color: var(--kumpe-primary) !important;
            }

            span {
              color: white !important;
            }
          }

          &.back-to-site {
            color: rgba(255, 255, 255, 0.5) !important;
            font-size: 14px;

            mat-icon {
              color: rgba(255, 255, 255, 0.5) !important;
            }

            span {
              color: rgba(255, 255, 255, 0.5) !important;
            }
          }
        }

        .divider {
          margin: 16px 0;
          background: rgba(255, 255, 255, 0.1);
        }
      }
    }

    mat-sidenav-content {
      background: var(--kumpe-bg-grey);
      min-height: 100vh;
    }
  `]
})
export class AdminLayoutComponent {}
