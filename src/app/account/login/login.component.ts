import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Form, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import MessageDialog from 'src/classes/messagedialog';
import MessageDialogInterface from 'src/classes/messagedialog.interface';
import * as constants from '../../../constants/constants';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {

  loginForm: FormGroup;
  showPassword: boolean = false;
  @ViewChild('password',{static: false}) iPass: ElementRef;
  public userCookie : any = {};

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
  login(data: any): void{
    let params = new HttpParams({fromObject: data});
    this.http.post(constants.loginUrl, params, {responseType: 'text'}).subscribe(res => {
      console.log(res);
      try{
        let rJson = JSON.parse(res);
        console.log(rJson);
        if(rJson['done'] && typeof rJson['username'] !== 'undefined'){
          localStorage.setItem("token_key",rJson["token_key"]);
          localStorage.setItem("username",rJson["username"]);
          this.userCookie["token_key"] = localStorage.getItem("token_key");
          this.userCookie["username"] = localStorage.getItem("username");
          this.api.changeUserdata(this.userCookie);
          this.router.navigate([constants.loginRedirect]);
        }
        else{
          //console.log(rJson);
          const data: MessageDialogInterface = {
            title: 'Login',
            message: rJson['msg']
          };
          let md = new MessageDialog(data);
          md.bt_ok.addEventListener('click',()=>{
            md.instance.dispose();
            md.div_dialog.remove();
          });
        }
      }catch(e){
        console.warn(e);
      }
    });
  }

  //when login form is submitted
  onSubmit(): void{
    if(this.loginForm.valid){
      //if all inputs are valid
      let data = {
        username: this.loginForm.controls['username'].value,
        password: this.loginForm.controls['password'].value
      };
      this.login(data);
    }//if(this.loginForm.valid){
    else{
    }
    
  }

}
