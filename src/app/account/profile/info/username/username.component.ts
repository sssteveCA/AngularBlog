import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { MatFormField, MatFormFieldControl } from '@angular/material/form-field';
import { MatInput, MatInputModule } from '@angular/material/input';
import { getUsername } from 'src/classes/requests/profile/getusername';
import GetUsernameInterface from 'src/interfaces/requests/profile/getusername.interface';
import * as constants from '../../../../../constants/constants';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import { Messages } from 'src/constants/messages';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import { messageDialog } from 'src/functions/functions';
import UpdateUsernameInterface from 'src/interfaces/requests/profile/updateusername.interface';
import UpdateUsername from 'src/classes/requests/profile/updateusername';
import { ApiService } from 'src/app/api.service';
import PasswordDialogInterface from 'src/interfaces/dialogs/passworddialog.interface';
import PasswordDialog from 'src/classes/dialogs/passworddialog';
import { EuParams } from 'src/constants/types';


@Component({
  selector: 'app-username',
  templateUrl: './username.component.html',
  styleUrls: ['./username.component.scss']
})
export class UsernameComponent implements OnInit {

  userCookie: any = {};
  groupEu: FormGroup; //Edit username form group
  getUsernameUrl: string = constants.profileGetUsernameUrl;
  updateUsernameUrl: string = constants.profileUpdateUsernameUrl;
  showUsernameSpinner: boolean = false;
  usernameError: boolean = false;

  constructor(public http: HttpClient, public fb: FormBuilder, public api: ApiService, public router: Router) {
    this.observeFromService();
    this.setFormGroupUsername();
    this.getUsername();
   }

  ngOnInit(): void {
  }

  private editUsernameRequest(eu_params: EuParams): void{
    let uu_data: UpdateUsernameInterface = {
      http: this.http,
      token_key: this.userCookie['token_key'],
      new_username: eu_params.username,
      password: eu_params.password,
      url: this.updateUsernameUrl
    };
    let uu: UpdateUsername = new UpdateUsername(uu_data);
    uu.updateUsername().then(obj => {
      if(obj['done']){
          localStorage.setItem('username', obj['new_username']);
          this.userCookie['username'] = localStorage.getItem('username');
          this.api.changeUserdata(this.userCookie);
          let md_data: MessageDialogInterface = {
            title: "Modifica nome utente",
            message: obj['msg']
          };
          messageDialog(md_data);
      }//if(obj['done']){
      else{
        if(obj['expired'] == true){
          this.api.removeItems();
          this.userCookie = {};
          this.api.changeUserdata(this.userCookie);
          this.router.navigateByUrl(constants.notLoggedRedirect);
        }
        else{
          let md_data: MessageDialogInterface = {
            title: "Modifica nome utente",
            message: obj['msg']
          };
          messageDialog(md_data);
        }
      }
    }).catch(err => {
      let md_data: MessageDialogInterface = {
        title: "Modifica nome utente",
        message: Messages.EDITUSERNAME_ERROR
      };
      messageDialog(md_data);
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
          let pdi: PasswordDialogInterface = {
            title: 'Inserisci la tua password'
          };
          let pd: PasswordDialog = new PasswordDialog(pdi);
          pd.bt_ok.addEventListener('click',()=>{
            pd.instance.dispose();
            pd.div_dialog.remove();
            document.body.style.overflow = 'auto';
            let eu_params: EuParams = {
              password: pd.i_pass.value,
              username: this.groupEu.controls['username'].value
            };
            this.editUsernameRequest(eu_params);
          });
          pd.bt_canc.addEventListener('click',()=>{
            pd.instance.dispose();
            pd.div_dialog.remove();
            document.body.style.overflow = 'auto';
          })
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
        messageDialog(mdi);
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
      else{
        this.usernameError = true;
      }
    }).catch(err => {
      this.usernameError = true;
    });
  }

  private observeFromService(): void{
    this.api.userChanged.subscribe(userdata => {
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

  private setFormGroupUsername(): void{
    this.groupEu = this.fb.group({
      'username': ['', Validators.compose([Validators.required, Validators.minLength(3)])]
    });
  }

}