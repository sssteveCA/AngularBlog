import { Component, ViewEncapsulation } from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent {
  title = 'AngularBlog';
  path : string;

  constructor(private router: Router){
    //console.log(this.router);
    this.router.events.subscribe((event) => {
      //console.log(event);
      if(event instanceof NavigationEnd){
        console.log(event.url);
        this.path = event.url;
      }
    });
  }
}
