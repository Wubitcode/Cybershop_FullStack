import { TestBed } from '@angular/core/testing';
import { Router } from '@angular/router';

// 1. Change 'adminGuard' to 'AdminGuard'
import { AdminGuard } from './admin.guard'; 

describe('AdminGuard', () => {
  let guard: AdminGuard;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [
        AdminGuard, // Provide the class
        { provide: Router, useValue: { navigate: () => {} } }
      ]
    });
    
    // 2. Inject the class instance
    guard = TestBed.inject(AdminGuard);
  });

  it('should be created', () => {
    expect(guard).toBeTruthy();
  });
});