import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {

  private userCookie = new Subject<any>();
  private loginStatus = new Subject<boolean>();
  userChanged = this.userCookie.asObservable();

  constructor(private http: HttpClient) { 

  }

  //change login status
  changeLoginStatus(logged: boolean){
    this.loginStatus.next(logged);
  }

  //when the value assigned to localStorage item "username" change
  changeUserdata(u: any){
    this.userCookie.next(u);
  }
}
