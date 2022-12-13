import { CdkAriaLive } from '@angular/cdk/a11y';
import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import PasswordConfirmDialog from 'src/classes/dialogs/passwordconfirmdialog';
import DeleteProfile from 'src/classes/requests/profile/deleteprofile';
import { Messages } from 'src/constants/messages';
import { DaParams } from 'src/constants/types';
import { messageDialog } from 'src/functions/functions';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import PasswordConfirmDialogInterface from 'src/interfaces/dialogs/passwordconfirmdialog.interface';
import DeleteProfileInterface from 'src/interfaces/requests/profile/deleteprofile.interface';
import * as constants from '../../../../../constants/constants';

@Component({
  selector: 'app-delete-account',
  templateUrl: './delete-account.component.html',
  styleUrls: ['./delete-account.component.scss']
})
export class DeleteAccountComponent implements OnInit {

  userCookie: any = {};

  deleteProfileUrl: string = constants.profileDeleteUrl;
  showDeleteProfileSpinner: boolean = false;

  constructor(public http: HttpClient, public api: ApiService, public fb: FormBuilder, public router: Router) {
    this.observeFromService();
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
      const mdData: MessageDialogInterface = {
        title: 'Cancellazione account', message: obj["msg"]
      }
      let md: MessageDialog = new MessageDialog(mdData);
      md.bt_ok.addEventListener('click',()=>{
        md.instance.dispose();
        md.div_dialog.remove();
        document.body.style.overflow = 'auto';
        if(obj["done"] == true){
          this.api.removeItems();
          this.api.changeUserdata({});
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

  private observeFromService(): void{
    this.api.userChanged.subscribe(userdata => {
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

}
