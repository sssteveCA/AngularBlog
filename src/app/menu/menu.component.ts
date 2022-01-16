import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import * as constants from '../../constants/constants';
import { ApiService } from '../api.service';

@Component({
  selector: 'app-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss']
})
export class MenuComponent implements OnInit {

  usernameCookie : any;

  constructor(private http:HttpClient, private router:Router, private api: ApiService) {
    this.usernameCookie = localStorage.getItem("username");
    this.api.userChanged.subscribe(username => {
      this.usernameCookie = username;
    });
  }

  //user wants  logout from his account
  logout(): void{
    this.http.get(constants.logoutUrl).subscribe(res => {
      localStorage.removeItem("username");
      this.api.changeUsername(null);
      this.router.navigate([constants.logoutRedirect]);
    },error => {
      console.log(error);
    });
  }

  ngOnInit(): void {
  }

}
