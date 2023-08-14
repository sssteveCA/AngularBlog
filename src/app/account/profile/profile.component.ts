import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { UserCookie } from 'src/constants/types';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.scss']
})
export class ProfileComponent implements OnInit {

  cookie: UserCookie = {};
  menuItems: object[] = [
    {title: "Il mio account", text: "Personalizza le informazioni del tuo profilo", link: "info" },
    {title: "I miei articoli", text: "Gestisci gli articoli che hai postato", link: "myArticles"},
    {title: "Crea un nuovo articolo", text: "Crea e pubblica un nuovo articolo", link: "myArticles/create"},
    {title: "Cronologia azioni effettuate", text: "Visualizza o rimuovi tutte le azioni effettuate con questo account", link: "history"}
  ]

  constructor(private http:HttpClient, private router: Router) {
   }

  ngOnInit(): void {
     this.cookie = {
      token_key: localStorage.getItem('token_key'), username: localStorage.getItem('username')
    }
  }

}
