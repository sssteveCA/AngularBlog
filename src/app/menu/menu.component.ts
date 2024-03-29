import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component, Input, OnInit, OnChanges, SimpleChanges, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import * as constants from '../../constants/constants';
import * as messages from '../../messages/messages';
import { Keys } from 'src/constants/keys';
import LogoutRequestInterface from 'src/interfaces/requests/logoutrequest.interface';
import LogoutRequest from 'src/classes/requests/logoutrequest';
import { messageDialog } from 'src/functions/functions';
import { LogindataService } from '../services/logindata.service';
import { UserCookie } from 'src/constants/types';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss']
})
export class MenuComponent implements OnInit, OnDestroy {

  cookie: UserCookie = {}
  menuColor: string = 'bg-dark';
  subscription: Subscription;

  constructor(private http:HttpClient, private router:Router, private loginData: LogindataService) {
  }

  loginDataObserver(): void{
    this.subscription = this.loginData.loginDataObservable.subscribe(loginData => {
      if(loginData.userCookie && loginData.userCookie.token_key != null && loginData.userCookie.username != null){
        this.cookie.token_key = loginData.userCookie.token_key;
        this.cookie.username = loginData.userCookie.username;
      }
      else{
        this.cookie = {}
      }
    })
  }

  //user wants  logout from his account
  logout(): void{
    const data: ConfirmDialogInterface = {
      title: 'Logout',
      message: messages.logoutConfirm
    };
    let cd = new ConfirmDialog(data);
    cd.bt_yes.addEventListener('click', ()=>{
      cd.instance.dispose();
      cd.div_dialog.remove();
      let lrData: LogoutRequestInterface = {
        http: this.http, token_key: localStorage.getItem('token_key') as string, url: constants.logoutUrl
      }
      let lr: LogoutRequest = new LogoutRequest(lrData)
      lr.logout().then(obj => {
        if(obj[Keys.DONE] == true){}//if(obj[Keys.DONE] == true){
        this.loginData.removeItems();
        this.loginData.changeLoginData({
          logout: true,
          userCookie: {}
        })
        //this.router.navigateByUrl(constants.logoutRedirect);
        /* else{
          const mdData: MessageDialogInterface = {
            title: 'Logout',
            message: obj[Keys.MESSAGE]
          };
          messageDialog(mdData)
        } */
      })
      
    });//cd.bt_yes.addEventListener('click', ()=>{
    cd.bt_no.addEventListener('click', ()=>{
      cd.instance.dispose();
      cd.div_dialog.remove();
    });
  }

  ngOnInit(): void {
    this.loginDataObserver()
    this.loginData.changeLoginData({
      userCookie: {
        token_key: localStorage.getItem('token_key'), username: localStorage.getItem('username')
      }
    })
  }

  ngOnDestroy(): void {
    if(this.subscription) this.subscription.unsubscribe();
  }

}
