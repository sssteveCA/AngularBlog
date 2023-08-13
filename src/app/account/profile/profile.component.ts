import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import * as constants from '../../../constants/constants';
import { UserCookie } from 'src/constants/types';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.scss']
})
export class ProfileComponent implements OnInit {

  cookie: UserCookie = {};
  userCookie: any = {};
  menuItems: object[] = [
    {title: "Il mio account", text: "Personalizza le informazioni del tuo profilo", link: "info" },
    {title: "I miei articoli", text: "Gestisci gli articoli che hai postato", link: "myArticles"},
    {title: "Crea un nuovo articolo", text: "Crea e pubblica un nuovo articolo", link: "myArticles/create"},
    {title: "Cronologia azioni effettuate", text: "Visualizza o rimuovi tutte le azioni effettuate con questo account", link: "history"}
  ]

  constructor(private http:HttpClient, private api: ApiService, private router: Router) {
    //this.loginStatus(); 
    //this.observeFromService();
    this.cookie = {
      token_key: localStorage.getItem('token_key'), username: localStorage.getItem('username')
    }
   }

  ngOnInit(): void {
  }

  loginStatus(): void{
    this.api.getLoginStatus().then(res => {
      if(res == true){
        this.userCookie['token_key'] = localStorage.getItem("token_key");
        this.userCookie['username'] = localStorage.getItem("username");
        this.api.changeUserdata(this.userCookie);
        this.http.get(constants.profileUrl,{responseType: 'text'}).subscribe(res => {
          let rJson = JSON.parse(res);
        });
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

}
