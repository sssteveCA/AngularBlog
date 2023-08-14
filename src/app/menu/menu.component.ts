import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component, Input, OnInit, OnChanges, SimpleChanges, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import * as constants from '../../constants/constants';
import * as messages from '../../messages/messages';
import { ApiService } from '../api.service';
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
  userCookie : any = {};
  menuColor: string = 'bg-dark';
  loginDataSubscription: Subscription;

  constructor(private http:HttpClient, private router:Router, private api: ApiService, private loginData: LogindataService) {
    console.log(localStorage)
  }

  loginDataObserver(): void{
    this.loginDataSubscription = this.loginData.userCookieObservable.subscribe(userCookie => {
      if(userCookie && userCookie.token_key && userCookie.username && userCookie.token_key != "" && userCookie.username != ""){
        this.cookie.username = userCookie.username;
        this.cookie.token_key = userCookie.token_key;
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
        if(obj[Keys.DONE] == true){
          this.loginData.removeItems();
          this.loginData.changeUserCookieData({});
          this.api.removeItems();
          this.api.changeUserdata({});
          this.router.navigate([constants.logoutRedirect]);
        }//if(obj[Keys.DONE] == true){
        else{
          const mdData: MessageDialogInterface = {
            title: 'Logout',
            message: obj[Keys.MESSAGE]
          };
          messageDialog(mdData)
        }
      })
      
    });//cd.bt_yes.addEventListener('click', ()=>{
    cd.bt_no.addEventListener('click', ()=>{
      cd.instance.dispose();
      cd.div_dialog.remove();
    });
  }

  ngOnInit(): void {
    this.loginDataObserver()
  }

  ngOnDestroy(): void {
    if(this.loginDataSubscription) this.loginDataSubscription.unsubscribe();
  }

}
