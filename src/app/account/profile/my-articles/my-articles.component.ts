import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import * as constants from 'src/constants/constants';
import * as messages from 'src/messages/messages';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import { Article } from 'src/app/models/article.model';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import { Observable } from 'rxjs';
import DeleteArticleInterface from 'src/interfaces/requests/article/deletearticle.interface';
import DeleteArticle from 'src/classes/requests/article/deletearticle';
import { Messages } from 'src/constants/messages';
import { THIS_EXPR } from '@angular/compiler/src/output/output_ast';
import GetArticlesInterface from 'src/interfaces/requests/article/getarticles.interface';
import GetArticles from 'src/classes/requests/article/getarticles';
import { Config } from 'config';
import { messageDialog } from 'src/functions/functions';
import { Keys } from 'src/constants/keys';

@Component({
  selector: 'app-my-articles',
  templateUrl: './my-articles.component.html',
  styleUrls: ['./my-articles.component.scss']
})
export class MyArticlesComponent implements OnInit {

  backlink: string = "../";
  userCookie: any = {};
  articles: Article[] = [];
  deleteArticle_url: string = constants.articleDeleteUrl;
  editArticle_url: string = constants.articleEditUrl;
  getArticles_url: string = constants.myArticlesUrl;
  blog_url: string = Config.ANGULAR_MAIN_URL+constants.blogUrl;
  message: string|null = null;
  done: boolean = false; //True if request has returned article list
  spinnerShow: number = -1; //Spinner to show specifying the position whe delete button click occurs
  title: string = "I miei articoli";

  constructor(public http: HttpClient, public api: ApiService,private router: Router) {
    this.loginStatus();
    this.observeFromService();
   }

  ngOnInit(): void {
  }

  loginStatus(): void{
    this.api.getLoginStatus().then(res => {
      //Check if user is logged
      if(res == true){
        this.userCookie['token_key'] = localStorage.getItem("token_key");
        this.userCookie['username'] = localStorage.getItem("username");
        this.api.changeUserdata(this.userCookie);
        this.getArticles();
      }//if(res == true){
      else{
        this.api.removeItems();
        this.userCookie = {};
        this.api.changeUserdata(this.userCookie);
        this.router.navigateByUrl(constants.notLoggedRedirect);
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

  public deleteArticle(data: object): void{
    let cd_data: ConfirmDialogInterface = {
      title: 'Rimuovi articolo',
      message: messages.deleteArticleConfirm
    };
    let cd: ConfirmDialog = new ConfirmDialog(cd_data);
    cd.bt_yes.addEventListener('click', ()=>{
      cd.instance.dispose();
      document.body.removeChild(cd.div_dialog);
      document.body.style.overflow = 'auto';
      let da_data: DeleteArticleInterface = {
        article_id: data['article_id'],
        http: this.http,
        token_key: this.userCookie['token_key'],
        url: this.deleteArticle_url
      };
      let da: DeleteArticle = new DeleteArticle(da_data);
      this.spinnerShow = data['article_pos'];
      da.deleteArticle().then(obj => {
        this.spinnerShow = -1;
        if(obj[Keys.EXPIRED] == true){
          //Session expired
          this.api.removeItems();
          this.userCookie = {};
          this.api.changeUserdata(this.userCookie);
        }
        let md_data: MessageDialogInterface = {
          title: 'Rimuovi articolo',
          message: obj[Keys.MESSAGE]
        };
        let md: MessageDialog = new MessageDialog(md_data);
        md.bt_ok.addEventListener('click',()=>{
          md.instance.dispose();
          md.div_dialog.remove();
          document.body.style.overflow = 'auto';
          if(obj[Keys.DONE] == true)
            this.articles = this.articles.filter((article)=> article.id != da.article_id) 
        });
      }).catch(err => {
        this.spinnerShow = -1;
        let md_data: MessageDialogInterface = {
          title: 'Rimuovi articolo',
          message: Messages.DELETEARTICLE_ERROR
        };
        messageDialog(md_data);
      })
    });
    cd.bt_no.addEventListener('click',()=>{
      cd.instance.dispose();
      document.body.removeChild(cd.div_dialog);
      document.body.style.overflow = 'auto';
    });
  }

  //Get all user articles
  private getArticles(): void{
    const ga_data: GetArticlesInterface = {
      http: this.http,
      token_key: this.userCookie['token_key'],
      url: this.getArticles_url
    };
    let ga: GetArticles = new GetArticles(ga_data);
    ga.getArticles().then(obj => {
      if(obj[Keys.DONE] == true){
        this.done = true;
        this.message = null;
        this.articles = obj['articles'] as Array<Article>;
        //this.insertArticles(this.router);
      }//if(obj[Keys.DONE] == true){
      else{
        this.done = false;
        this.message = obj[Keys.MESSAGE];
      }        
    }).catch(err => {
      //console.warn(err);
      this.done = false;
      this.message = Messages.ARTICLESVIEW_ERROR;
    });
  }

}
