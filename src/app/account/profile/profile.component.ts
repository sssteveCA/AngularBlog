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

  userCookie: any = {};

  constructor(public http:HttpClient, public api: ApiService, public router: Router) {
    this.observeFromService();
    if(this.api.getLoginStatus()){
      this.userCookie['token_key'] = localStorage.getItem("token_key");
      this.userCookie['username'] = localStorage.getItem("username");
      this.api.changeUserdata(this.userCookie);
    }
    else{
      this.api.removeItems();
      this.api.changeUserdata({});
    }
    this.http.get(constants.profileUrl,{responseType: 'text'}).subscribe(res => {
      console.log(res);
      let rJson = JSON.parse(res);
    });
   }

  ngOnInit(): void {
  }

  observeFromService(): void{
    this.api.loginChanged.subscribe(logged => {
      console.log("logged");
      console.log(logged);
    });
    this.api.userChanged.subscribe(userdata => {
      console.log("userdata");
      console.log(userdata);
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

}
