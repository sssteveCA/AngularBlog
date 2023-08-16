import { HttpClient } from '@angular/common/http';
import { Component, OnInit, OnChanges, SimpleChanges, Input, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { MatFormField, MatFormFieldControl } from '@angular/material/form-field';
import { MatInput, MatInputModule } from '@angular/material/input';
import * as constants from '../../../../../constants/constants';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import { Messages } from 'src/constants/messages';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import { messageDialog } from 'src/functions/functions';
import UpdateUsernameInterface from 'src/interfaces/requests/profile/updateusername.interface';
import UpdateUsername from 'src/classes/requests/profile/updateusername';
import PasswordDialogInterface from 'src/interfaces/dialogs/passworddialog.interface';
import PasswordDialog from 'src/classes/dialogs/passworddialog';
import { EuParams, UserCookie } from 'src/constants/types';
import { Keys } from 'src/constants/keys';
import { LogindataService } from 'src/app/services/logindata.service';
import { EventEmitter } from '@angular/core'


@Component({
  selector: 'app-username',
  templateUrl: './username.component.html',
  styleUrls: ['./username.component.scss']
})
export class UsernameComponent implements OnInit, OnChanges {

  cookie: UserCookie = {}
  groupEu: FormGroup; //Edit username form group
  getUsernameUrl: string = constants.profileGetUsernameUrl;
  updateUsernameUrl: string = constants.profileUpdateUsernameUrl;
  showUsernameSpinner: boolean = false;
  spinnerId: string = "username-spinner"
  usernameError: boolean = false;
  messageError: string = "Impossibile rilevare il tuo nome utente";
  @Input() usernameObject: object;
  @Output() sessionExpired: EventEmitter<boolean> = new EventEmitter<boolean>();

  constructor(public http: HttpClient, public fb: FormBuilder, public router: Router, private loginData: LogindataService) {
    this.setFormGroupUsername();
   }

   ngOnChanges(changes: SimpleChanges){
    if('usernameObject' in changes){
      this.usernameError = changes['usernameObject']['currentValue'][Keys.DONE] == false ? true : false;
      if(changes['usernameObject']['currentValue']['username']){
        this.groupEu.controls['username'].setValue(changes['usernameObject']['currentValue']['username']);
      }
    }
   }

  ngOnInit(): void {
  }


  private editUsernameRequest(eu_params: EuParams): void{
    let uu_data: UpdateUsernameInterface = {
      http: this.http,
      token_key: localStorage.getItem('token_key') as string,
      new_username: eu_params.username,
      password: eu_params.password,
      url: this.updateUsernameUrl
    };
    let uu: UpdateUsername = new UpdateUsername(uu_data);
    uu.updateUsername().then(obj => {
      if(obj[Keys.DONE]){
          localStorage.setItem('username', obj['new_username']);
          (this.cookie).username = localStorage.getItem('username');
          this.loginData.changeUserCookieData({
            token_key: localStorage.getItem('token_key'),
            username: localStorage.getItem('username')
          })
          let md_data: MessageDialogInterface = {
            title: "Modifica nome utente",
            message: obj[Keys.MESSAGE]
          };
          messageDialog(md_data);
      }//if(obj[Keys.DONE]){
      else{
        if(obj[Keys.EXPIRED] == true){
          this.sessionExpired.emit(true);
        }
        else{
          let md_data: MessageDialogInterface = {
            title: "Modifica nome utente",
            message: obj[Keys.MESSAGE]
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

  private setFormGroupUsername(): void{
    this.groupEu = this.fb.group({
      'username': ['', Validators.compose([Validators.required, Validators.minLength(3)])]
    });
  }

}
