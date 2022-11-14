import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import * as constants from '../../../constants/constants';
import * as messages from '../../../messages/messages';
import MessageDialog from '../../../classes/dialogs/messagedialog';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import { Messages } from 'src/constants/messages';
import SubscribeRequestInterface from 'src/interfaces/requests/subscriberequest.interface';
import SubscribeRequest from 'src/classes/requests/subscriberequest';
import { messageDialog } from 'src/functions/functions';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements OnInit {

  @ViewChild('password',{static: false}) iPass: ElementRef;
  @ViewChild('confPwd',{static: false}) iConfPwd: ElementRef;

  subscribeForm: FormGroup;
  showPassword: boolean = false;
  showConf: boolean = false;
  subscribe_url: string = constants.registerUrl;
  showSpinner: boolean = false;
  confPwdValidatorsTrue: boolean = true;
  
  constructor(public fb: FormBuilder, public http: HttpClient) {
    this.subscribeForm = fb.group({
      'name' : ['',Validators.compose([Validators.required,Validators.minLength(3)])],
      'surname' : ['',Validators.compose([Validators.required,Validators.minLength(2)])],
      'username' : ['',Validators.compose([Validators.required,Validators.minLength(5)])],
      'email' : ['',Validators.compose([Validators.required,Validators.email])],
      'password' : ['',Validators.compose([Validators.required,Validators.minLength(6)])],
      'confPwd' : ['',Validators.compose([Validators.required,Validators.minLength(6)])],
    });
   }

  ngOnInit(): void {
  }

  //when user submit registration form
  onSubmit(): void{
    if(this.subscribeForm.valid){
      //form data are all valid
      let dati: SubscribeRequestInterface = {
        http: this.http,
        name : this.subscribeForm.controls['name'].value,
        surname : this.subscribeForm.controls['surname'].value,
        username : this.subscribeForm.controls['username'].value,
        email : this.subscribeForm.controls['email'].value,
        password : this.subscribeForm.controls['password'].value,
        confPwd : this.subscribeForm.controls['confPwd'].value,
        subscribed: 0,
        url: this.subscribe_url
      };
      //console.log(dati);
      if(dati['password'] == dati['confPwd']){
        this.subscribe(dati);
      }
      else{
        const data: MessageDialogInterface = {
          title: 'Registrazione',
          message: messages.passwordMismatch
        };
        let md = new MessageDialog(data);
        md.bt_ok.addEventListener('click',()=>{
          md.instance.dispose();
          md.div_dialog.remove();
          document.body.style.overflow = 'auto';
        });
      }
    }//if(this.formGroup.valid){
    else{
      //invalid form data
      const data: MessageDialogInterface = {
        title: 'Registrazione',
        message: messages.invalidData
      };
      let md = new MessageDialog(data);
      md.bt_ok.addEventListener('click',()=>{
        md.instance.dispose();
        md.div_dialog.remove();
      });
    }
  }

  fShowPassword(): void{
    //show or hide password
    this.showPassword = !this.showPassword;
    if(this.showPassword){
      //user want show password
      this.iPass.nativeElement.setAttribute('type','text');
      this.iConfPwd.nativeElement.setAttribute('type','text');
    }//if(this.showPassword){
    else{
      //user wants hide password
      this.iPass.nativeElement.setAttribute('type','password');
      this.iConfPwd.nativeElement.setAttribute('type','password');
    }
  }

  //if data are correct subscribe to blog
  subscribe(data: SubscribeRequestInterface): void{
    let subscribe: SubscribeRequest = new SubscribeRequest(data);
    this.showSpinner = true;
    subscribe.subscribe().then(obj => {
      this.showSpinner = false;
      const md_data: MessageDialogInterface = {
        title: 'Registrazione',
        message: obj['msg']
      };
      messageDialog(md_data);
    }).catch(err => {
      this.showSpinner = false;
      const md_data: MessageDialogInterface = {
        title: 'Registrazione',
        message: Messages.SUBSCRIBE_ERROR
      };
      messageDialog(md_data);
    });
  }

}


