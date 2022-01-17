import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import * as constants from '../../../constants/constants';
import * as functions from '../../../functions/functions';

@Component({
  selector: 'app-contacts',
  templateUrl: './contacts.component.html',
  styleUrls: [
    './contacts.component.scss',
  ]
})
export class ContactsComponent implements OnInit {

  contactForm: FormGroup;

  constructor(public fb: FormBuilder, public http: HttpClient) {
    this.contactForm = fb.group({
      'email' : ['',Validators.compose([Validators.required,Validators.email])],
      'subject' : ['',Validators.required],
      'message' : ['',Validators.required]
    });
   }

  ngOnInit(): void {
  }

  //when user submit the contact form
  onSubmit():void{
    console.log("onSubmit");
    if(this.contactForm.valid){
      console.log("valid");
      let dati = {
        email : this.contactForm.controls['email'].value,
        subject : this.contactForm.controls['subject'].value,
        message : this.contactForm.controls['message'].value
      };
      //console.log(dati);
      this.sendEmail(dati);
    }//if(this.contactForm.valid){
    else{
    }
  }

  sendEmail(data: any): void{
    let params = new HttpParams({fromObject: data});
    this.http.post(constants.contactUrl,params,{responseType: 'text'}).subscribe(res => {
      console.log(res);
      try{
        let rJson = JSON.parse(res);
        console.log(rJson);
        functions.dialogMessage('Contatti',rJson['msg']);
      }catch(e){
        console.warn(e);
      }
      
    });
  }

}
