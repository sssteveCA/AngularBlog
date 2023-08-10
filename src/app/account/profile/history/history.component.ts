import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import { HistoryItem } from 'src/app/models/historyitem.model';
import History from 'src/classes/requests/profile/gethistory';
import { Keys } from 'src/constants/keys';
import HistoryInterface from 'src/interfaces/requests/profile/gethistory.interface';
import * as constants from '../../../../constants/constants';
import GetHistory from 'src/classes/requests/profile/gethistory';
import GetHistoryInterface from 'src/interfaces/requests/profile/gethistory.interface';
import DeleteHistoryItemInterface from 'src/interfaces/requests/profile/deletehistoryitem.interface';
import DeleteHistoryItem from 'src/classes/requests/profile/deletehistoryitem';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import MessageDialog from 'src/classes/dialogs/messagedialog';

@Component({
  selector: 'app-history',
  templateUrl: './history.component.html',
  styleUrls: ['./history.component.scss']
})
export class HistoryComponent implements OnInit {

  backlink: string = "../";
  title: string = "Cronologia azioni effettuate";
  urlDeleteHistoryItem: string = constants.profileDeleteHistoryItemUrl;
  urlGetHistory: string = constants.profileGetHistoryUrl;
  userCookie: any = {};
  historyItems: HistoryItem[] = [];
  empty: boolean = false;
  error: boolean = false;
  notLoading: boolean = false;
  spinnerId: string = "history-spinner"

  constructor(private api: ApiService, private http: HttpClient, private router: Router) {
    this.loginStatus(); 
    this.observeFromService();
    this.getHistory();
   }

  ngOnInit(): void {
  }

  getHistory(): void{
    let historyData: GetHistoryInterface = {
      http: this.http,
      token_key: localStorage.getItem('token_key') as string,
      url: this.urlGetHistory
    }
    let history: GetHistory = new GetHistory(historyData);
    history.history().then(res => {
      this.notLoading = true;
      if(res[Keys.DONE]){
        this.historyItems = res[Keys.DATA]['actions'];
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
    });
    this.api.userChanged.subscribe(userdata => {
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

  onActionIdReceived(action_id: string): void{
    let dhiData: DeleteHistoryItemInterface = {
      action_id: action_id,
      http: this.http,
      token_key: this.userCookie['token_key'],
      url: this.urlDeleteHistoryItem
    }
    let dhi: DeleteHistoryItem = new DeleteHistoryItem(dhiData)
    dhi.delete().then(obj => {
      let mdData: MessageDialogInterface = {
        title: 'Rimuovi azione',
        message: obj[Keys.MESSAGE]
      }
      let md: MessageDialog = new MessageDialog(mdData)
      md.bt_ok.addEventListener('click',()=>{
        md.instance.dispose();
        document.body.removeChild(md.div_dialog)
        document.body.style.overflow = 'auto'
        if(obj[Keys.DONE]){
          this.historyItems = this.historyItems.filter(item => item.id != action_id)
        }
     })
    });
  }

}
