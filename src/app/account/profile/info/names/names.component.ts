import { HttpClient } from '@angular/common/http';
import { Component, Input, OnInit, OnChanges, SimpleChanges } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import UpdateNames from 'src/classes/requests/profile/updatenames';
import { Keys } from 'src/constants/keys';
import { Messages } from 'src/constants/messages';
import { EnParams } from 'src/constants/types';
import { messageDialog } from 'src/functions/functions';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import UpdateNamesInterface from 'src/interfaces/requests/profile/updatenames.interface';
import * as constants from '../../../../../constants/constants';

@Component({
  selector: 'app-names',
  templateUrl: './names.component.html',
  styleUrls: ['./names.component.scss']
})
export class NamesComponent implements OnInit, OnChanges {

  userCookie: any = {};
  updateNamesUrl: string = constants.profileUpdateNamesUrl;
  getNamesUrl: string = constants.profileGetNamesUrl;
  groupNames: FormGroup;
  showNamesSpinner: boolean = false;
  namesError: boolean = false;
  @Input() namesObject: object;
  

  constructor(public http: HttpClient, public api: ApiService, public router: Router, public fb: FormBuilder) { 
    this.observeFromService();
    this.setFormGroupNames();
  }

  ngOnChanges(changes: SimpleChanges): void {
      /* console.log("Names component ngOnChanges changes => ");
      console.log(changes); */
      if('namesObject' in changes){
        this.namesError = changes['namesObject']['currentValue'][Keys.DONE] == false ? true : false;
        if(changes['namesObject']['currentValue']['name'] && changes['namesObject']['currentValue']['surname']){
          this.groupNames.controls['name'].setValue(changes['namesObject']['currentValue']['name']);
          this.groupNames.controls['surname'].setValue(changes['namesObject']['currentValue']['surname']);
        }
      }
  }

  ngOnInit(): void {
  }

  private editNamesRequest(en_params: EnParams): void{
    let un_data: UpdateNamesInterface = {
      http: this.http,
      token_key: this.userCookie['token_key'],
      new_name: en_params.name,
      new_surname: en_params.surname,
      url: this.updateNamesUrl
    }
    let un: UpdateNames = new UpdateNames(un_data);
    this.showNamesSpinner = true;
    un.updateUsername().then(obj => {
      this.showNamesSpinner = false;
      let mdi: MessageDialogInterface = {
        title: 'Modifica nome e cognome',
        message: obj[Keys.MESSAGE]
      }
      messageDialog(mdi);
    });
  }

  /**
   * When user submit names form
   */
  editNamesSubmit(): void{
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
        let en_params: EnParams = {
          name: this.groupNames.controls["name"].value,
          surname: this.groupNames.controls["surname"].value
        }
        this.editNamesRequest(en_params);
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
