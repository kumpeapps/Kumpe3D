import { Routes } from '@angular/router';

export const ADMIN_ROUTES: Routes = [
  {
    path: '',
    loadComponent: () => import('./dashboard/dashboard.component').then(m => m.DashboardComponent),
  },
  {
    path: 'users',
    loadComponent: () => import('./users/user-list.component').then(m => m.UserListComponent),
  },
  {
    path: 'products',
    loadComponent: () => import('./products/admin-product-list.component').then(m => m.AdminProductListComponent),
  },
  {
    path: 'parts',
    loadComponent: () => import('./parts/part-list.component').then(m => m.PartListComponent),
  },
  {
    path: 'orders',
    loadComponent: () => import('./orders/admin-order-list.component').then(m => m.AdminOrderListComponent),
  },
];
