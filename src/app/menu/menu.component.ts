import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import ConfirmDialog from 'src/classes/confirmdialog';
import ConfirmDialogInterface from 'src/classes/confirmdialog.interface';
import MessageDialog from 'src/classes/messagedialog';
import MessageDialogInterface from 'src/classes/messagedialog.interface';
import * as constants from '../../constants/constants';
import * as messages from '../../messages/messages';
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
    this.api.getLoginStatus().then(logged => {
      console.log("getLoginStatus logged => "+logged);
      if(!logged){
        localStorage.removeItem("token_key");
        localStorage.removeItem("username");
      }
    }).catch(err => {
      console.warn("GetLoginStatus err");
      console.warn(err);
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
        else{
          const data: MessageDialogInterface = {
            title: 'Logout',
            message: rJson['msg']
          };
          let md = new MessageDialog(data);
          md.bt_ok.addEventListener('click',()=>{
            md.instance.dispose();
            md.div_dialog.remove();
            document.body.style.overflow = 'auto';
          });
        }
        
      },error => {
        console.log(error);
      });
    });//cd.bt_yes.addEventListener('click', ()=>{
    cd.bt_no.addEventListener('click', ()=>{
      cd.instance.dispose();
      cd.div_dialog.remove();
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
