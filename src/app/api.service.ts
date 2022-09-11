import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Observable, Subject } from 'rxjs';
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
  async loginStatusRequest(url: string, body: any, headers: any){
    let promise = await new Promise((resolve,reject) => {
      this.http.post(url,body,{headers: headers, responseType: 'text'}).subscribe(res => {
        //console.log("Login status request resolve => ");
        //console.log(res);
        resolve(res);       
      },
      error =>{
        console.warn("Login status request error => ");
        //console.warn(error);
        reject(error);
      });
    });
    return promise;
  }

  //change login status
  async getLoginStatus(): Promise<boolean>{
    //console.log("api service getLoginStatus()");
    let logged = false;
    const token_key = localStorage.getItem('token_key');
    const username = localStorage.getItem('username');
    if(token_key && username){
      //Token_key exists
      const headers = new HttpHeaders({'Accept': 'application/json','Content-Type': 'application/json'});
      const body = {'token_key': token_key, 'username': username};
      await this.loginStatusRequest(constants.loginStatusUrl,body,headers).then(res => {
        let rJson = JSON.parse(res as string);
        if(rJson['logged'] == true){
          logged = true;
        }
      }).catch(err => {
      });
    }//if(token_key && username){
    //console.log("api service getLoginStatus() finish");
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
