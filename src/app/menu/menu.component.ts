import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
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

@Component({
  selector: 'app-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss']
})
export class MenuComponent implements OnInit {

  userCookie : any = {};
  menuColor: string = 'bg-dark';

  constructor(private http:HttpClient, private router:Router, private api: ApiService) {
    this.userCookie["token_key"] = localStorage.getItem("token_key");
    this.userCookie["username"] = localStorage.getItem("username");
    this.observeFromService();
    this.api.getLoginStatus().then(logged => {
      //console.log("getLoginStatus logged => "+logged);
      if(!logged){
        localStorage.removeItem("token_key");
        localStorage.removeItem("username");
      }
    }).catch(err => {
      console.warn("GetLoginStatus err");
      //console.warn(err);
    });
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
        http: this.http, token_key: this.userCookie['token_key'], url: constants.logoutUrl
      }
      let lr: LogoutRequest = new LogoutRequest(lrData)
      lr.logout().then(obj => {
        if(obj[Keys.DONE] == true){
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
    /* console.log("userCookie => ");
    console.log(this.userCookie);  */
  }

  observeFromService(): void{
    this.api.loginChanged.subscribe(logged => {
      //console.log("logged");
      //console.log(logged);
    });
    this.api.userChanged.subscribe(userdata => {
      /* console.log("userdata");
      console.log(userdata); */
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

}
