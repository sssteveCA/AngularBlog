import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import { HistoryItem } from 'src/app/models/historyitem.model';
import History from 'src/classes/requests/profile/history';
import { Keys } from 'src/constants/keys';
import HistoryInterface from 'src/interfaces/requests/profile/history.interface';
import * as constants from '../../../../constants/constants';

@Component({
  selector: 'app-history',
  templateUrl: './history.component.html',
  styleUrls: ['./history.component.scss']
})
export class HistoryComponent implements OnInit {

  backlink: string = "../";
  title: string = "Cronologia azioni effettuate";
  urlHistory: string = constants.profileGetHistoryUrl;
  userCookie: any = {};
  historyItems: HistoryItem[] = [];
  empty: boolean = false;
  error: boolean = false;
  notLoading: boolean = false;

  constructor(private api: ApiService, private http: HttpClient, private router: Router) {
    this.loginStatus(); 
    this.observeFromService();
    this.getHistory();
   }

  ngOnInit(): void {
  }

  getHistory(): void{
    let historyData: HistoryInterface = {
      http: this.http,
      token_key: localStorage.getItem('token_key') as string,
      url: this.urlHistory
    }
    let history: History = new History(historyData);
    history.history().then(res => {
      this.notLoading = true;
      if(res[Keys.DONE]){
        this.historyItems = res[Keys.DATA]['actions'];
        //console.log(this.historyItems)
        this.empty = res[Keys.EMPTY];
      }
      else this.error = true;
    });
  }

  loginStatus(): void{
    this.api.getLoginStatus().then(res => {
      if(res == true){
        this.userCookie['token_key'] = localStorage.getItem("token_key");
        this.userCookie['username'] = localStorage.getItem("username");
        this.api.changeUserdata(this.userCookie);
      }
      else{
        this.api.removeItems();
        this.userCookie = {};
        this.api.changeUserdata(this.userCookie);
        this.router.navigate([constants.notLoggedRedirect]);
      }
      
      
  }).catch(err => {
    this.api.removeItems();
    this.userCookie = {};
    this.api.changeUserdata(this.userCookie);
    this.router.navigate([constants.notLoggedRedirect]);
  });
  }

  observeFromService(): void{
    this.api.loginChanged.subscribe(logged => {
      /* console.log("logged");
      console.log(logged); */
    });
    this.api.userChanged.subscribe(userdata => {
      /* console.log("userdata");
      console.log(userdata); */
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

}
