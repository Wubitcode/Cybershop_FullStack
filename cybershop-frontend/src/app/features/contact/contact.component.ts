import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './contact.component.html',
  styleUrl: './contact.component.css'
})
export class ContactComponent {
  contactData = {
    name: '',
    email: '',
    subject: 'General Inquiry',
    message: ''
  };

  submitted = false;

  onSubmit() {
    console.log('Secure Message Sent:', this.contactData);
    this.submitted = true;
    
    // Reset form after 3 seconds
    setTimeout(() => {
      this.submitted = false;
      this.contactData = { name: '', email: '', subject: 'General Inquiry', message: '' };
    }, 3000);
  }
}