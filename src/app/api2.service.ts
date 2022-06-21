import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import * as constants from '../constants/constants';

@Injectable({
  providedIn: 'root'
})
export class Api2Service {

  private userCookie = new Subject<any>;

  constructor(public http: HttpClient) { }

  //Check if user is authorized to do a certain action to an article
  async isAuthorizedArticle(id: string): Promise<boolean>{
    let authorized = false;
    const url = constants.articleAuthorizedUrl;
    const data = {
      'token_key': localStorage.getItem('token_key'),
      'username': localStorage.getItem('username')
    };
    const headers = new HttpHeaders({
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    });
    await this.isAuthorizedArticlePromise(url,data,headers).then(res => {
      console.log(res);
    }).catch(err => {

    });
    return authorized;
  }

  async isAuthorizedArticlePromise(url: string, body: any, headers: any){
    let promise = await new Promise((resolve,reject) => {
      this.http.post(url,body,{headers: headers,responseType: 'text'}).subscribe(res => {
        resolve(res);
      },error => {
        reject(error);
      });
    });
    return promise;
  }

  changeUserData(u: any){
    this.userCookie.next(u);
  }
}

