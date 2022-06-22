import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import * as constants from '../constants/constants';
import * as messages from '../messages/messages';

@Injectable({
  providedIn: 'root'
})
export class Api2Service {

  private userCookie = new Subject<any>();

  constructor(public http: HttpClient) { }

  //Check if user is authorized to do a certain action to an article
  async isAuthorizedArticle(id: string): Promise<any>{
    let authStatus = {
      'authorized': false,
      'msg': '',
      'article': {}
    };
    const url = constants.articleAuthorizedUrl;
    const data = {
      'token_key': localStorage.getItem('token_key'),
      'username': localStorage.getItem('username'),
      'article_id': id
    };
    const headers = new HttpHeaders({
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    });
    await this.isAuthorizedArticlePromise(url,data,headers).then(res => {
      console.log("Api2Service isAuthorizedArticlePromise => ");
      console.log(res);
      let rJson = JSON.parse(res as string);
      if(rJson['authorized'] == true){
        authStatus['authorized'] = true;
        authStatus['article'] = rJson['article']
      }
      else{
        authStatus['msg'] = rJson['msg'];
      }
    }).catch(err => {
      authStatus['msg'] = messages.articleAuthorizedError;
    });
    return authStatus;
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

