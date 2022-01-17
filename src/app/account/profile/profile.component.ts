import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import * as constants from '../../../constants/constants';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.scss']
})
export class ProfileComponent implements OnInit {

  usernameCookie: any;

  constructor(public http:HttpClient, public api: ApiService, public router: Router) {
    this.usernameCookie = localStorage.getItem("username");
    this.api.userChanged.subscribe(username => {
      this.usernameCookie = username;
    });
    this.http.get(constants.profileUrl,{responseType: 'text'}).subscribe(res => {
      console.log(res);
      let rJson = JSON.parse(res);
      /*if(rJson['session'] == false){
        //if user is not authenticated
        localStorage.removeItem("username");
        this.api.changeUsername(null);
        this.router.navigate([constants.notLoggedRedirect]);
      }*/
    });
   }

  ngOnInit(): void {
  }

}
