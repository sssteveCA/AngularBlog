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

  @ViewChild('currentPwd', {static: false}) iCurrPwd: ElementRef<HTMLInputElement>;
  @ViewChild('newPwd', {static: false}) iNewPwd: ElementRef<HTMLInputElement>;
  @ViewChild('confNewPwd', {static: false}) iConfNewPwd: ElementRef<HTMLInputElement>;
  @ViewChild('showPwd', {static: false}) cbShowPwd: MatCheckbox;

  userCookie: any = {};
  groupEu: FormGroup; //Edit username form group
  groupEp: FormGroup; //Edit password form group
  getUsernameUrl: string = constants.profileGetUsernameUrl;
  updateUsernameUrl: string = constants.profileUpdateUsernameUrl;
  updatePasswordUrl: string = constants.profileUpdatePasswordUrl;
  deleteProfileUrl: string = constants.profileDeleteUrl;
  showUsernameSpinner: boolean = false;
  showPasswordSpinner: boolean = false;
  showDeleteProfileSpinner: boolean = false;
  usernameError: boolean = false;


  constructor(public http: HttpClient, public api: ApiService, public router: Router, public fb: FormBuilder) {
    this.observeFromService();
    this.setFormsGroup();
    this.getUsername();
   }

  ngOnInit(): void {
  }

  private deleteAccountRequest(da_params: DaParams): void{
    let da_data: DeleteProfileInterface = {
      conf_password: da_params.conf_password,
      http: this.http,
      password: da_params.password,
      token_key: this.userCookie['token_key'],
      url: this.deleteProfileUrl
    };
    let da: DeleteProfile = new DeleteProfile(da_data);
    da.deleteProfile().then(obj => {

    }).catch(err => {

    });
  }

  /**
   * When user submit delete account form
   */
  deleteAccountSubmit(): void{
    let cdi: ConfirmDialogInterface = {
      title: 'Elimina account',
      message: Messages.DELETEACCOUNT_CONFIRM
    };
    let cd: ConfirmDialog = new ConfirmDialog(cdi);
    cd.bt_yes.addEventListener('click',()=>{
      cd.instance.dispose();
      cd.div_dialog.remove();
      let pcdi: PasswordConfirmDialogInterface = {
        title: 'Elimina account'
      };
      let pcd: PasswordConfirmDialog = new PasswordConfirmDialog(pcdi);
      pcd.bt_ok.addEventListener('click',()=>{
        let da_params: DaParams = {
          conf_password: pcd.i_confpass.value,
          password: pcd.i_pass.value
        };
        pcd.instance.dispose();
        pcd.div_dialog.remove();
        document.body.style.overflow = 'auto';
        this.deleteAccountRequest(da_params);
      });
      pcd.bt_canc.addEventListener('click',()=>{
        pcd.instance.dispose();
        pcd.div_dialog.remove();
        document.body.style.overflow = 'auto';
      })
    });
    cd.bt_no.addEventListener('click', ()=>{
      cd.instance.dispose();
      cd.div_dialog.remove();
      document.body.style.overflow = 'auto';
    });
  }

  private editPasswordRequest(ep_params: EpParams): void{
    let ep_data: UpdatePasswordInterface = {
      conf_new_password: ep_params.conf_new_password,
      http: this.http,
      new_password: ep_params.new_password,
      old_password: ep_params.old_password,
      token_key: this.userCookie['token_key'],
      url: this.updatePasswordUrl
    };
    let ep: UpdatePassword = new UpdatePassword(ep_data);
    ep.updatePassword().then(obj => {

    }).catch(err => {

    });
  }

  /**
   * When user submit edit password form
   */
  editPasswordSubmit(): void{
    if(this.groupEp.valid){
      let cdi: ConfirmDialogInterface = {
        title: 'Modifica password',
        message: Messages.EDITPASSWORD_CONFIRM
      };
      let cd: ConfirmDialog = new ConfirmDialog(cdi);
      cd.bt_yes.addEventListener('click',()=>{
        cd.instance.dispose();
        cd.div_dialog.remove();
        document.body.style.overflow = 'auto';
        let ep_params: EpParams = {
          conf_new_password: this.groupEp.controls['confNewPwd'].value,
          new_password: this.groupEp.controls['newPwd'].value,
          old_password: this.groupEp.controls['currentPwd'].value
        };
        this.editPasswordRequest(ep_params);
      });
      cd.bt_no.addEventListener('click',()=>{
        cd.instance.dispose();
        cd.div_dialog.remove();
        document.body.style.overflow = 'auto';
      });
    }//if(this.groupEp.valid){
    else{
      let mdi: MessageDialogInterface = {
        title: 'Modifica password',
        message: 'Uno o più dati tra quelli richiesti hanno un formato non valido'
      };
      this.messageDialog(mdi);
    }
  }

  private editUsernameRequest(new_username: string): void{
    let uu_data: UpdateUsernameInterface = {
      http: this.http,
      token_key: this.userCookie['token_key'],
      new_username: new_username,
      url: this.updateUsernameUrl
    };
    let uu: UpdateUsername = new UpdateUsername(uu_data);
    uu.updateUsername().then(obj => {

    }).catch(err => {

    });
  }

  /**
   * When user submit edit username form
   */
  editUsernameSubmit(): void{
    if(this.groupEu.valid){
      let cdi: ConfirmDialogInterface = {
        title: 'Modifica nome utente',
        message: Messages.EDITUSERNAME_CONFIRM
      };
      let cd: ConfirmDialog = new ConfirmDialog(cdi);
      cd.bt_yes.addEventListener('click',()=>{
        cd.instance.dispose();
        cd.div_dialog.remove();
        document.body.style.overflow = 'auto';
        let new_username: string = this.groupEu.controls['username'].value;
        this.editUsernameRequest(new_username);
      });
      cd.bt_no.addEventListener('click',()=>{
        cd.instance.dispose();
        cd.div_dialog.remove();
        document.body.style.overflow = 'auto';
      });
    }//if(this.groupEu.valid){
    else{
      let mdi: MessageDialogInterface = {
        title: 'Modifica nome utente',
        message: 'Il nome utente inserito ha un formato non valido'
      };
      this.messageDialog(mdi);
    }
  }

  private getUsername(): void{
    let gu_data: GetUsernameInterface = {
      http: this.http,
      token_key: localStorage.getItem("token_key") as string,
      url: this.getUsernameUrl
    };
    let gu: getUsername = new getUsername(gu_data);
    gu.getUsername().then(obj => {
      if(obj['done'] == true){
        this.groupEu.controls['username'].setValue(obj['username']);
      }//if(obj['done'] == true){
      else if(obj['done'] == false && obj['expired'] == true){}
      else{
        this.usernameError = true;
      }
    }).catch(err => {
      this.usernameError = true;
    });
  }

  messageDialog(mdi: MessageDialogInterface): void{
    let md: MessageDialog = new MessageDialog(mdi);
    md.bt_ok.addEventListener('click', ()=>{
      md.instance.dispose();
      md.div_dialog.remove();
      document.body.style.overflow = 'auto';
    });
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

  setFormsGroup(): void{
    this.groupEu = this.fb.group({
      'username': ['', Validators.compose([Validators.required, Validators.minLength(3)])]
    });
    this.groupEp = this.fb.group({
      'currentPwd': ['', Validators.compose([Validators.required, Validators.minLength(6)])],
      'newPwd': ['', Validators.compose([Validators.required, Validators.minLength(6)])],
      'confNewPwd': ['', Validators.compose([Validators.required, Validators.minLength(6)])],
    });
  }

  /**
   * When the value of 'show password' checkbox change
   */
  showPwdCbChange(): void{
    if(this.cbShowPwd.checked){
      this.iCurrPwd.nativeElement.setAttribute('type','text');
      this.iNewPwd.nativeElement.setAttribute('type','text');
      this.iConfNewPwd.nativeElement.setAttribute('type','text');
    }
    else{
      this.iCurrPwd.nativeElement.setAttribute('type','password');
      this.iNewPwd.nativeElement.setAttribute('type','password');
      this.iConfNewPwd.nativeElement.setAttribute('type','password');
    }
  }
}
