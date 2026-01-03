import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { environment } from '@environments/environment';
import {
  User,
  AuthTokens,
  LoginRequest,
  RegisterRequest,
  APIResponse,
} from '../models/auth.model';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private http = inject(HttpClient);
  private router = inject(Router);
  private apiUrl = `${environment.apiUrl}/auth`;

  private currentUserSubject = new BehaviorSubject<User | null | undefined>(undefined);
  public currentUser$ = this.currentUserSubject.asObservable();

  private tokensSubject = new BehaviorSubject<AuthTokens | null>(this.getStoredTokens());
  public tokens$ = this.tokensSubject.asObservable();

  constructor() {
    // Load user on app start if tokens exist
    const tokens = this.getStoredTokens();
    console.log('AuthService constructor - tokens exist:', !!tokens);
    if (tokens) {
      console.log('AuthService constructor - calling loadCurrentUser()');
      this.loadCurrentUser();
    } else {
      // No tokens, user is definitely not logged in
      console.log('AuthService constructor - no tokens, setting user to null');
      this.currentUserSubject.next(null);
    }
  }

  register(data: RegisterRequest): Observable<APIResponse<User>> {
    return this.http.post<APIResponse<User>>(`${this.apiUrl}/register`, data);
  }

  login(credentials: LoginRequest): Observable<APIResponse<AuthTokens>> {
    return this.http.post<APIResponse<AuthTokens>>(`${this.apiUrl}/login`, credentials).pipe(
      tap((response) => {
        if (response.data) {
          this.storeTokens(response.data);
          this.tokensSubject.next(response.data);
          this.loadCurrentUser();
        }
      })
    );
  }

  logout(): void {
    this.http.post(`${this.apiUrl}/logout`, {}).subscribe();
    this.clearTokens();
    this.currentUserSubject.next(null);
    this.tokensSubject.next(null);
    this.router.navigate(['/']);
  }

  refreshToken(): Observable<APIResponse<AuthTokens>> {
    const tokens = this.getStoredTokens();
    if (!tokens?.refresh_token) {
      throw new Error('No refresh token available');
    }

    return this.http
      .post<APIResponse<AuthTokens>>(`${this.apiUrl}/refresh`, {
        refresh_token: tokens.refresh_token,
      })
      .pipe(
        tap((response) => {
          if (response.data) {
            this.storeTokens(response.data);
            this.tokensSubject.next(response.data);
          }
        })
      );
  }

  loadCurrentUser(): void {
    console.log('loadCurrentUser() called, making request to /auth/me');
    this.http.get<APIResponse<User>>(`${this.apiUrl}/me`).subscribe({
      next: (response) => {
        console.log('loadCurrentUser() - received response:', response);
        if (response.data) {
          this.currentUserSubject.next(response.data);
        } else {
          // No user data in response, clear tokens
          console.log('loadCurrentUser() - no user data, clearing tokens');
          this.clearTokens();
          this.currentUserSubject.next(null);
        }
      },
      error: (error) => {
        console.error('Failed to load user:', error);
        // Clear tokens and logout on 401 Unauthorized
        if (error.status === 401) {
          this.clearTokens();
        }
        // Always set to null so guards can proceed
        this.currentUserSubject.next(null);
      },
    });
  }

  isAuthenticated(): boolean {
    return !!this.getStoredTokens();
  }

  getCurrentUser(): User | null | undefined {
    return this.currentUserSubject.value;
  }

  isAdmin(): boolean {
    const user = this.currentUserSubject.value;
    if (user === undefined) {
      // User not loaded yet
      return false;
    }
    return user?.roles?.some((role) => role.name === 'admin') ?? false;
  }

  getAccessToken(): string | null {
    return this.getStoredTokens()?.access_token ?? null;
  }

  private storeTokens(tokens: AuthTokens): void {
    localStorage.setItem(environment.tokenStorageKey, JSON.stringify(tokens));
  }

  private getStoredTokens(): AuthTokens | null {
    const tokens = localStorage.getItem(environment.tokenStorageKey);
    return tokens ? JSON.parse(tokens) : null;
  }

  private clearTokens(): void {
    localStorage.removeItem(environment.tokenStorageKey);
  }
}
