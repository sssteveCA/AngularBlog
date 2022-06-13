import { Component, ViewEncapsulation } from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';
import { ApiService } from './api.service';
import * as constants from '../constants/constants';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent {
  title = 'AngularBlog';
  path : string;

  constructor(private router: Router, private api: ApiService){
    //console.log(this.router);
    this.router.events.subscribe((event) => {
      //console.log(event);
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
      'z-index': '-1',
      height: '-webkit-fill-available'
    }
  }
}
