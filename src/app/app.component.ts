import { Component, ViewEncapsulation } from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';
import { ApiService } from './api.service';
import * as constants from '../constants/constants';
import { PassvariablesService } from './services/passvariables.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent {
  text_container = 'text-container';
  title = 'AngularBlog';
  path : string;

  constructor(private router: Router, private api: ApiService, private pvs: PassvariablesService){
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

  /**
   * Pass the variables from this component to children components
   */
  passVariables(): void{
    this.pvs.textComponent$.subscribe(tc_class => {
      console.log("tc_class => "+tc_class);
    });
  }
}
