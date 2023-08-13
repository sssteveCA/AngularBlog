import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';
import { ApiService } from '../api.service';
import * as constants from '../../constants/constants';

@Injectable({
  providedIn: 'root'
})
export class NotAuthGuard implements CanActivate {

  constructor( public router: Router){}

  /**
   * redirect to home if user is authenticated
   * @param route 
   * @param state 
   * @returns 
   */
  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    const token_key = localStorage.getItem("token_key");
    const username = localStorage.getItem("username");
    if(token_key && username && token_key != "" && username != ""){
      this.router.navigate([constants.loginRedirect]);
      return false;
    }
    return true;
  }
  
}
