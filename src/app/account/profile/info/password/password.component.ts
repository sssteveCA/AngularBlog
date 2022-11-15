import { HttpClient } from '@angular/common/http';
import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatCheckbox } from '@angular/material/checkbox';
import { ApiService } from 'src/app/api.service';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import UpdatePassword from 'src/classes/requests/profile/updatepassword';
import { Messages } from 'src/constants/messages';
import { EpParams } from 'src/constants/types';
import { messageDialog } from 'src/functions/functions';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import UpdatePasswordInterface from 'src/interfaces/requests/profile/updatepassword.interface';
import * as constants from '../../../../../constants/constants';

@Component({
  selector: 'app-password',
  templateUrl: './password.component.html',
  styleUrls: ['./password.component.scss']
})
export class PasswordComponent implements OnInit {

  @ViewChild('currentPwd', {static: false}) iCurrPwd: ElementRef<HTMLInputElement>;
  @ViewChild('newPwd', {static: false}) iNewPwd: ElementRef<HTMLInputElement>;
  @ViewChild('confNewPwd', {static: false}) iConfNewPwd: ElementRef<HTMLInputElement>;
  @ViewChild('showPwd', {static: false}) cbShowPwd: MatCheckbox;

  userCookie: any = {};
  groupEp: FormGroup; //Edit password form group

  updatePasswordUrl: string = constants.profileUpdatePasswordUrl;
  showPasswordSpinner: boolean = false;

  constructor(public http: HttpClient, public api: ApiService, public fb: FormBuilder) {
    this.observeFromService();
    this.setFormGroupPassword();
   }

  ngOnInit(): void {
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
    this.showPasswordSpinner = true;
    ep.updatePassword().then(obj => {
      this.showPasswordSpinner = false;
      let md_data: MessageDialogInterface = {
        title: 'Modifica password', message: obj['msg']
      };
      messageDialog(md_data);
    }).catch(err => {
      this.showPasswordSpinner = false;
      let md_data: MessageDialogInterface = {
        title: 'Modifica password', message: Messages.EDITPASSWORD_ERROR
      };
      messageDialog(md_data);
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
        let md_data: MessageDialogInterface = {
          title: 'Modifica password',
          message: 'Uno o più dati tra quelli richiesti hanno un formato non valido'
        };
        messageDialog(md_data);
      }
    }

    private observeFromService(): void{
      this.api.userChanged.subscribe(userdata => {
        this.userCookie['token_key'] = userdata['token_key'];
        this.userCookie['username'] = userdata['username'];
      });
    }

    private setFormGroupPassword(): void{
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
