import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import * as constants from '../../../constants/constants';
import * as functions from '../../../functions/functions';
declare var $:any;

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements OnInit {

  @ViewChild('password',{static: false}) iPass: ElementRef;
  @ViewChild('confPwd',{static: false}) iConfPwd: ElementRef;

  formGroup: FormGroup;
  showPassword: boolean = false;
  showConf: boolean = false;

  constructor(public fb: FormBuilder, public http: HttpClient) {
    this.formGroup = fb.group({
      'nome' : ['',Validators.compose([Validators.required,Validators.minLength(3)])],
      'cognome' : ['',Validators.compose([Validators.required,Validators.minLength(2)])],
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
    if(this.formGroup.valid){
      //form data are all valid
      let dati = {
        nome : this.formGroup.controls['nome'].value,
        cognome : this.formGroup.controls['cognome'].value,
        username : this.formGroup.controls['username'].value,
        email : this.formGroup.controls['email'].value,
        password : this.formGroup.controls['password'].value,
        confPwd : this.formGroup.controls['confPwd'].value,
      };
      console.log(dati);
      if(dati['password'] == dati['confPwd']){
        this.subscribe(dati);
      }
      else{
        functions.dialogMessage($,"Registrazione","Le due password non coincidono");
      }
    }//if(this.formGroup.valid){
    else{
      //invalid form data
      functions.dialogMessage($,"Registrazione","I dati inseriti non sono validi, riprova");
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
  subscribe(data: any): void{
    let params = new HttpParams({fromObject: data});
    /*Object.keys(data).forEach(function(key){
      //append data from Object to HttpParams
      params.append(key,data[key]);
    });*/
    console.log(params);
    this.http.post(constants.registerUrl, params,{responseType: 'text'}).subscribe(res => {
      console.log(res);
      let rJson = JSON.parse(res);
      functions.dialogMessage($,"Registrazione",rJson["msg"]);
    });
  }

}
