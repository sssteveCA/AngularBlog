import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AfterViewInit, Component, Input, OnInit } from '@angular/core';
import { Comment } from 'src/app/models/comment.model';
import * as constants from "../../../../constants/constants";
import { Messages } from 'src/constants/messages';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import MessageDialogInterface from 'src/classes/messagedialog.interface';
import MessageDialog from 'src/classes/messagedialog';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-comments',
  templateUrl: './comments.component.html',
  styleUrls: ['./comments.component.scss']
})
export class CommentsComponent implements OnInit,AfterViewInit {

  @Input() permalink: string|null;
  addComment_url: string = constants.createComment;
  getComments_url: string = constants.articleComments;

   done: boolean;
   error: boolean;
   empty: boolean;
   comments: Comment[];
   message: string;
   newComment: FormControl = new FormControl('',Validators.required);
   logged: boolean;
   userCookie: any = {};


  constructor(public http: HttpClient, public api: ApiService) {
    this.observeFromService();
   }

  ngAfterViewInit(): void {
    //console.log(this.permalink);
    this.getCommnents();
  }

  ngOnInit(): void {

  }

  //Set the reactive form for add new comment element
  addComment(){
    if(this.newComment.valid){
      const post_values = {
        'comment_text': this.newComment.value,
        'permalink': this.permalink,
        'token_key': this.userCookie['token_key']
      };
      /* console.log("post values");
      console.log(post_values); */
      const headers: HttpHeaders = new HttpHeaders().set('Content-Type','application/json').set('Accept','application/json');
      this.http.post(this.addComment_url,post_values,{headers: headers, responseType: 'text'}).subscribe(res => {
        //console.log(res);
        let json: object = JSON.parse(res);
        //console.log(json);
        if(json['done'] === true){
          setTimeout(()=>{
            this.getCommnents();
          },500);
        }
        else{
          let md_data: MessageDialogInterface = {
            title: 'Nuovo commento',
            message: json['msg']
          };
          this.dialogMessage(md_data);
        }
      },error => {
        console.warn(error);
        let md_data: MessageDialogInterface = {
          title: 'Nuovo commento',
          message: Messages.COMMENTNEW_ERROR
        };
        this.dialogMessage(md_data);
      });
    }//if(this.newComment.valid){
    else{
      let md_data: MessageDialogInterface = {
        title: 'Nuovo commento',
        message: Messages.INSERTCOMMENT_ERROR
      };
      this.dialogMessage(md_data);
    }
  }

  dialogMessage(md_data: MessageDialogInterface) {
    let md: MessageDialog = new MessageDialog(md_data);
      md.bt_ok.addEventListener('click',()=>{
        md.instance.dispose();
        md.div_dialog.remove();
        document.body.style.overflow = 'auto';
      });
  }

  //Get comments of this article
  getCommnents(): void{
    let get_url: string = this.getComments_url+'?permalink='+this.permalink
    if(this.userCookie.hasOwnProperty('token_key')){
      get_url += "&token_key"+this.userCookie['token_key'];
    }
    this.http.get(get_url,{responseType: 'text'}).subscribe(res => {
      //console.log(res);
      let json: object = JSON.parse(res);
      this.done = json['done'] as boolean;
      this.empty = json['empty'] as boolean;
      if(!this.empty)
        this.comments = json['comments'] as Comment[];
      /* console.log(this.done);
      console.log(this.empty); */
      console.log(this.comments);
    }, error => {
      console.warn(error);
      this.error = true;
      this.message = Messages.COMMENTLIST_ERROR;
    });
  }

  //Check modification from service methods
  observeFromService():void{
    this.api.getLoginStatus().then(res => {
      if(res == true){
        this.userCookie['token_key'] = localStorage.getItem('token_key');
        this.userCookie['username'] = localStorage.getItem('username');
        this.api.changeUserdata(this.userCookie);
        this.logged = true;
      }//if(res == true){
      else{
        this.removeCookie();
      }
    }).catch(err => {
      this.removeCookie();
    });//this.api.getLoginStatus().then(res => {
    //console.log("observeFormService logged => "+this.logged);
    this.api.loginChanged.subscribe(logged => {
      //console.log("logged");
      console.log(logged);
    });
    this.api.userChanged.subscribe(userdata => {
      /* console.log("userdata");
      console.log(userdata); */
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

  removeCookie(): void{
    this.api.removeItems();
    this.userCookie = {};
    this.api.changeUserdata(this.userCookie);
    this.logged = false;
  }


}
