import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/api.service';
import * as constants from 'src/constants/constants';

@Component({
  selector: 'app-my-articles',
  templateUrl: './my-articles.component.html',
  styleUrls: ['./my-articles.component.scss']
})
export class MyArticlesComponent implements OnInit {

  userCookie: any = {};

  constructor(public http: HttpClient, public api: ApiService) {
    this.observeFromService();
    this.api.getLoginStatus().then(res => {
      if(res == true){
        this.userCookie['token_key'] = localStorage.getItem("token_key");
        this.userCookie['username'] = localStorage.getItem("username");
      }
      else{
        this.api.removeItems();
        this.userCookie = {};
      }
      this.api.changeUserdata(this.userCookie);
      
  }).catch(err => {
    this.api.removeItems();
    this.userCookie = {};
    this.api.changeUserdata(this.userCookie);
  });
   }

  ngOnInit(): void {
    this.http.get(constants.myArticlesUrl,{responseType: 'text'}).subscribe(res => {
      console.log(res);
    });//this.http.get(constants.myArticlesUrl,{responseType: 'text'}).subscribe(res => {
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
