import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-users',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './users.component.html',
  styleUrls: ['./users.component.css']
})
// 🛡️ Ensure this says 'UsersComponent' and has 'export'
export class UsersComponent {
  // User management logic
}
