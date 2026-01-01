export interface User {
  id: number;
  email: string;
  username?: string;
  first_name?: string;
  last_name?: string;
  is_active: boolean;
  is_verified: boolean;
  created_at: string;
  last_login?: string;
  roles: Role[];
}

export interface Role {
  id: number;
  name: string;
  description?: string;
  is_active: boolean;
  created_at: string;
}

export interface AuthTokens {
  access_token: string;
  refresh_token: string;
  token_type: string;
  expires_in: number;
}

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  email: string;
  password: string;
  username?: string;
  first_name?: string;
  last_name?: string;
}

export interface APIResponse<T> {
  data?: T;
  meta?: ResponseMetadata;
  error?: ErrorDetail;
}

export interface ResponseMetadata {
  page: number;
  per_page: number;
  total: number;
  total_pages: number;
}

export interface ErrorDetail {
  code: string;
  message: string;
  details?: any;
}
