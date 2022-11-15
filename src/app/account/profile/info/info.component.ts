import { HttpClient } from '@angular/common/http';
import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatCheckbox } from '@angular/material/checkbox';
import { MatFormField, MatFormFieldControl } from '@angular/material/form-field';
import { MatInput, MatInputModule } from '@angular/material/input';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import PasswordConfirmDialog from 'src/classes/dialogs/passwordconfirmdialog';
import DeleteProfile from 'src/classes/requests/profile/deleteprofile';
import { getUsername } from 'src/classes/requests/profile/getusername';
import UpdatePassword from 'src/classes/requests/profile/updatepassword';
import UpdateUsername from 'src/classes/requests/profile/updateusername';
import { Messages } from 'src/constants/messages';
import { DaParams, EpParams } from 'src/constants/types';
import { messageDialog } from 'src/functions/functions';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import PasswordConfirmDialogInterface from 'src/interfaces/dialogs/passwordconfirmdialog.interface';
import DeleteProfileInterface from 'src/interfaces/requests/profile/deleteprofile.interface';
import GetUsernameInterface from 'src/interfaces/requests/profile/getusername.interface';
import UpdatePasswordInterface from 'src/interfaces/requests/profile/updatepassword.interface';
import UpdateUsernameInterface from 'src/interfaces/requests/profile/updateusername.interface';
import * as constants from '../../../../constants/constants';

@Component({
  selector: 'app-info',
  templateUrl: './info.component.html',
  styleUrls: ['./info.component.scss']
})
export class InfoComponent implements OnInit {

  userCookie: any = {};

  constructor(public http: HttpClient, public api: ApiService, public router: Router, public fb: FormBuilder) {
    this.observeFromService();
   }

  ngOnInit(): void {
  }

  observeFromService(): void{
    this.api.getLoginStatus().then(res => {
      if(res == true){
        this.userCookie['token_key'] = localStorage.getItem("token_key");
        this.userCookie['username'] = localStorage.getItem("username");
        this.api.changeUserdata(this.userCookie);
      }//if(res == true){
      else{
        this.api.removeItems();
        this.userCookie = {};
        this.api.changeUserdata(this.userCookie);
        this.router.navigateByUrl(constants.notLoggedRedirect);
      }
    }).catch(err => {
      this.api.removeItems();
        this.userCookie = {};
        this.api.changeUserdata(this.userCookie);
        this.router.navigateByUrl(constants.notLoggedRedirect);
    });
    this.api.loginChanged.subscribe(logged => {

    });
    this.api.userChanged.subscribe(userdata => {
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

  
}
