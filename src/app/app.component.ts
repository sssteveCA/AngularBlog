import { Component, OnDestroy, ViewEncapsulation } from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';
import { ApiService } from './api.service';
import * as constants from '../constants/constants';
import { LogindataService } from './services/logindata.service';
import { UserCookie } from 'src/constants/types';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent implements OnDestroy {
  title = 'AngularBlog';
  path : string;
  cookie: UserCookie = {};
  username: string|null;
  loginDataSubscription: Subscription;

  constructor(private router: Router, private api: ApiService, private loginData: LogindataService ){
    this.loginDataObserver();
    this.router.events.subscribe((event) => {
      if(event instanceof NavigationEnd){
        this.path = event.url.split('?')[0];
      }
    });
  }

  ngOnDestroy(): void {
    this.loginDataSubscription.unsubscribe();
  }

  loginDataObserver(): void{
    this.loginDataSubscription = this.loginData.userCookieObservable.subscribe(userCookie => {
      if(userCookie && userCookie.token_key && userCookie.username && userCookie.token_key != "" && userCookie.username != ""){
        this.username = userCookie.username;
      }
      else{
        this.username = null;
        this.loginData.removeItems();
        this.loginData.changeUserCookieData({});
      }
    })
  }

  //show the background-image
  backgroundStyle() : Object{
    return {
      'background-image' : "url('"+constants.imgUrl+"')",
      'background-repeat' : 'repeat',
      'z-index': '-1'
    }
  }
}
