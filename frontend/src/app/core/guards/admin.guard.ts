import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

export const adminGuard: CanActivateFn = () => {
  const authService = inject(AuthService);
  const router = inject(Router);

  // Check if authenticated first
  if (!authService.isAuthenticated()) {
    router.navigate(['/auth/login']);
    return false;
  }

  // Check if user is admin (will be loaded by now or loading in background)
  if (authService.isAdmin()) {
    return true;
  }

  // If user not loaded yet but tokens exist, allow access
  // (isAdmin returns false if user is undefined)
  // The UI will show loading state until user loads
  const currentUser = authService.getCurrentUser();
  if (currentUser === undefined) {
    // User still loading, allow access
    return true;
  }

  // User loaded but not admin
  router.navigate(['/']);
  return false;
};
