import { HttpClient } from '@angular/common/http';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { HistoryItem } from 'src/app/models/historyitem.model';
import { Keys } from 'src/constants/keys';
import * as constants from '../../../../constants/constants';
import GetHistory from 'src/classes/requests/profile/gethistory';
import GetHistoryInterface from 'src/interfaces/requests/profile/gethistory.interface';
import DeleteHistoryItemInterface from 'src/interfaces/requests/profile/deletehistoryitem.interface';
import DeleteHistoryItem from 'src/classes/requests/profile/deletehistoryitem';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import { Subscription } from 'rxjs';
import { LogindataService } from 'src/app/services/logindata.service';
import { UserCookie } from 'src/constants/types';

@Component({
  selector: 'app-history',
  templateUrl: './history.component.html',
  styleUrls: ['./history.component.scss']
})
export class HistoryComponent implements OnInit, OnDestroy {

  backlink: string = "../";
  cookie: UserCookie = {};
  title: string = "Cronologia azioni effettuate";
  urlDeleteHistoryItem: string = constants.profileDeleteHistoryItemUrl;
  urlGetHistory: string = constants.profileGetHistoryUrl;
  userCookie: any = {};
  historyItems: HistoryItem[] = [];
  empty: boolean = false;
  error: boolean = false;
  messageError: string = "Errore durante la lettura delle azioni effettuate";
  messageSecondary: string = "Nessun azione effettuata";
  notLoading: boolean = false;
  spinnerId: string = "history-spinner"
  subscription: Subscription;

  constructor(private http: HttpClient, private router: Router, private loginData: LogindataService) {
    this.getHistory();
   }

   ngOnDestroy(): void {
    if(this.subscription != null) this.subscription.unsubscribe();
  }

  ngOnInit(): void {
    this.loginDataObserver()
    this.loginData.changeLoginData({
      userCookie: {
        token_key: localStorage.getItem('token_key'), username: localStorage.getItem('username')
      }
    })
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

  loginDataObserver(): void{
    this.subscription = this.loginData.loginDataObservable.subscribe(loginData => {
      if(loginData.userCookie && loginData.userCookie.token_key != null && loginData.userCookie.username != null){
        this.cookie.token_key = loginData.userCookie.token_key;
        this.cookie.username = loginData.userCookie.username;
      }
      else{
        if(loginData.logout && loginData.logout == true)
          this.router.navigateByUrl(constants.homeUrl)
        else
          this.router.navigateByUrl(constants.notLoggedRedirect)
      }
    })
  }

  onActionIdReceived(action_id: string): void{
    let dhiData: DeleteHistoryItemInterface = {
      action_id: action_id,
      http: this.http,
      token_key: localStorage.getItem("token_key") as string,
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
