import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';
import { ApiService } from '../api.service';
import * as constants from '../../constants/constants';

@Injectable({
  providedIn: 'root'
})
export class NotAuthGuard implements CanActivate {

  usernameCookie: any;

  constructor(public api: ApiService, public router: Router){
    this.usernameCookie = localStorage.getItem("username");
    this.api.userChanged.subscribe(username => {
      //detect changes from cookie value
      this.usernameCookie = username;
    });
  }

  //redirect to home if user is authenticated
  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    if(this.usernameCookie != null){
      this.router.navigate([constants.loginRedirect]);
      return false;
    }
    return true;
  }
  
}
