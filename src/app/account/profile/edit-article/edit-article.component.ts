import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, ParamMap, Router } from '@angular/router';
import * as constants from 'src/constants/constants';
import * as messages from 'src/messages/messages';
import { ApiService } from 'src/app/api.service';
import { Api2Service } from 'src/app/api2.service';
import { Article } from 'src/app/models/article.model';

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
        api2.isAuthorizedArticle(this.article.id).then(res => {
          console.log("EditArticleComponent isAuthorized article =>");
          console.log(res);
          //Check if user is authorized to edit this article
          this.authorized = res['authorized'];
          this.message = res['msg'];
          console.log(this.message);
        }).catch(err => {

        });
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
  getArticleInfo(id: string): void{

  }

  edit(): void{

  }

}
