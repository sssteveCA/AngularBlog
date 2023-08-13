import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { UserCookie } from 'src/constants/types';
import * as constants from '../../constants/constants';

@Injectable({
  providedIn: 'root'
})
export class LogindataService {

  private userCookie = new Subject<UserCookie>();
  public userCookieObservable = this.userCookie.asObservable();

  constructor(private http: HttpClient) {
  }

  /**
   * Check if user is still logged
   * @returns 
   */
  public async checkLoginStatus(): Promise<boolean>{
    return await new Promise<boolean>((resolve,reject) => {
      this.loginStatusRequest().then(logged => {
        if(logged) resolve(true);
        else{
          this.removeItems();
          reject(false);
        } 
      })
    })
  }

  /**
   * Check if user is still logged
   */
  private  async loginStatusRequest(): Promise<boolean>{
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

  /**
   * Chenga the value of the userCookie property
   * @param userCookie the new value of the userCookie property
   */
  public changeUserCookieData(userCookie: UserCookie): void{
    this.userCookie.next(userCookie);
  }

  /**
   * Remove userCookie values and from local storage
   */
  removeItems(){
    const token_key = localStorage.getItem('token_key');
    const username = localStorage.getItem('username');
    if(token_key)
      localStorage.removeItem("token_key");
    if(username)
      localStorage.removeItem("username");
    //this.changeUserCookieData({});
  }


}
