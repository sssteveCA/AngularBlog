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
export class AppComponent {
  title = 'AngularBlog';
  path : string;

  constructor(private router: Router, private loginData: LogindataService ){
    this.router.events.subscribe((event) => {
      if(event instanceof NavigationEnd){
        this.path = event.url.split('?')[0];
      }
    });
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
