import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, ParamMap, Router } from '@angular/router';
import * as constants from 'src/constants/constants';
import * as messages from 'src/messages/messages';
import { Api2Service } from 'src/app/api2.service';
import { Article } from 'src/app/models/article.model';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import UpdateArticleInterface from 'src/interfaces/requests/article/updatearticle.interface';
import UpdateArticle from 'src/classes/requests/article/updatearticle';
import { Messages } from 'src/constants/messages';
import { Keys } from 'src/constants/keys';
import { LogindataService } from 'src/app/services/logindata.service';
import { UserCookie } from 'src/constants/types';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-edit-article',
  templateUrl: './edit-article.component.html',
  styleUrls: ['./edit-article.component.scss']
})
export class EditArticleComponent implements OnInit, OnDestroy {

  article: Article = new Article();
  backlink: string = "../../";
  form: FormGroup;
  authorized: boolean = false; //true if user can edit the founded article
  message: string = "";
  showSpinner: boolean = false;
  showStartSpinner: boolean = true;
  spinnerId: string = "edit-article-spinner"
  spinnerIdStart: string = "edit-article-start-spinner"
  title: string = "Modifica articolo";
  updateArticle_url: string = constants.articleEditScriptUrl;
  cookie: UserCookie = {};
  subscription: Subscription;

  constructor(
    public http: HttpClient, public fb: FormBuilder, private router: Router, public route: ActivatedRoute, private api2: Api2Service, private loginData: LogindataService) {
    /* this.loginStatus();
    this.observeFromService(); */
    this.formBuild();
   }

   ngOnDestroy(): void {
    if(this.subscription != null) this.subscription.unsubscribe();
  }

  ngOnInit(): void {
    this.loginDataObserver()
    this.editArticleParams();
  }

   editArticleParams(): void{
    this.subscription = this.route.paramMap.subscribe((params: ParamMap) => {
      let id = params.get('articleId');
      if(typeof id !== 'undefined' && id != null){
        this.article.id = id;
        this.getArticleInfo(this.article.id,this.api2);
      }
    });
   }

   formBuild(): void{
    this.form = this.fb.group({
      'title': ['',Validators.required],
      'introtext': ['',Validators.required],
      'content': ['',Validators.required],
      'permalink': ['',Validators.required],
      'categories': ['',Validators.pattern('^[a-zA-Z0-9,]*$')],
      'tags': ['',Validators.pattern('^[a-zA-Z0-9,]*$')]
    });
   }

  

  //Get article info and put in inputs
  getArticleInfo(id: string,api2: Api2Service): void{
    api2.isAuthorizedArticle(this.article.id).then(res => {
      this.showStartSpinner = false;
      //Check if user is authorized to edit this article
      this.authorized = res['authorized'];
      this.message = res[Keys.MESSAGE];
      if(this.authorized == true){
        this.article.title = res['article']['title'];
        this.article.introtext = res['article']['introtext'];
        this.article.content = res['article']['content'];
        this.article.permalink = res['article']['permalink'];
        this.article.categories = res['article']['categories'];
        this.article.tags = res['article']['tags'];
        this.setFields();
      }
    }).catch(err => {

    });
  }

  loginDataObserver(): void{
    this.subscription = this.loginData.loginDataObservable.subscribe(loginData => {
      if(!(loginData.userCookie && loginData.userCookie.token_key != null && loginData.userCookie.username != null)){
        this.loginData.removeItems();
        if(loginData.logout && loginData.logout == true)
          this.router.navigateByUrl(constants.homeUrl)
        else
          this.router.navigateByUrl(constants.notLoggedRedirect)
      }
    })
  }

  edit(): void{
    let data: ConfirmDialogInterface = {
      title: 'Modifica articolo',
      message: messages.editArticleConfirm
    };
    let cd = new ConfirmDialog(data);
    cd.bt_yes.addEventListener('click',()=>{
      cd.instance.dispose();
      document.body.removeChild(cd.div_dialog);
      this.article.title = this.form.controls['title'].value;
      this.article.introtext = this.form.controls['introtext'].value;
      this.article.introtext = this.form.controls['introtext'].value;
      this.article.content = this.form.controls['content'].value;
      this.article.permalink = this.form.controls['permalink'].value;
      this.article.categories = this.form.controls['categories'].value;
      this.article.tags = this.form.controls['tags'].value;
      const ua_data: UpdateArticleInterface = {
        article: this.article,
        http: this.http,
        token_key: localStorage.getItem("token_key") as string,
        url: this.updateArticle_url
      };
      this.showSpinner = true;
      let ua: UpdateArticle = new UpdateArticle(ua_data);
      ua.updateArticle().then(obj => {
        this.showSpinner = false;
        const data: MessageDialogInterface = {
          title: 'Modifica articolo',
          message: obj[Keys.MESSAGE]
        };
        let cd = new MessageDialog(data);
        cd.bt_ok.addEventListener('click', ()=>{
          cd.instance.dispose();
          cd.div_dialog.remove();
          document.body.style.overflow = 'auto';
          if(obj[Keys.EXPIRED] == true){
            //Session expired
            this.loginData.changeLoginData({
              logout: false, userCookie: {}
            })
          }
        }); 
      }).catch(err => {
        const data: MessageDialogInterface = {
          title: 'Modifica articolo',
          message: Messages.ARTICLEUPDATE_ERROR
        };
        let cd = new MessageDialog(data);
        cd.bt_ok.addEventListener('click', ()=>{
          cd.instance.dispose();
          cd.div_dialog.remove();
          document.body.style.overflow = 'auto';
        }); 
      });
    });
    cd.bt_no.addEventListener('click',()=>{
      cd.instance.dispose();
      document.body.removeChild(cd.div_dialog);
    });
  }

  //insert article data to proper input fields
  setFields(): void{
    this.form.controls['title'].setValue(this.article.title);
    this.form.controls['introtext'].setValue(this.article.introtext);
    this.form.controls['content'].setValue(this.article.content);
    this.form.controls['permalink'].setValue(this.article.permalink);
    this.form.controls['categories'].setValue(this.article.categories);
    this.form.controls['tags'].setValue(this.article.tags);
  }
}
