import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { UserCookie } from 'src/constants/types';

@Injectable({
  providedIn: 'root'
})
export class LogindataService {

  private userCookie = new Subject<UserCookie>();

  constructor(private http: HttpClient) {

  }


}
