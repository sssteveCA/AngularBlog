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

@Component({
  selector: 'app-my-articles',
  templateUrl: './my-articles.component.html',
  styleUrls: ['./my-articles.component.scss']
})
export class MyArticlesComponent implements OnInit {

  userCookie: any = {};
  articles: Article[] = new Array();
  deleteArticle_url: string = constants.articleDeleteUrl;
  getArticles_url: string = constants.myArticlesUrl;
  blog_url: string = constants.homeUrl+constants.blogUrl;
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
      console.log("logged");
      //console.log(logged);
    });
    this.api.userChanged.subscribe(userdata => {
      console.log("userdata");
      //console.log(userdata);
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

  //Delete an article
  private delete(deleteData: any): void{
    const router = this.router;
    //console.log("delete => ");
    if(deleteData.hasOwnProperty('article_id') && deleteData.hasOwnProperty('token_key')){
      //console.log(deleteData);
      //Required properties exist
      this.deletePromise(deleteData).then(res =>{
        //console.log(res);
        let rJson = JSON.parse(res);
        if(rJson['expired'] == true){
          //Session expired
          this.api.removeItems();
          this.userCookie = {};
          this.api.changeUserdata(this.userCookie);
        }
        let data: MessageDialogInterface = {
          title: 'Rimuovi articolo',
          message: rJson['msg']
        };
        let md: MessageDialog = new MessageDialog(data);
        md.bt_ok.addEventListener('click',()=>{
          md.instance.dispose();
          md.div_dialog.remove();
          if(rJson['done'] == true)
            this.getArticles();
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

  public deleteArticle(event): void{
    //Get the delete button when click occurred
    let click_button: JQuery = $(event.target);
    //Get the article id of element in the same div
    let article_id: string = click_button.siblings('.article_id').val() as string;
    console.log(this.userCookie);
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
      da.deleteArticle().then(obj => {
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
        let md_data: MessageDialogInterface = {
          title: 'Rimuovi articolo',
          message: Messages.DELETEARTICLE_ERROR
        };
        this.dialogMessage(md_data);
      })
    });
    cd.bt_no.addEventListener('click',()=>{
      cd.instance.dispose();
      document.body.removeChild(cd.div_dialog);
      document.body.style.overflow = 'auto';
    });
  }

  private dialogMessage(md_data: MessageDialogInterface){
    let md: MessageDialog = new MessageDialog(md_data);
    md.bt_ok.addEventListener('click',()=>{
      md.instance.dispose();
      md.div_dialog.remove();
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
      this.done = false;
      this.message = Messages.ARTICLESVIEW_ERROR;
    });
  }


  //Insert articles list in DOM
  private insertArticles(router: Router): void{
    const component = this;
    let container = $('#articles-list');
    container.html('');
    /* console.log("Container before foreach => ");
    console.log($(container)); */
    this.articles.forEach((article) => {
        //console.log(article);
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
                document.body.style.overflow = 'auto';
                let deleteData = {
                  'article_id': article.id,
                  'token_key': localStorage.getItem('token_key')
                };
                //console.log(deleteData); 
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
