import { HttpClient } from '@angular/common/http';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { Router } from '@angular/router';
import GetUserInfo from 'src/classes/requests/profile/getuserinfo';
import { Keys } from 'src/constants/keys';
import GetUserInfoInterface from 'src/interfaces/requests/profile/getuserinfo.interface';
import * as constants from '../../../../constants/constants';
import {Subscription } from 'rxjs';
import { LogindataService } from 'src/app/services/logindata.service';
import { UserCookie } from 'src/constants/types';

@Component({
  selector: 'app-info',
  templateUrl: './info.component.html',
  styleUrls: ['./info.component.scss']
})
export class InfoComponent implements OnInit, OnDestroy {

  backlink: string = "../";
  cookie: UserCookie = {};
  emailObject: object = {};
  namesObject: object = {};
  usernameObject: object = {};
  urlUserInfo: string = constants.profileGetUserInfoUrl;
  title: string = "Modifica il tuo account";
  subscription: Subscription;

  constructor(public http: HttpClient, public router: Router, public fb: FormBuilder, private loginData: LogindataService) {
    
   }

   ngOnDestroy(): void {
    if(this.subscription != null) this.subscription.unsubscribe();
  }

  ngOnInit(): void {
    this.loginDataObserver()
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

  loginDataObserver(): void{
    this.subscription = this.loginData.loginDataObservable.subscribe(loginData => {
      if(!(loginData.userCookie && loginData.userCookie.token_key != null && loginData.userCookie.username != null)){
        if(loginData.logout && loginData.logout == true)
          this.router.navigateByUrl(constants.homeUrl)
        else
          this.router.navigateByUrl(constants.notLoggedRedirect)
      }
    })
  }

  /**
   * Emitted from child components when the session is expired
   * @param event 
   */
  onSessionExpired(expired: boolean): void{
    if(expired){
      this.loginData.changeLoginData({
        logout: false, userCookie: {}
      })
    }
  }
  
}
