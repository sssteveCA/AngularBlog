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

  userCookie : any = {};

  constructor(private http:HttpClient, private router:Router, private api: ApiService) {
    this.userCookie["id"] = localStorage.getItem("id");
    this.userCookie["username"] = localStorage.getItem("username");
    this.api.userChanged.subscribe(userdata => {
      console.log("userdata");
      console.log(userdata);
      this.userCookie['id'] = userdata['id'];
      this.userCookie['username'] = userdata['username'];
      /* console.log("userCookie => ");
      console.log(this.userCookie); */
    });
  }

  //user wants  logout from his account
  logout(): void{
    this.http.get(constants.logoutUrl).subscribe(res => {
      localStorage.removeItem("id");
      localStorage.removeItem("username");
      this.api.changeUserdata({});
      this.router.navigate([constants.logoutRedirect]);
    },error => {
      console.log(error);
    });
  }

  ngOnInit(): void {
  }

}
