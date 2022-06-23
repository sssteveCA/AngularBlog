import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormControl, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import MessageDialog from 'src/classes/messagedialog';
import MessageDialogInterface from 'src/classes/messagedialog.interface';
import * as constants from '../../../constants/constants';
import * as messages from '../../../messages/messages';

@Component({
  selector: 'app-attiva',
  templateUrl: './attiva.component.html',
  styleUrls: ['./attiva.component.scss']
})
export class AttivaComponent implements OnInit {

  fromSubmit: boolean = false; //if get request was performed from activation form
  status : number = 0;
  emailCode : FormControl = new FormControl();
  urlParams : string;

  constructor(public http: HttpClient, private route: ActivatedRoute) {
    this.route.queryParams.subscribe(params =>{
      console.log(params);
      if(typeof params['emailVerif'] !== "undefined"){
        this.urlParams = constants.activationUrl+'?emailVerif='+params['emailVerif'];
      }
      else
      this.urlParams = constants.activationUrl;
      this.active(this.urlParams);
    });
    this.emailCode.setValidators([Validators.required]);
    
   }

  ngOnInit(): void {
  }

  active(urlParams: any): void{
    //account activation
    console.log(urlParams);
    this.http.get(urlParams,{responseType: 'text'}).subscribe(resp => {
      console.log(resp);
      let rJson = JSON.parse(resp);
      this.status = rJson.status;
      console.log(this.status);
      if(this.fromSubmit){
        //display dialog message if get request was performed from form 
        if(this.status == 0){
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
        this.fromSubmit = false;
      }
    });
  }

  //when user submit the activation form
  onSubmit(): void{
    if(this.emailCode.valid){
      this.urlParams = constants.activationUrl+'?emailVerif='+this.emailCode.value;
      this.fromSubmit = true;
      this.active(this.urlParams);
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
