import { HttpClient } from '@angular/common/http';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { Subscribable, Subscription } from 'rxjs';
import { LogindataService } from 'src/app/services/logindata.service';
import { UserCookie } from 'src/constants/types';
import * as constants from '../../../constants/constants';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.scss']
})
export class ProfileComponent implements OnInit, OnDestroy {

  cookie: UserCookie = {};
  menuItems: object[] = [
    {title: "Il mio account", text: "Personalizza le informazioni del tuo profilo", link: "info" },
    {title: "I miei articoli", text: "Gestisci gli articoli che hai postato", link: "myArticles"},
    {title: "Crea un nuovo articolo", text: "Crea e pubblica un nuovo articolo", link: "myArticles/create"},
    {title: "Cronologia azioni effettuate", text: "Visualizza o rimuovi tutte le azioni effettuate con questo account", link: "history"}
  ]
  subscription: Subscription;

  constructor(private http:HttpClient, private router: Router, private loginData: LogindataService) {
   }


  ngOnDestroy(): void {
    if(this.subscription != null) this.subscription.unsubscribe();
  }

  ngOnInit(): void {
     this.cookie = {
      token_key: localStorage.getItem('token_key'), username: localStorage.getItem('username')
    }
  }


  loginDataObserver(): void{
    this.subscription = this.loginData.userCookieObservable.subscribe(userCookie => {
      if(userCookie && 'token_key' in userCookie && 'username' in userCookie){
        this.cookie.username = userCookie.username;
        this.cookie.token_key = userCookie.token_key;
      }
      else{
        this.router.navigateByUrl(constants.homeUrl);
      }
    })
  }

}
