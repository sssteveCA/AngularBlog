import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Article } from 'src/app/models/article.model';
import { ApiService } from 'src/app/api.service';
import * as functions from 'src/functions/functions';
import * as constants from 'src/constants/constants';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-new-article',
  templateUrl: './new-article.component.html',
  styleUrls: ['./new-article.component.scss']
})
export class NewArticleComponent implements OnInit {

  article: Article = new Article();
  form: FormGroup;
  userCookie: any = {};

  constructor(public http: HttpClient, public fb: FormBuilder, public api: ApiService, private router: Router) {
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
        this.router.navigate([constants.notLoggedRedirect]);
      }
    }).catch(err => {
      this.api.removeItems();
        this.userCookie = {};
        this.api.changeUserdata(this.userCookie);
        this.router.navigate([constants.notLoggedRedirect]);
    });
    this.form = fb.group({
      'title': ['',Validators.required],
      'introtext': ['',Validators.required],
      'content': ['',Validators.required],
      'permalink': ['',Validators.required],
      'categories': ['',Validators.required],
      'tags': ['',Validators.required]
    });
   }

  ngOnInit(): void {
  }

  create(): void{
    this.article.title = this.form.controls['title'].value;
    this.article.introtext = this.form.controls['introtext'].value;
    this.article.content = this.form.controls['content'].value;
    this.article.permalink = this.form.controls['permalink'].value;
    this.article.categories = this.form.controls['categories'].value;
    this.article.tags = this.form.controls['tags'].value;
    if(this.form.valid){
      //All form input fields validated
    }
    else{
      functions.dialogMessage($,'Creazione articolo','Uno o più valori del form non sono validi');
    }
  }

}
