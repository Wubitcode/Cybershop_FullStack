import { Component, OnInit } from '@angular/core';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-order-success',
  standalone: true,
  imports: [RouterModule],
  templateUrl: './order-success.component.html',
  styleUrls: ['./order-success.component.css']
})
export class OrderSuccessComponent implements OnInit {
  transactionId: string = '';

  ngOnInit(): void {
    // Generate a mock secure transaction ID
    this.transactionId = Math.random().toString(36).substr(2, 9).toUpperCase();
  }

  printReceipt() {
    window.print();
  }
}