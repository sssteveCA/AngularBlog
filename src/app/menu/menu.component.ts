import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import * as constants from '../../constants/constants';
import * as functions from '../../functions/functions';
import { ApiService } from '../api.service';

@Component({
  selector: 'app-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss']
})
export class MenuComponent implements OnInit {

  userCookie : any = {};

  constructor(private http:HttpClient, private router:Router, private api: ApiService) {
    this.userCookie["token_key"] = localStorage.getItem("token_key");
    this.userCookie["username"] = localStorage.getItem("username");
    this.observeFromService();
    let logged = this.api.getLoginStatus();
    if(!logged){
      localStorage.removeItem("token_key");
      localStorage.removeItem("username");
    }
  }

  //user wants  logout from his account
  logout(): void{
    let token_key = localStorage.getItem("token_key");
    console.log(token_key);
    this.http.get(constants.logoutUrl+'?token_key='+token_key,{responseType: 'text'}).subscribe(res => {
      console.log(res);
      let rJson = JSON.parse(res);
      if(rJson['done'] == true){
        localStorage.removeItem("token_key");
        localStorage.removeItem("username");
        this.api.changeUserdata({});
        this.router.navigate([constants.logoutRedirect]);
      }//if(rJson['done'] == true){
      else
        functions.dialogMessage($,'Logout',rJson['msg']);
      
    },error => {
      console.log(error);
    });
  }

  ngOnInit(): void {
    console.log("userCookie => ");
    console.log(this.userCookie); 
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
