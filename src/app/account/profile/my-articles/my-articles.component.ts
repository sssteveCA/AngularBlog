import { HttpClient } from '@angular/common/http';
import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import * as constants from 'src/constants/constants';
import { Article } from 'src/app/models/article.model';

@Component({
  selector: 'app-my-articles',
  templateUrl: './my-articles.component.html',
  styleUrls: ['./my-articles.component.scss']
})
export class MyArticlesComponent implements OnInit {

  userCookie: any = {};
  articles: Article[] = new Array();
  message: string|null = null;
  done: boolean = false; //True if request has returned article list

  constructor(public http: HttpClient, public api: ApiService,private router: Router) {
    this.observeFromService();
    this.api.getLoginStatus().then(res => {
      //Check if user is logged
      if(res == true){
        this.userCookie['token_key'] = localStorage.getItem("token_key");
        this.userCookie['username'] = localStorage.getItem("username");
        this.api.changeUserdata(this.userCookie);
        this.http.get(constants.myArticlesUrl+'?token_key='+this.userCookie['token_key'],{responseType: 'text'}).subscribe(res => {
          //console.log(res);
          let rJson = JSON.parse(res);
          if(rJson['done'] == true){
            this.done = true;
            this.message = null;
            this.articles = rJson['articles'] as Array<Article>;
            this.insertArticles();
          }//if(rJson['done'] == true){
          else{
            this.done = false;
            this.message = rJson['msg'];
          }        
          //console.log(rJson);
        });
      }//if(res == true){
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

  ngOnInit(): void {
  }

  observeFromService(): void{
    this.api.loginChanged.subscribe(logged => {
      console.log("logged");
      console.log(logged);
    });
    this.api.userChanged.subscribe(userdata => {
      console.log("userdata");
      console.log(userdata);
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

  //Insert articles list in DOM
  private insertArticles(): void{
    let container = $('#articles-list');
    console.log("Container before foreach => ");
    console.log($(container));
    this.articles.forEach((article) => {
        console.log(article);
        let divArticle = $('<div>');
        divArticle.addClass('article');
          let divTitle = $('<div>');  
            divTitle.addClass('article-title');
            let articleLink = $('<a>');
            articleLink.addClass('article-link');
            articleLink.attr({
              'href': constants.homeUrl+constants.blogUrl+'/'+article.permalink,
              'target': '_blank'
            }); 
              let h3 = $('<h3>');
              h3.addClass('article-title');
              h3.html(article.title);
            articleLink.append(h3);
          divTitle.append(articleLink);
        divArticle.append(divTitle);
          let divButtons = $('<div>');
          divButtons.addClass('article-buttons');
            let btnEdit = $('<a>');
            btnEdit.addClass('btn btn-primary');
            btnEdit.attr({
              'href': '#',
              'role': 'button'
            });
            btnEdit.html('MODIFICA');
          divButtons.append(btnEdit);
            let btnDel = $('<button>');
            btnDel.addClass('btn btn-danger');
            btnDel.attr('type','button');
            btnDel.html('ELIMINA');
          divButtons.append(btnDel);
        divArticle.append(divButtons);
      container.append(divArticle);
    });
  }

}
