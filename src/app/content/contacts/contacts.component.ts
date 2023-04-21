import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import ContactsRequest from 'src/classes/requests/contactsrequest';
import { Keys } from 'src/constants/keys';
import { Messages } from 'src/constants/messages';
import { messageDialog } from 'src/functions/functions';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import ContactsRequestInterface from 'src/interfaces/requests/constactsrequest.interface';
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
  title: string = "Contatti";

  constructor(public fb: FormBuilder, public http: HttpClient) {
    this.formBuild();
   }

  ngOnInit(): void {
  }

  formBuild(): void{
    this.contactForm = this.fb.group({
      'email' : ['',Validators.compose([Validators.required,Validators.email])],
      'subject' : ['',Validators.required],
      'message' : ['',Validators.required]
    });
  }

  //when user submit the contact form
  onSubmit():void{
    if(this.contactForm.valid){
      let data: ContactsRequestInterface = {
        email : this.contactForm.controls['email'].value,
        http: this.http,
        message : this.contactForm.controls['message'].value,
        subject : this.contactForm.controls['subject'].value,
        url: constants.contactUrl  
      };
      this.sendEmail(data);
    }//if(this.contactForm.valid){
    else{
      const mdData: MessageDialogInterface = {
        title: 'Contatti', message: Messages.INVALIDDATA_ERROR
      };
      messageDialog(mdData);
    }
  }

  sendEmail(data: ContactsRequestInterface): void{
    //let params = new HttpParams({fromObject: data});
    let cr: ContactsRequest = new ContactsRequest(data);
    this.showSpinner = true;
    cr.contactsRequest().then(obj => {
      this.showSpinner = false;
      const data: MessageDialogInterface = {
        title: 'Contatti',
        message: obj[Keys.MESSAGE]
      };
      messageDialog(data);
    });
  }
}
