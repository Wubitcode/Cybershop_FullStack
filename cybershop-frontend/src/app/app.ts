import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common'; // 🛡️ Good for signals and pipes
import { FooterComponent } from './shared/components/footer/footer.component';
import { HeaderComponent } from './shared/components/header/header.component';

@Component({
  selector: 'app-root',
  standalone: true, // 🛡️ Ensure this is here for standalone logic
  imports: [
    RouterOutlet, 
    CommonModule,
    HeaderComponent, 
    FooterComponent // 👈 Add this so <app-footer> works in app.html
     
  ],
  templateUrl: './app.html',
  styleUrl: './app.css'
})
export class App {
  protected readonly title = signal('cybershop-frontend');
}