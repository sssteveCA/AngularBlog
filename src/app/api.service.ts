import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {

  private userCookie = new Subject<any>();
  userChanged = this.userCookie.asObservable();

  constructor(private http: HttpClient) { 

  }

  //when the value assigned to localStorage item "username" change
  changeUserdata(u: any){
    this.userCookie.next(u);
  }
}
