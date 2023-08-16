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
    this.loginDataObserver()
    this.loginData.changeLoginData({
      userCookie: {
        token_key: localStorage.getItem('token_key'), username: localStorage.getItem('username')
      }
    })
  }


  loginDataObserver(): void{
    this.subscription = this.loginData.loginDataObservable.subscribe(loginData => {
      if(loginData.userCookie && loginData.userCookie.token_key != null && loginData.userCookie.username != null){
        this.cookie.token_key = loginData.userCookie.token_key;
        this.cookie.username = loginData.userCookie.username;
      }
      else{
        this.loginData.removeItems();
        if(loginData.logout && loginData.logout == true)
          this.router.navigateByUrl(constants.homeUrl)
        else
          this.router.navigateByUrl(constants.notLoggedRedirect)
      }
    })

  }

}
