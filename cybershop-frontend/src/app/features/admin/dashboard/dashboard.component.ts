import { Component, OnInit, OnDestroy, inject, ViewChild, ElementRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { interval, Subscription } from 'rxjs';
import { Chart, registerables } from 'chart.js';

// Register Chart.js components
Chart.register(...registerables);

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit, OnDestroy {
  // 🚀 ViewChild links the TS to the <canvas #salesChart> in HTML
  @ViewChild('salesChart') salesChart!: ElementRef;
  
  private router = inject(Router);
  // private adminService = inject(AdminService); // Inject your service here

  adminName: string = 'Admin';
  transactions: any[] = [];
  chart: any;
  private pollSubscription?: Subscription;

  ngOnInit(): void {
    // 1. Load Admin Name
    const userData = localStorage.getItem('user');
    if (userData) {
      const user = JSON.parse(userData);
      this.adminName = user.name || 'Admin';
    }

    // 2. Initial Data Load
    this.loadOrders(); 
    
    // 3. 🛰️ Polling for Orders (Every 10 Seconds)
    this.pollSubscription = interval(10000).subscribe(() => {
      this.loadOrders();
    });
  }

  // This runs after the HTML is rendered, perfect for Chart.js
  ngAfterViewInit(): void {
    this.initChart();
  }

  loadOrders(): void {
    // Logic for fetching transactions from PHP goes here
    console.log("Checking Durham Node for new transactions...");
  }

  initChart(): void {
    const ctx = this.salesChart.nativeElement.getContext('2d');
    this.chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Sales Revenue ($)',
          data: [120, 190, 300, 500, 200, 300, 450], // We will replace with real data soon
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: '#1f2937' } },
          x: { grid: { display: false } }
        }
      }
    });
  }

  logout(): void {
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    this.router.navigate(['/']); 
  }

  ngOnDestroy(): void {
    if (this.pollSubscription) {
      this.pollSubscription.unsubscribe();
    }
    if (this.chart) {
      this.chart.destroy(); // Clean up chart memory
    }
  }
}