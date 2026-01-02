import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [CommonModule],
  template: `
    <footer class="footer">
      <div class="container">
        <p>&copy; {{ currentYear }} KumpeApps LLC d/b/a Kumpe3D. All rights reserved.</p>
      </div>
    </footer>
  `,
  styles: [`
    .footer {
      background-color: #f5f5f5;
      padding: 24px 0;
      margin-top: auto;
      text-align: center;
      color: #666;
    }
  `]
})
export class FooterComponent {
  currentYear = new Date().getFullYear();
}
