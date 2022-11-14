import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import * as constants from '../../../constants/constants';

@Component({
  selector: 'app-contacts',
  templateUrl: './contacts.component.html',
  styleUrls: [
    './contacts.component.scss',
  ]
})
export class ContactsComponent implements OnInit {

  contactForm: FormGroup;
  showSpinner: boolean = false;

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
    if(this.contactForm.valid){
      let dati = {
        email : this.contactForm.controls['email'].value,
        subject : this.contactForm.controls['subject'].value,
        message : this.contactForm.controls['message'].value
      };
      //console.log(dati);
      this.showSpinner = true;
      this.sendEmail(dati);
    }//if(this.contactForm.valid){
    else{
    }
  }

  sendEmail(data: any): void{
    let params = new HttpParams({fromObject: data});
    this.http.post(constants.contactUrl,params,{responseType: 'text'}).subscribe(res => {
      this.showSpinner = false;
      //console.log(res);
      try{
        let rJson = JSON.parse(res);
        //console.log(rJson);
        const data: MessageDialogInterface = {
          title: 'Contatti',
          message: rJson['msg']
        };
        let md = new MessageDialog(data);
        md.bt_ok.addEventListener('click',()=>{
          md.instance.dispose();
          md.div_dialog.remove();
          document.body.style.overflow = 'auto';
        });
      }catch(e){
        console.warn(e);
      }
      
    });
  }

}
