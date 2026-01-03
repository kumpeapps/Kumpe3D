import { Routes } from '@angular/router';

export const ADMIN_ROUTES: Routes = [
  {
    path: '',
    loadComponent: () => import('./admin-layout.component').then(m => m.AdminLayoutComponent),
    children: [
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
        path: 'products/new',
        loadComponent: () => import('./products/product-form.component').then(m => m.ProductFormComponent),
      },
      {
        path: 'products/:id/edit',
        loadComponent: () => import('./products/product-form.component').then(m => m.ProductFormComponent),
      },
      {
        path: 'parts',
        loadComponent: () => import('./parts/parts-list.component').then(m => m.PartsListComponent),
      },
      {
        path: 'orders',
        loadComponent: () => import('./orders/admin-order-list.component').then(m => m.AdminOrderListComponent),
      },
    ]
  },
];
