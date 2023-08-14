import { CdkAriaLive } from '@angular/cdk/a11y';
import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { Router } from '@angular/router';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import PasswordConfirmDialog from 'src/classes/dialogs/passwordconfirmdialog';
import DeleteProfile from 'src/classes/requests/profile/deleteprofile';
import { Keys } from 'src/constants/keys';
import { Messages } from 'src/constants/messages';
import { DaParams } from 'src/constants/types';
import { messageDialog } from 'src/functions/functions';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import PasswordConfirmDialogInterface from 'src/interfaces/dialogs/passwordconfirmdialog.interface';
import DeleteProfileInterface from 'src/interfaces/requests/profile/deleteprofile.interface';
import * as constants from '../../../../../constants/constants';
import { LogindataService } from 'src/app/services/logindata.service';

@Component({
  selector: 'app-delete-account',
  templateUrl: './delete-account.component.html',
  styleUrls: ['./delete-account.component.scss']
})
export class DeleteAccountComponent implements OnInit {

  deleteProfileUrl: string = constants.profileDeleteUrl;
  showDeleteProfileSpinner: boolean = false;
  spinnerId: string = "delete-account-spinner"

  constructor(public http: HttpClient, public fb: FormBuilder, public router: Router, private loginData: LogindataService) {
   }

  ngOnInit(): void {
  }

  private deleteAccountRequest(da_params: DaParams): void{
    let da_data: DeleteProfileInterface = {
      conf_password: da_params.conf_password,
      http: this.http,
      password: da_params.password,
      token_key: localStorage.getItem('token_key') as string,
      url: this.deleteProfileUrl
    };
    let da: DeleteProfile = new DeleteProfile(da_data);
    this.showDeleteProfileSpinner = true;
    da.deleteProfile().then(obj => {
      this.showDeleteProfileSpinner = false;
      const mdData: MessageDialogInterface = {
        title: 'Cancellazione account', message: obj[Keys.MESSAGE]
      }
      let md: MessageDialog = new MessageDialog(mdData);
      md.bt_ok.addEventListener('click',()=>{
        md.instance.dispose();
        md.div_dialog.remove();
        document.body.style.overflow = 'auto';
        if(obj[Keys.DONE] == true){
          this.loginData.removeItems();
          this.loginData.changeUserCookieData({});
          this.router.navigateByUrl(constants.deleteAccountRedirect);
        }
      });
    }).catch(err => {
      const mdData: MessageDialogInterface = {
        title: 'Cancellazione account',
        message: Messages.DELETEACCOUNT_ERROR
      }
      messageDialog(mdData);
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

}
