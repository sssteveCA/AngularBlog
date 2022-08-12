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
      'categories': ['',Validators.pattern('^[a-zA-Z0-9,]*$')],
      'tags': ['',Validators.pattern('^[a-zA-Z0-9,]*$')]
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
    console.log(this.article);
    const data = {
      token_key: this.userCookie['token_key'],
      article: this.article
    }
    if(this.form.valid){
      //All form input fields validated
      const headers = new HttpHeaders().set('Content-Type','application/json').set('Accept','application/json');
      this.http.post(constants.articleCreateUrl,data,{headers: headers,responseType: 'text'}).subscribe(res => {
        //Send data in JSON format
        console.log("Create.php response => ");
        console.log(res);
        let rJson = JSON.parse(res);
        if(rJson['expired'] == true){
          //session expired
          this.api.removeItems();
          this.userCookie = {};
          this.api.changeUserdata(this.userCookie);
          //this.router.navigateByUrl(constants.notLoggedRedirect);
        }
        const data: MessageDialogInterface = {
          title: 'Creazione articolo',
          message: rJson['msg']
        };
        let md = new MessageDialog(data);
        md.bt_ok.addEventListener('click',()=>{
          md.instance.dispose();
          md.div_dialog.remove();
          document.body.style.overflow = 'auto';
        });
        
      },error => {
        console.warn(error);
      });
    }
    else{
      /* let invalid = [];
      const controls = this.form.controls;
      for(const name in controls){
        if(controls[name].invalid){
          invalid.push(name);
        }
      } 
      console.log(invalid);*/
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

}
