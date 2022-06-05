import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Subject } from 'rxjs';
import * as constants from '../constants/constants';

@Injectable({
  providedIn: 'root'
})
export class ApiService {

  private userCookie = new Subject<any>();
  private loginStatus = new Subject<boolean>();
  userChanged = this.userCookie.asObservable();
  loginChanged = this.loginStatus.asObservable();

  constructor(private http: HttpClient,private router: Router) { 

  }

  //wait for HTTP post request response
  async loginStatusRequest(url: string, body: any, options: any){
    let promise = await new Promise((resolve,reject) => {
      this.http.post(url,body,options).subscribe(res => {
        resolve(res);
      },
      error =>{
        reject(error);
      });
    });
    return promise;
  }

  //change login status
  getLoginStatus(): boolean{
    let logged = false;
    const token_key = localStorage.getItem('token_key');
    const username = localStorage.getItem('username');
    if(token_key && username){
      //Token_key exists
      const options = {
        headers: new HttpHeaders({'Accept': 'application/json','Content-Type': 'application/json'})
      };
      const body = {'token_key': token_key, 'username': username};
      this.loginStatusRequest(constants.loginStatusUrl,body,options).then(res => {
        console.log("loginStatusRequest res => ");
        console.log(res);
      }).catch(err => {
        console.warn("loginStatusRequest err => ");
        console.warn(err);
      });
    }//if(token_key && username){
    return logged;
  }

  changeLoginStatus(logged: boolean){
    this.loginStatus.next(logged);
  }

  //when the value assigned to localStorage item "username" change
  changeUserdata(u: any){
    this.userCookie.next(u);
  }

  //Remove localStorage items and redirect to non private area
  removeItems(){
    const token_key = localStorage.getItem('token_key');
    const username = localStorage.getItem('username');
    if(token_key)
      localStorage.removeItem("token_key");
    if(username)
      localStorage.removeItem("username");
  }
}
