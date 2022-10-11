import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Form, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import { Messages } from 'src/constants/messages';
import * as constants from '../../../constants/constants';
import LoginRequestInterface from 'src/interfaces/loginrequest.interface';
import { THIS_EXPR } from '@angular/compiler/src/output/output_ast';
import LoginRequest from 'src/classes/requests/loginrequest';
import { messageDialog } from 'src/functions/functions';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {

  loginForm: FormGroup;
  login_url: string = constants.loginUrl;
  showPassword: boolean = false;
  @ViewChild('password',{static: false}) iPass: ElementRef;
  public userCookie : any = {};
  showSpinner:boolean = false;

  constructor(private fb: FormBuilder,private router: Router, private http:HttpClient, private api: ApiService) {
    this.loginForm = fb.group({
      'username' : ['',Validators.compose([Validators.required,Validators.minLength(5)])],
      'password' : ['',Validators.compose([Validators.required,Validators.minLength(6)])]
      });
     }

  ngOnInit(): void {
  }

  fShowPassword(): void{
    //show or hide password
    this.showPassword = !this.showPassword;
    if(this.showPassword){
      //user want show password
      this.iPass.nativeElement.setAttribute('type','text');
    }//if(this.showPassword){
    else{
      //user wants hide password
      this.iPass.nativeElement.setAttribute('type','password');
    }
  }

  //user try to login with data passed
  login(data: LoginRequestInterface): void{
    let login: LoginRequest = new LoginRequest(data);
    this.showSpinner = true;
    login.login().then(obj => {
      this.showSpinner = false;
      //console.log(obj);
      if(obj['done'] && typeof obj['username'] !== 'undefined'){
        localStorage.setItem("token_key",obj["token_key"]);
        localStorage.setItem("username",obj["username"]);
        this.userCookie["token_key"] = localStorage.getItem("token_key");
        this.userCookie["username"] = localStorage.getItem("username");
        this.api.changeUserdata(this.userCookie);
        this.router.navigate([constants.loginRedirect]);
      }
      else{
        //console.log(rJson);
        const md_data: MessageDialogInterface = {
          title: 'Login',
          message: obj['msg']
        };
        messageDialog(md_data);
      }
    }).catch(err => {
      //console.log(err);
      this.showSpinner = false;
      const md_data: MessageDialogInterface = {
        title: 'Login',
        message: Messages.LOGIN_ERROR
      };
      messageDialog(md_data);
    })
  }

  //when login form is submitted
  onSubmit(): void{
    if(this.loginForm.valid){
      //if all inputs are valid
      const data: LoginRequestInterface = {
        http: this.http,
        username: this.loginForm.controls['username'].value,
        password: this.loginForm.controls['password'].value,
        url: this.login_url
      };
      this.login(data);
    }//if(this.loginForm.valid){
    else{
      const md_data: MessageDialogInterface = {
        title: 'Login',
        message: Messages.INVALIDDATA_ERROR
      };
      messageDialog(md_data);
    }
    
  }

}
