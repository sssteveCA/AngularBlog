import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';
import { ApiService } from '../api.service';
import * as constants from '../../constants/constants';

@Injectable({
  providedIn: 'root'
})
export class NotAuthGuard implements CanActivate {

  userCookie: any = {};

  constructor(public api: ApiService, public router: Router){
    let token_key = localStorage.getItem("token_key");
    let username = localStorage.getItem("username");
    if(token_key && username){
      this.userCookie["token_key"] = token_key;
      this.userCookie["username"] = username;
    }
    //console.log(this.userCookie);
    this.api.userChanged.subscribe(user => {
      //detect changes from cookie value
      this.userCookie = user;
    });
  }

  //redirect to home if user is authenticated
  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    if(this.userCookie != null && Object.keys(this.userCookie).length !== 0){
      this.router.navigate([constants.loginRedirect]);
      return false;
    }
    return true;
  }
  
}
