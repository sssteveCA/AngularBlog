import { HttpClient } from '@angular/common/http';
import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import GetUserInfo from 'src/classes/requests/profile/getuserinfo';
import { Keys } from 'src/constants/keys';
import GetUserInfoInterface from 'src/interfaces/requests/profile/getuserinfo.interface';
import * as constants from '../../../../constants/constants';

@Component({
  selector: 'app-info',
  templateUrl: './info.component.html',
  styleUrls: ['./info.component.scss']
})
export class InfoComponent implements OnInit {

  backlink: string = "../";
  emailObject: object = {};
  namesObject: object = {};
  usernameObject: object = {};
  urlUserInfo: string = constants.profileGetUserInfoUrl;
  title: string = "Modifica il tuo account";

  constructor(public http: HttpClient, public router: Router, public fb: FormBuilder) {
    
   }

  ngOnInit(): void {
    this.getUserInfo();
  }

  private getUserInfo(): void{
    let gui_data: GetUserInfoInterface = {
      http: this.http,
      token_key: localStorage.getItem("token_key") as string,
      url: this.urlUserInfo
    }
    let gui: GetUserInfo = new GetUserInfo(gui_data);
    gui.getUserInfo().then(obj => {
      this.emailObject = { 'done': obj[Keys.DONE] }
      this.namesObject = { 'done': obj[Keys.DONE] }
      this.usernameObject = { 'done': obj[Keys.DONE] }
      if(obj[Keys.DONE] == true){
        this.emailObject['email'] = obj[Keys.DATA]['email'];
        this.namesObject['name'] = obj[Keys.DATA]['name'];
        this.namesObject['surname'] = obj[Keys.DATA]['surname'];
        this.usernameObject['username'] = obj[Keys.DATA]['username'];
      }
    }).catch(err => {
      this.emailObject = { 'done': false }
      this.namesObject = { 'done': false }
      this.usernameObject = { 'done': false }
    })
  }
  
}
