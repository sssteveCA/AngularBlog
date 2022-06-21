import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class Api2Service {

  constructor(public http: HttpClient) { }

  //Check if user is authorized to do a certain action
  async isAuthorized(): Promise<boolean>{
    let authorized = false;
    return authorized;
  }

  async isAuthorizedPromise(url: string, body: any, headers: any){
    let promise = await new Promise((resolve,reject) => {
      this.http.post(url,body,{headers: headers,responseType: 'text'}).subscribe(res => {
        resolve(res);
      },error => {
        reject(error);
      });
    });
    return promise;
  }
}
