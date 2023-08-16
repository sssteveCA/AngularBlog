import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component, ElementRef, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import * as constants from 'src/constants/constants';
import * as messages from 'src/messages/messages';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import { Article } from 'src/app/models/article.model';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import { Observable, Subscription } from 'rxjs';
import DeleteArticleInterface from 'src/interfaces/requests/article/deletearticle.interface';
import DeleteArticle from 'src/classes/requests/article/deletearticle';
import { Messages } from 'src/constants/messages';
import { THIS_EXPR } from '@angular/compiler/src/output/output_ast';
import GetArticlesInterface from 'src/interfaces/requests/article/getarticles.interface';
import GetArticles from 'src/classes/requests/article/getarticles';
import { Config } from 'config';
import { messageDialog } from 'src/functions/functions';
import { Keys } from 'src/constants/keys';
import { LogindataService } from 'src/app/services/logindata.service';
import { UserCookie } from 'src/constants/types';

@Component({
  selector: 'app-my-articles',
  templateUrl: './my-articles.component.html',
  styleUrls: ['./my-articles.component.scss']
})
export class MyArticlesComponent implements OnInit, OnDestroy {

  backlink: string = "../";
  cookie: UserCookie = {};
  articles: Article[] = [];
  deleteArticle_url: string = constants.articleDeleteUrl;
  editArticle_url: string = constants.articleEditUrl;
  getArticles_url: string = constants.myArticlesUrl;
  blog_url: string = constants.blogUrl;
  message: string|null = null;
  messageSecondary: string = "Nessun articolo da mostrare";
  done: boolean = false; //True if request has returned article list
  showStartSpinner: boolean = true;
  spinnerStartId: string = "my-artcles-start-spinner"
  spinnerShow: number = -1; //Spinner to show specifying the position whe delete button click occurs
  title: string = "I miei articoli";
  subscription: Subscription;

  constructor(public http: HttpClient,private router: Router, private loginData: LogindataService) {
   }

   ngOnDestroy(): void {
    if(this.subscription != null) this.subscription.unsubscribe();
  }

  ngOnInit(): void {
    this.loginDataObserver()
    this.getArticles();
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
        token_key: localStorage.getItem('token_key') as string,
        url: this.deleteArticle_url
      };
      let da: DeleteArticle = new DeleteArticle(da_data);
      this.spinnerShow = data['article_pos'];
      da.deleteArticle().then(obj => {
        this.spinnerShow = -1;
        if(obj[Keys.EXPIRED] == true){
          //Session expired
          this.loginData.removeItems();
          this.loginData.changeUserCookieData({});
          this.router.navigateByUrl(constants.notLoggedRedirect);
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
      token_key: localStorage.getItem("token_key") as string,
      url: this.getArticles_url
    };
    let ga: GetArticles = new GetArticles(ga_data);
    ga.getArticles().then(obj => {
      this.showStartSpinner = false;
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

  loginDataObserver(): void{
    this.subscription = this.loginData.loginDataObservable.subscribe(loginData => {
      if(!(loginData.userCookie && loginData.userCookie.token_key != null && loginData.userCookie.username != null)){
        if(loginData.logout && loginData.logout == true)
          this.router.navigateByUrl(constants.homeUrl)
        else
          this.router.navigateByUrl(constants.notLoggedRedirect)
      }
    })
  }

}
