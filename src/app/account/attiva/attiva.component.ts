import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormControl, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import * as constants from '../../../constants/constants';
import * as messages from '../../../messages/messages';
import ActivateRequestInterface from 'src/interfaces/requests/activaterequest.interface';
import ActivateRequest from 'src/classes/requests/activaterequest';

@Component({
  selector: 'app-attiva',
  templateUrl: './attiva.component.html',
  styleUrls: ['./attiva.component.scss']
})
export class AttivaComponent implements OnInit {

  fromSubmit: boolean = false; //if get request was performed from activation form
  status : number = 0;
  emailCode : FormControl = new FormControl();
  invalidCode: string = "Codice non valido";
  activationError: string = "Errore durante l'attivazione dell'account. Se il problema persiste contattare l'amministratore del sito";

  constructor(public http: HttpClient, private route: ActivatedRoute) {
    this.route.queryParams.subscribe(params =>{
      const emailVerif = (typeof params['emailVerif'] == "string") ? params['emailVerif'] : null;
      this.active(emailVerif);
    });
    this.emailCode.setValidators([Validators.required]);
    
   }

  ngOnInit(): void {
  }

  active(emailVerif: string|null): void{
    let arData: ActivateRequestInterface = {
      emailVerif: emailVerif,
      http: this.http,
      url: constants.activationUrl
    }
    let ar: ActivateRequest = new ActivateRequest(arData)
    ar.activate().then(obj => {
      this.status = obj['status'];
      if(this.fromSubmit){
        if(this.status == 0){
          const data: MessageDialogInterface = {
            title: 'Attivazione account',
            message: messages.activationCodeMissing
          };
          let md = new MessageDialog(data);
          md.bt_ok.addEventListener('click',()=>{
            md.instance.dispose();
            md.div_dialog.remove();
            document.body.style.overflow = 'auto';
          });
        }
        this.fromSubmit = false;
      }
    })
  }

  //when user submit the activation form
  onSubmit(): void{
    if(this.emailCode.valid){
      this.fromSubmit = true;
      this.active(this.emailCode.value);
    }
    else{
      //if emailCode input is void
      const data: MessageDialogInterface = {
        title: 'Attivazione account',
        message: messages.activationCodeMissing
      };
      let md = new MessageDialog(data);
      md.bt_ok.addEventListener('click',()=>{
        md.instance.dispose();
        md.div_dialog.remove();
      });
    }
  }

}
