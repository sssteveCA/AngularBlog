import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { LoginStatusResponse } from 'src/constants/types';
import * as constants from '../../constants/constants';

@Injectable({
  providedIn: 'root'
})
export class LogindataService {

  private userCookie = new Subject<LoginStatusResponse>();

  constructor(private http: HttpClient) {

  }

  /**
   * Check if user is still logged
   */
  private async loginStatusRequest(): Promise<boolean>{
    let logged = false;
    const token_key = localStorage.getItem('token_key');
    const username = localStorage.getItem('username');
    if(token_key && username){
      //Token_key exists
      const headers = new HttpHeaders({'Accept': 'application/json','Content-Type': 'application/json'});
      const body: object = {
        'token_key': token_key, 
        'username': username
      };
      await this.loginStatusRequestPromise(constants.loginStatusUrl,body,headers).then(res => {
        let rJson = JSON.parse(res);
        if(rJson['logged'] == true){
          logged = true;
        }
      }).catch(err => {});
    }//if(token_key && username){
    return logged;
  }

  private async loginStatusRequestPromise(url: string, body: object, headers: HttpHeaders): Promise<string>{
    let promise = await new Promise<string>((resolve,reject) => {
      this.http.post(url,body,{headers: headers, responseType: 'text'}).subscribe({
        next: (res) => resolve(res),
        error: (error) => {
          reject(error);
        }
      })
    });
    return promise;
  }


}
