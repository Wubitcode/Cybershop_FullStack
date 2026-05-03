import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-services',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './services.component.html',
  styleUrl: './services.component.css'
})
export class ServicesComponent {
  services = [
    {
      title: 'Security Training',
      description: 'Comprehensive cybersecurity education tailored for students, small businesses, and enterprise organizations.',
      icon: '🎓',
      features: ['Student certification prep', 'Corporate security awareness', 'Phishing simulations']
    },
    {
      title: 'Security Auditing',
      description: 'In-depth assessment of your digital infrastructure to identify vulnerabilities before they are exploited.',
      icon: '🛡️',
      features: ['Compliance checks', 'Risk assessment', 'Network vulnerability scans']
    },
    {
      title: 'Secure Web Development',
      description: 'Custom full-stack web applications built with a security-first mindset and robust data protection.',
      icon: '💻',
      features: ['Angular & PHP integration', 'Encrypted database design', 'RBAC implementation']
    },
    {
     title: 'Security Infrastructure Advisory',
     description: 'We help clients select and implement trusted cybersecurity tools through verified vendors and industry-standard solutions. Some recommendations may include affiliate partnerships.',
   icon: '🔗',
    features: [
    'Guidance on hardware firewall selection from trusted providers',
    'Antivirus and endpoint protection recommendations',
    'Secure VPN setup and privacy-focused solutions'
  ]
}
  ];
}