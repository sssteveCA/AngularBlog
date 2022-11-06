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

@Component({
  selector: 'app-my-articles',
  templateUrl: './my-articles.component.html',
  styleUrls: ['./my-articles.component.scss']
})
export class MyArticlesComponent implements OnInit {

  userCookie: any = {};
  articles: Article[] = new Array();
  deleteArticle_url: string = constants.articleDeleteUrl;
  editArticle_url: string = constants.articleEditUrl;
  getArticles_url: string = constants.myArticlesUrl;
  blog_url: string = Config.ANGULAR_MAIN_URL+constants.blogUrl;
  message: string|null = null;
  done: boolean = false; //True if request has returned article list
  spinnerShow: number = -1; //Spinner to show specifying the position whe delete button click occurs

  constructor(public http: HttpClient, public api: ApiService,private router: Router) {
    this.observeFromService();
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

  ngOnInit(): void {
  }

  observeFromService(): void{
    this.api.loginChanged.subscribe(logged => {
      //console.log("logged");
      //console.log(logged);
    });
    this.api.userChanged.subscribe(userdata => {
      //console.log("userdata");
      //console.log(userdata);
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

  public deleteArticle(event): void{
    //Get the delete button when click occurred
    let click_button: JQuery = $(event.target);
    //Get the article id of element in the same div
    let article_id: string = click_button.parents('.article-buttons').children('input[name=article_id]').val() as string;
    let article_pos: number = click_button.siblings('input[name=article_pos]').val() as number;
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
        article_id: article_id,
        http: this.http,
        token_key: this.userCookie['token_key'],
        url: this.deleteArticle_url
      };
      let da: DeleteArticle = new DeleteArticle(da_data);
      this.spinnerShow = article_pos;
      da.deleteArticle().then(obj => {
        this.spinnerShow = -1;
        if(obj['expired'] == true){
          //Session expired
          this.api.removeItems();
          this.userCookie = {};
          this.api.changeUserdata(this.userCookie);
        }
        let md_data: MessageDialogInterface = {
          title: 'Rimuovi articolo',
          message: obj['msg']
        };
        let md: MessageDialog = new MessageDialog(md_data);
        md.bt_ok.addEventListener('click',()=>{
          md.instance.dispose();
          md.div_dialog.remove();
          document.body.style.overflow = 'auto';
          if(obj['done'] == true)
            this.getArticles(); 
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
    /* console.log("myArticles getArticles ga_data");
    console.log(ga_data); */
    let ga: GetArticles = new GetArticles(ga_data);
    ga.getArticles().then(obj => {
      //console.log(obj);
      if(obj['done'] == true){
        this.done = true;
        this.message = null;
        this.articles = obj['articles'] as Array<Article>;
        //this.insertArticles(this.router);
      }//if(obj['done'] == true){
      else{
        this.done = false;
        this.message = obj['msg'];
      }        
    }).catch(err => {
      //console.warn(err);
      this.done = false;
      this.message = Messages.ARTICLESVIEW_ERROR;
    });
  }

}
