import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { AuthService } from '../services/auth.service';

/**
 * Generate or retrieve a guest session ID
 */
function getGuestSessionId(): string {
  const sessionKey = 'guest_session_id';
  let sessionId = localStorage.getItem(sessionKey);
  
  if (!sessionId) {
    // Generate a random session ID
    sessionId = 'guest_' + Math.random().toString(36).substring(2) + Date.now().toString(36);
    localStorage.setItem(sessionKey, sessionId);
  }
  
  return sessionId;
}

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const authService = inject(AuthService);
  const token = authService.getAccessToken();

  let headers = req.headers;

  if (token) {
    // Add authentication token for logged-in users
    headers = headers.set('Authorization', `Bearer ${token}`);
  } else {
    // Add session ID for guest users
    const sessionId = getGuestSessionId();
    headers = headers.set('X-Session-ID', sessionId);
  }

  const cloned = req.clone({ headers });
  return next(cloned);
};
