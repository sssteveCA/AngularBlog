import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormControl, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import * as constants from '../../../constants/constants';
import * as functions from '../../../functions/functions';
declare var $:any;

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
          functions.dialogMessage($,"Attivazione account","Inserisci un codice di attivazione");
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
      /*if(this.rJson.status == 1){
        functions.dialogMessage($,"Attivazione account","Account attivato con successo");
      }*/
      
      /*else if(this.rJson.status == -1){
        functions.dialogMessage($,"Attivazione account","Codice non valido");
      }*/
    }
    else{
      //if emailCode input is void
      functions.dialogMessage($,"Attivazione account","Inserisci un codice di attivazione");
    }
  }

}
