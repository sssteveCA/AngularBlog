import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, ParamMap, Router } from '@angular/router';
import * as constants from 'src/constants/constants';
import * as messages from 'src/messages/messages';
import { ApiService } from 'src/app/api.service';
import { Api2Service } from 'src/app/api2.service';
import { Article } from 'src/app/models/article.model';
import ConfirmDialog from 'src/classes/confirmdialog';
import ConfirmDialogInterface from 'src/classes/confirmdialog.interface';
import MessageDialogInterface from 'src/classes/messagedialog.interface';
import MessageDialog from 'src/classes/messagedialog';

@Component({
  selector: 'app-edit-article',
  templateUrl: './edit-article.component.html',
  styleUrls: ['./edit-article.component.scss']
})
export class EditArticleComponent implements OnInit {

  article: Article = new Article();
  form: FormGroup;
  authorized: boolean = false; //true if user can edit the founded article
  message: string = "";
  userCookie: any = {};

  constructor(
    public http: HttpClient, public fb: FormBuilder, public api: ApiService, private router: Router, public route: ActivatedRoute, api2: Api2Service) {
    this.observeFromService();
    this.api.getLoginStatus().then(res => {
      //Check if user is logged
      if(res == true){
        this.userCookie['token_key'] = localStorage.getItem("token_key");
        this.userCookie['username'] = localStorage.getItem("username");
        this.api.changeUserdata(this.userCookie);
      }//if(res == true){
      else{
        this.api.removeItems();
        this.userCookie = {};
        this.api.changeUserdata(this.userCookie);
        this.router.navigateByUrl(constants.notLoggedRedirect)
      }
    });
    this.form = fb.group({
      'title': ['',Validators.required],
      'introtext': ['',Validators.required],
      'content': ['',Validators.required],
      'permalink': ['',Validators.required],
      'categories': ['',Validators.pattern('^[a-zA-Z0-9,]*$')],
      'tags': ['',Validators.pattern('^[a-zA-Z0-9,]*$')]
    });
    this.route.paramMap.subscribe((params: ParamMap) => {
      let id = params.get('articleId');
      console.log("id => ");
      console.log(id);
      if(typeof id !== 'undefined' && id != null){
        this.article.id = id;
        this.getArticleInfo(this.article.id,api2);
      }
    });
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

  ngOnInit(): void {
  }

  //Get article info and put in inputs
  getArticleInfo(id: string,api2: Api2Service): void{
    api2.isAuthorizedArticle(this.article.id).then(res => {
      console.log("EditArticleComponent isAuthorized article =>");
      console.log(res);
      //Check if user is authorized to edit this article
      this.authorized = res['authorized'];
      this.message = res['msg'];
      if(this.authorized == true){
        this.article.title = res['article']['title'];
        this.article.introtext = res['article']['introtext'];
        this.article.content = res['article']['content'];
        this.article.permalink = res['article']['permalink'];
        this.article.categories = res['article']['categories'];
        this.article.tags = res['article']['tags'];
        this.setFields();
      }
      console.log(this.message);
    }).catch(err => {

    });
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
      console.log(this.article);
      this.article.title = this.form.controls['title'].value;
      this.article.introtext = this.form.controls['introtext'].value;
      this.article.introtext = this.form.controls['introtext'].value;
      this.article.content = this.form.controls['content'].value;
      this.article.permalink = this.form.controls['permalink'].value;
      this.article.categories = this.form.controls['categories'].value;
      this.article.tags = this.form.controls['tags'].value;
      this.editPromise(this.article).then(res => {
        console.log(res);
        let rJson = JSON.parse(res);
        if(rJson['expired'] == true){
          //Session expired
          this.api.removeItems();
          this.userCookie = {};
          this.api.changeUserdata(this.userCookie);
        }
        const data: MessageDialogInterface = {
          title: 'Modifica articolo',
          message: rJson['msg']
        };
        let cd = new MessageDialog(data);
        cd.bt_ok.addEventListener('click', ()=>{
          cd.instance.dispose();
          cd.div_dialog.remove();
          document.body.style.overflow = 'auto';
        }); 
      }).catch(err => {
        console.warn(err);
      });
    });
    cd.bt_no.addEventListener('click',()=>{
      cd.instance.dispose();
      document.body.removeChild(cd.div_dialog);
    });
  }

  //Edit article HTTP request
  async editPromise(article: Article): Promise<any>{
    return await new Promise((resolve,reject)=>{
      const data = {
        token_key: this.userCookie['token_key'],
        article: article
      };
      const headers = new HttpHeaders({
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      });
      this.http.put(constants.articleEditScriptUrl,data,{headers: headers,responseType: 'text'}).subscribe(res =>{
        resolve(res);
      },error => {
        reject(error);
      })
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
