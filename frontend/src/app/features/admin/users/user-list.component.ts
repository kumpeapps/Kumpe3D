import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { HttpClient } from '@angular/common/http';
import { environment } from '@environments/environment';

interface User {
  id: number;
  email: string;
  username: string;
  first_name?: string;
  last_name?: string;
  phone?: string;
  is_active: boolean;
  is_verified: boolean;
  created_at: string;
  roles: Array<{
    id: number;
    name: string;
    is_active: boolean;
  }>;
}

@Component({
  selector: 'app-user-list',
  standalone: true,
  imports: [
    CommonModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatChipsModule,
    MatTooltipModule,
    MatSnackBarModule,
  ],
  template: `
    <div class="container">
      <div class="header">
        <div>
          <h1>User Management</h1>
          <p>Manage user accounts and permissions</p>
        </div>
      </div>

      @if (loading) {
        <div class="loading">Loading users...</div>
      } @else if (users.length === 0) {
        <div class="empty-state">
          <mat-icon>people</mat-icon>
          <h3>No Users Found</h3>
          <p>No users are registered yet</p>
        </div>
      } @else {
        <div class="table-container">
          <table mat-table [dataSource]="users" class="users-table">
            <!-- Email Column -->
            <ng-container matColumnDef="email">
              <th mat-header-cell *matHeaderCellDef>Email</th>
              <td mat-cell *matCellDef="let user">
                <div class="user-info">
                  <strong>{{ user.email }}</strong>
                  @if (!user.is_verified) {
                    <mat-chip color="warn" class="status-chip">Unverified</mat-chip>
                  }
                </div>
              </td>
            </ng-container>

            <!-- Username Column -->
            <ng-container matColumnDef="username">
              <th mat-header-cell *matHeaderCellDef>Username</th>
              <td mat-cell *matCellDef="let user">{{ user.username }}</td>
            </ng-container>

            <!-- Name Column -->
            <ng-container matColumnDef="name">
              <th mat-header-cell *matHeaderCellDef>Name</th>
              <td mat-cell *matCellDef="let user">
                @if (user.first_name || user.last_name) {
                  {{ user.first_name }} {{ user.last_name }}
                } @else {
                  —
                }
              </td>
            </ng-container>

            <!-- Phone Column -->
            <ng-container matColumnDef="phone">
              <th mat-header-cell *matHeaderCellDef>Phone</th>
              <td mat-cell *matCellDef="let user">{{ user.phone || '—' }}</td>
            </ng-container>

            <!-- Roles Column -->
            <ng-container matColumnDef="roles">
              <th mat-header-cell *matHeaderCellDef>Roles</th>
              <td mat-cell *matCellDef="let user">
                <div class="roles">
                  @for (role of user.roles; track role.id) {
                    <mat-chip [color]="role.name === 'admin' ? 'accent' : 'primary'">
                      {{ role.name }}
                    </mat-chip>
                  }
                  @if (user.roles.length === 0) {
                    <mat-chip>user</mat-chip>
                  }
                </div>
              </td>
            </ng-container>

            <!-- Status Column -->
            <ng-container matColumnDef="status">
              <th mat-header-cell *matHeaderCellDef>Status</th>
              <td mat-cell *matCellDef="let user">
                <mat-chip [color]="user.is_active ? 'primary' : 'default'">
                  {{ user.is_active ? 'Active' : 'Inactive' }}
                </mat-chip>
              </td>
            </ng-container>

            <!-- Created Column -->
            <ng-container matColumnDef="created_at">
              <th mat-header-cell *matHeaderCellDef>Registered</th>
              <td mat-cell *matCellDef="let user">
                {{ user.created_at | date:'short' }}
              </td>
            </ng-container>

            <!-- Actions Column -->
            <ng-container matColumnDef="actions">
              <th mat-header-cell *matHeaderCellDef>Actions</th>
              <td mat-cell *matCellDef="let user">
                <button mat-icon-button [matTooltip]="user.is_active ? 'Deactivate' : 'Activate'" 
                        (click)="toggleUserStatus(user)">
                  <mat-icon>{{ user.is_active ? 'block' : 'check_circle' }}</mat-icon>
                </button>
                <button mat-icon-button matTooltip="Manage Roles" (click)="manageRoles(user)">
                  <mat-icon>security</mat-icon>
                </button>
              </td>
            </ng-container>

            <tr mat-header-row *matHeaderRowDef="displayedColumns"></tr>
            <tr mat-row *matRowDef="let row; columns: displayedColumns;"></tr>
          </table>
        </div>
      }
    </div>
  `,
  styles: [`
    .container {
      padding: 24px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;

      h1 {
        margin: 0 0 8px 0;
        color: #333;
      }

      p {
        margin: 0;
        color: #666;
      }
    }

    .loading,
    .empty-state {
      text-align: center;
      padding: 48px;
      color: #666;
    }

    .empty-state {
      mat-icon {
        font-size: 64px;
        width: 64px;
        height: 64px;
        color: #ccc;
        margin-bottom: 16px;
      }

      h3 {
        margin: 16px 0 8px 0;
        color: #333;
      }

      p {
        margin-bottom: 24px;
      }
    }

    .table-container {
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .users-table {
      width: 100%;

      th {
        background: #f5f5f5;
        font-weight: 600;
        color: #333;
      }

      td, th {
        padding: 12px 16px;
      }
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .status-chip,
    mat-chip {
      font-size: 11px;
      min-height: 20px;
      padding: 4px 8px;
    }

    .roles {
      display: flex;
      gap: 4px;
      flex-wrap: wrap;
    }
  `]
})
export class UserListComponent implements OnInit {
  private http = inject(HttpClient);
  private snackBar = inject(MatSnackBar);

  users: User[] = [];
  loading = true;
  displayedColumns = ['email', 'username', 'name', 'phone', 'roles', 'status', 'created_at', 'actions'];

  ngOnInit() {
    this.loadUsers();
  }

  loadUsers() {
    this.loading = true;
    console.log('Loading users from:', `${environment.apiUrl}/admin/users`);
    this.http.get<any>(`${environment.apiUrl}/admin/users`).subscribe({
      next: (response) => {
        console.log('Users loaded:', response);
        this.users = response.data || [];
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading users:', error);
        console.error('Error status:', error.status);
        console.error('Error details:', error.error);
        // For now, show a friendly message if endpoint doesn't exist
        if (error.status === 404) {
          this.snackBar.open('User management endpoint not yet implemented', 'Close', { duration: 5000 });
        } else {
          this.snackBar.open(`Failed to load users: ${error.message || 'Unknown error'}`, 'Close', { duration: 5000 });
        }
        this.loading = false;
      }
    });
  }

  toggleUserStatus(user: User) {
    const action = user.is_active ? 'deactivate' : 'activate';
    if (confirm(`Are you sure you want to ${action} ${user.email}?`)) {
      this.http.put<any>(`${environment.apiUrl}/admin/users/${user.id}`, {
        is_active: !user.is_active
      }).subscribe({
        next: () => {
          this.snackBar.open(`User ${action}d successfully`, 'Close', { duration: 3000 });
          this.loadUsers();
        },
        error: (error) => {
          console.error('Error updating user:', error);
          this.snackBar.open('Failed to update user status', 'Close', { duration: 5000 });
        }
      });
    }
  }

  manageRoles(user: User) {
    // This would open a dialog to manage user roles
    this.snackBar.open('Role management dialog coming soon', 'Close', { duration: 3000 });
  }
}

