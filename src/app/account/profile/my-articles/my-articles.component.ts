import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import * as constants from 'src/constants/constants';
import * as messages from 'src/messages/messages';
import ConfirmDialog from 'src/classes/confirmdialog';
import MessageDialog from 'src/classes/messagedialog';
import { Article } from 'src/app/models/article.model';
import ConfirmDialogInterface from 'src/classes/confirmdialog.interface';
import MessageDialogInterface from 'src/classes/messagedialog.interface';
import { Observable } from 'rxjs';

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
            this.insertArticles(this.router);
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

  //Delete an article
  private delete(deleteData: any): void{
    console.log("delete => ");
    if(deleteData.hasOwnProperty('article_id') && deleteData.hasOwnProperty('token_key')){
      console.log(deleteData);
      //Required properties exist
      this.deletePromise(deleteData).then(res =>{
        console.log(res);
        let rJson = JSON.parse(res);
        let data: MessageDialogInterface = {
          title: 'Rimuovi articolo',
          message: rJson['msg']
        };
        let md: MessageDialog = new MessageDialog(data);
        md.bt_ok.addEventListener('click',()=>{
          md.instance.dispose();
          md.div_dialog.remove();
        });

      }).catch(err =>{
        console.warn(err);
      });
    }//if(deleteData.hasOwnProperty('id') && deleteData.hasOwnProperty('token_key')){
    else{
      let data: MessageDialogInterface = {
        title: 'Rimuovi articolo',
        message: messages.deleteArticleError
      };
      let md: MessageDialog = new MessageDialog(data);
      md.bt_ok.addEventListener('click',()=>{
        md.instance.dispose();
        md.div_dialog.remove();
      });
    }
  }

  //Function that does the HTTP request to delete the article
  private async deletePromise(deleteData: any): Promise<any>{
    return await new Promise((resolve, reject) => {
      const headers = new HttpHeaders({
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      });
      this.http.post(constants.articleDeleteUrl,deleteData,{headers: headers,responseType: 'text'}).subscribe(res => {
          resolve(res);
        },err => {
          reject(err);
        }
      );
    });
  }


  //Insert articles list in DOM
  private insertArticles(router: Router): void{
    const component = this;
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
              'role': 'button'
            });
            btnEdit.html('MODIFICA');
            btnEdit.on('click',function(e){
              e.preventDefault();
              router.navigate([constants.articleEditUrl,article.id]);
            });
          divButtons.append(btnEdit);
            let btnDel = $('<button>');
            btnDel.addClass('btn btn-danger');
            btnDel.attr('type','button');
            btnDel.html('ELIMINA');
            btnDel.on('click', function(e){
              let data: ConfirmDialogInterface = {
                title: 'Rimuovi articolo',
                message: messages.deleteArticleConfirm
              };
              let cd: ConfirmDialog = new ConfirmDialog(data);
              cd.bt_yes.addEventListener('click',()=>{
                cd.instance.dispose();
                document.body.removeChild(cd.div_dialog);
                let deleteData = {
                  'article_id': article.id,
                  'token_key': localStorage.getItem('token_key')
                };
                console.log(deleteData); 
                component.delete(deleteData);
              });
              cd.bt_no.addEventListener('click',()=>{
                cd.instance.dispose();
                document.body.removeChild(cd.div_dialog);
              });
            });//btnDel.on('click', function(e){
          divButtons.append(btnDel);
        divArticle.append(divButtons);
      container.append(divArticle);
    });
  }

}
