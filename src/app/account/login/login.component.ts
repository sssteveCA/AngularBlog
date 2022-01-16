import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Form, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
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

  constructor(private fb: FormBuilder,private router: Router, private http:HttpClient) {
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
