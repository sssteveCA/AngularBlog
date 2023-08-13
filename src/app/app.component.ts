import { Component, ViewEncapsulation } from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';
import { ApiService } from './api.service';
import * as constants from '../constants/constants';
import { LogindataService } from './services/logindata.service';
import { UserCookie } from 'src/constants/types';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent {
  title = 'AngularBlog';
  path : string;
  cookie: UserCookie = {};
  username: string|null;

  constructor(private router: Router, private api: ApiService, private loginData: LogindataService ){
    this.loginDataObserver();
    this.router.events.subscribe((event) => {
      if(event instanceof NavigationEnd){
        this.path = event.url.split('?')[0];
      }
    });
  }

  loginDataObserver(): void{
    this.loginData.userCookieObservable.subscribe(userCookie => {
      if(userCookie && userCookie.token_key && userCookie.username && userCookie.token_key != "" && userCookie.username != ""){
        this.username = userCookie.username;
      }
      else{
        this.username = null;
        this.loginData.removeItems();
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
