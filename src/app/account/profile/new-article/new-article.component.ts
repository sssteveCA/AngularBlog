import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Article } from 'src/app/models/article.model';
import { ApiService } from 'src/app/api.service';
import * as functions from 'src/functions/functions';
import * as constants from 'src/constants/constants';
import * as messages from 'src/messages/messages';
import { Router } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import AddArticleInterface from 'src/interfaces/requests/article/addarticle.interface';
import AddArticle from 'src/classes/requests/article/addarticle';
import { Messages } from 'src/constants/messages';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import { Keys } from 'src/constants/keys';
import { LogindataService } from 'src/app/services/logindata.service';
import { UserCookie } from 'src/constants/types';

@Component({
  selector: 'app-new-article',
  templateUrl: './new-article.component.html',
  styleUrls: ['./new-article.component.scss']
})
export class NewArticleComponent implements OnInit {

  addArticle_url: string = constants.articleCreateUrl;
  backlink: string = "../../";
  article: Article = new Article();
  form: FormGroup;
  showSpinner: boolean = false;
  spinnerId: string = "new-article-spinner";
  cookie: UserCookie = {}
  userCookie: any = {};
  title: string = "Crea un nuovo articolo";

  constructor(public http: HttpClient, public fb: FormBuilder, public api: ApiService, private router: Router, private loginData: LogindataService) {
    this.formBuild();
   }

  ngOnInit(): void {
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

  create(): void{
    this.article.title = this.form.controls['title'].value;
    this.article.introtext = this.form.controls['introtext'].value;
    this.article.content = this.form.controls['content'].value;
    this.article.permalink = this.form.controls['permalink'].value;
    this.article.categories = this.form.controls['categories'].value;
    this.article.tags = this.form.controls['tags'].value;
    if(this.form.valid){
      let cd_data: ConfirmDialogInterface = {
        title: 'Creazione articolo',
        message: Messages.CREATEARTICLE_CONFIRM
      };
      let cd: ConfirmDialog = new ConfirmDialog(cd_data);
      cd.bt_yes.addEventListener('click', ()=>{
        cd.instance.dispose();
        document.body.removeChild(cd.div_dialog);
        document.body.style.overflow = 'auto';
        this.newArticle();
      });
      cd.bt_no.addEventListener('click',()=>{
        cd.instance.dispose();
        document.body.removeChild(cd.div_dialog);
        document.body.style.overflow = 'auto';
      });
    }
    else{
      const data: MessageDialogInterface = {
        title: 'Creazione articolo',
        message: messages.invalidData
      };
      let md = new MessageDialog(data);
      md.bt_ok.addEventListener('click',()=>{
        md.instance.dispose();
        md.div_dialog.remove();
        document.body.style.overflow = 'auto';
      });
    }
  }

  newArticle(): void{
    const aa_data: AddArticleInterface = {
      article: this.article,
      http: this.http,
      token_key: localStorage.getItem("token_key") as string,
      url: this.addArticle_url
    };
    let aa: AddArticle = new AddArticle(aa_data);
    this.showSpinner = true;
    aa.createArticle().then(obj => {
      this.showSpinner = false;
      if(obj[Keys.EXPIRED] == true){
        //session expired
        this.loginData.removeItems();
        this.loginData.changeUserCookieData({});
        this.api.removeItems();
        this.userCookie = {};
        this.api.changeUserdata(this.userCookie);
        //this.router.navigateByUrl(constants.notLoggedRedirect);
      }
      const md_data: MessageDialogInterface = {
        title: 'Creazione articolo',
        message: obj[Keys.MESSAGE]
      };
      let md = new MessageDialog(md_data);
      md.bt_ok.addEventListener('click',()=>{
        md.instance.dispose();
        md.div_dialog.remove();
        document.body.style.overflow = 'auto';
      });
    }).catch(err => {
      const md_data: MessageDialogInterface = {
        title: 'Creazione articolo',
        message: Messages.ARTICLENEW_ERROR
      };
      let md = new MessageDialog(md_data);
      md.bt_ok.addEventListener('click',()=>{
        md.instance.dispose();
        md.div_dialog.remove();
        document.body.style.overflow = 'auto';
      });
    });
  }

}
