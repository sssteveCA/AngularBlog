import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, CanLoad, Route, Router, RouterStateSnapshot, UrlSegment, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';
import { ApiService } from '../api.service';
import * as constants from '../../constants/constants';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate{

  userCookie: any = {};

  constructor(public api: ApiService, public router: Router){
    let token_key = localStorage.getItem("token_key");
    let username = localStorage.getItem("username");
    if(token_key && username){
      this.userCookie["token_key"] = token_key;
      this.userCookie["username"] = username;
    }
    this.api.userChanged.subscribe(user => {
      //detect changes from cookie value
      this.userCookie = user;
    });
  }

  //redirect to login page if user is not authenticated
  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    if(this.userCookie == null || Object.keys(this.userCookie).length === 0){
      this.router.navigate([constants.notLoggedRedirect]);
      return false;
    }
    return true;
  }
  
}
