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

  usernameCookie: any;

  constructor(public http: HttpClient, public api: ApiService) {
    this.usernameCookie = localStorage.getItem("username");
    this.api.userChanged.subscribe(username => {
      this.usernameCookie = username;
    });
   }

  ngOnInit(): void {
    this.http.get(constants.myArticlesUrl,{responseType: 'text'}).subscribe(res => {
      console.log(res);
    });//this.http.get(constants.myArticlesUrl,{responseType: 'text'}).subscribe(res => {
  }

}
