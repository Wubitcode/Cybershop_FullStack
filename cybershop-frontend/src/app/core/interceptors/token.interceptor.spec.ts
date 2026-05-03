import { TestBed } from '@angular/core/testing';
import { HttpInterceptorFn, HttpRequest, HttpHandlerFn, HttpResponse } from '@angular/common/http';
import { tokenInterceptor } from './token.interceptor';
import { of } from 'rxjs';



describe('tokenInterceptor', () => {

  const interceptor: HttpInterceptorFn = (req, next) =>
    TestBed.runInInjectionContext(() => tokenInterceptor(req, next));

  beforeEach(() => {
    TestBed.configureTestingModule({});
    localStorage.clear();
  });

  it('should add Authorization header when token exists', () => {

    localStorage.setItem('token', 'fake-token-123');

    const req = new HttpRequest('GET', '/test');

    const next: HttpHandlerFn = (request) => {

      expect(request.headers.get('Authorization'))
        .toBe('Bearer fake-token-123');

      return of(new HttpResponse({ body: null }));
    };

    interceptor(req, next).subscribe();
  });

  it('should NOT add Authorization header when no token', () => {

    localStorage.removeItem('token');

    const req = new HttpRequest('GET', '/test');

    const next: HttpHandlerFn = (request) => {

      expect(request.headers.has('Authorization')).toBeFalsy();

      return of(new HttpResponse({ body: null }));
    };

    interceptor(req, next).subscribe();
  });

});