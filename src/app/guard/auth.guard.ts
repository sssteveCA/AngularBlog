import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, CanLoad, Route, Router, RouterStateSnapshot, UrlSegment, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';
import * as constants from '../../constants/constants';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate{

  constructor(public router: Router){}

  //redirect to login page if user is not authenticated
  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    const token_key = localStorage.getItem("token_key");
    const username = localStorage.getItem("username");
    if(token_key && username && token_key != "" && username != ""){
      return true;
    }
    this.router.navigate([constants.notLoggedRedirect]);
    return false;
  }
  
}
