import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import { Messages } from 'src/constants/messages';
import { messageDialog } from 'src/functions/functions';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';

@Component({
  selector: 'app-names',
  templateUrl: './names.component.html',
  styleUrls: ['./names.component.scss']
})
export class NamesComponent implements OnInit {

  userCookie: any = {};
  groupNames: FormGroup;
  showNamesSpinner: boolean = false;
  namesError: boolean = false;

  constructor(public http: HttpClient, public api: ApiService, public router: Router, public fb: FormBuilder) { 
    this.observeFromService();
    this.setFormGroupNames();
  }

  ngOnInit(): void {
  }

  /**
   * When user submit names form
   */
  namesSubmit(): void{
    if(this.groupNames.valid){
      let cdi: ConfirmDialogInterface = {
        title: 'Modifica nome e cognome',
        message: Messages.EDITNAMES_CONFIRM
      };
      let cd: ConfirmDialog = new ConfirmDialog(cdi);
      cd.bt_yes.addEventListener('click',()=>{
        cd.instance.dispose();
        cd.div_dialog.remove();
        document.body.style.overflow = 'auto';
      });
      cd.bt_no.addEventListener('click',()=>{
        cd.instance.dispose();
        cd.div_dialog.remove();
        document.body.style.overflow = 'auto';
      });
    }//if(this.groupNames.valid){
    else{
      let mdi: MessageDialogInterface = {
        title: 'Modifica nome e cognome',
        message: Messages.INVALIDDATA_ERROR
      };
      messageDialog(mdi);
    }
  }

  private observeFromService(): void{
    this.api.userChanged.subscribe(userdata => {
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

  private setFormGroupNames(): void{
    this.groupNames = this.fb.group({
      'name': ['', Validators.compose([Validators.required, Validators.minLength(3)])],
      'surname': ['', Validators.compose([Validators.required,Validators.minLength(2)])]
    });
  }

}
