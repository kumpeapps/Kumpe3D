import { HttpInterceptorFn } from '@angular/common/http';
import { environment } from '@environments/environment';

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

/**
 * Get access token directly from localStorage to avoid circular dependency
 */
function getAccessToken(): string | null {
  const tokensJson = localStorage.getItem(environment.tokenStorageKey);
  if (!tokensJson) return null;
  
  try {
    const tokens = JSON.parse(tokensJson);
    return tokens.access_token || null;
  } catch {
    return null;
  }
}

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const token = getAccessToken();

  console.log('Auth Interceptor - URL:', req.url, 'Token exists:', !!token);

  let headers = req.headers;

  if (token) {
    // Add authentication token for logged-in users
    headers = headers.set('Authorization', `Bearer ${token}`);
    console.log('Auth Interceptor - Added Bearer token to headers');
  } else {
    // Add session ID for guest users
    const sessionId = getGuestSessionId();
    headers = headers.set('X-Session-ID', sessionId);
    console.log('Auth Interceptor - Added guest session ID:', sessionId);
  }

  const cloned = req.clone({ headers });
  return next(cloned);
};
