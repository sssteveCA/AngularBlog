import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AfterViewInit, Component, Input, OnInit } from '@angular/core';
import { Comment } from 'src/app/models/comment.model';
import * as constants from "../../../../constants/constants";
import { Messages } from 'src/constants/messages';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import { ApiService } from 'src/app/api.service';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import AddCommentInterface from 'src/interfaces/requests/article/comment/addcomment.interface';
import AddComment from 'src/classes/requests/article/comment/addcomment';

@Component({
  selector: 'app-comments',
  templateUrl: './comments.component.html',
  styleUrls: ['./comments.component.scss']
})
export class CommentsComponent implements OnInit,AfterViewInit {

  @Input() permalink: string|null;
  addComment_url: string = constants.createComment;
  deleteComment_url: string = constants.deleteComment;
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
      const ac_data: AddCommentInterface = {
        comment_text: this.newComment.value,
        http: this.http,
        permalink: this.permalink as string,
        token_key: this.userCookie['token_key'],
        url: this.addComment_url
      };
      let ac: AddComment = new AddComment(ac_data);
      ac.addComment().then(obj => {
        if(obj['done'] === true){
          setTimeout(()=>{
            this.getCommnents();
          },500);
        }//if(obj['done'] === true){
        else{
          let md_data: MessageDialogInterface = {
            title: 'Nuovo commento',
            message: obj['msg']
          };
          this.dialogMessage(md_data);
        }
      }).catch(err => {
        console.warn(err);
        let md_data: MessageDialogInterface = {
          title: 'Nuovo commento',
          message: Messages.COMMENTNEW_ERROR
        };
        this.dialogMessage(md_data);
      });//this.addCommentPromise(post_values).then(res => {
    }//if(this.newComment.valid){
    else{
      let md_data: MessageDialogInterface = {
        title: 'Nuovo commento',
        message: Messages.INSERTCOMMENT_ERROR
      };
      this.dialogMessage(md_data);
    }
  }

  private async addCommentPromise(createData: object): Promise<any>{
    return await new Promise((resolve,reject)=>{
      const headers: HttpHeaders = new HttpHeaders({
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      });
      this.http.post(this.addComment_url,createData,{headers: headers, responseType: 'text'}).subscribe(res => {
        resolve(res);
      }, error => {
        reject(error);
      });
    });
  }

  //delete comment event
  deleteComment(event): void{
    let link: JQuery = $(event.target);
    let input: JQuery = link.siblings('input');
    let comment_id: string = input.val() as string;
    let cd_data: ConfirmDialogInterface = {
      title: 'Elimina commento',
      message: Messages.DELETECOMMENT_CONFIRM
    };
    let cd: ConfirmDialog = new ConfirmDialog(cd_data);
    cd.bt_yes.addEventListener('click', ()=>{
      //User confirms comment delete
      cd.instance.dispose();
      cd.div_dialog.remove();
      document.body.style.overflow = 'auto';
      console.log(this.userCookie);
      const delete_data = {
        'token_key': this.userCookie['token_key'],
        'comment_id': comment_id
      };
      this.deletePromise(delete_data).then(res => {
        console.log(res);
        const json: object = JSON.parse(res);
        if(json['done'] === true){
          //delete operation executed
          setTimeout(()=>{
            this.getCommnents();
          },500);
        }
        else{
          //Error during comment delete
          let md_data: MessageDialogInterface = {
            title: 'Elimina commento',
            message: json['msg']
          };
          this.dialogMessage(md_data);
        }
      }).catch(err => {
        console.warn(err);
        let md_data: MessageDialogInterface = {
          title: 'Elimina commento',
          message: Messages.COMMENTDELETE_ERROR
        };
        this.dialogMessage(md_data);
      });
    });
    cd.bt_no.addEventListener('click',()=>{
      cd.instance.dispose();
      cd.div_dialog.remove();
      document.body.style.overflow = 'auto';
    });
  }

  //comment delete request
  private async deletePromise(deleteData: object): Promise<any>{
    return await new Promise((resolve,reject) => {
      const headers: HttpHeaders = new HttpHeaders({
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      });
      this.http.post(this.deleteComment_url,deleteData,{headers: headers, responseType: 'text'}).subscribe(res => {
        resolve(res);
      },error => {
        reject(error);
      })
    });
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
    let get_url: string = this.getComments_url+'?permalink='+this.permalink;
    let token_key = localStorage.getItem('token_key');
    if(token_key !== null){
      get_url += "&token_key="+token_key;
    }
    this.http.get(get_url,{responseType: 'text'}).subscribe(res => {
      //console.log(res);
      let json: object = JSON.parse(res);
      this.done = json['done'] as boolean;
      this.empty = json['empty'] as boolean;
      this.error = json['error'] as boolean;
      this.message = json['msg'] as string;
      if(!this.empty)
        this.comments = json['comments'] as Comment[];
      /* console.log(this.done);
      console.log(this.empty); */
      //onsole.log(this.comments);
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

  updateComment(event): void{
    let link: JQuery = $(event.target);
    let input: JQuery = link.siblings('input');
    let comment_id: string = input.val() as string;
    let comment_div: JQuery = link.parents('.comment');
    let text_div: JQuery = comment_div.find('.text');
    let textarea_inside: boolean = text_div.find('textarea').length > 0 ? true : false;
    console.log(textarea_inside);
    if(textarea_inside == false){
      //If element is a div turn into a textarea
      let comment_text: string = text_div.children('div').html() as string;
      text_div.html('<div><textarea style="resize: vertical; width: 100%;">'+comment_text+'</textarea></div>');
    }
    else{
      //If element is not a div turn it into  a div
      let new_comment_val: string = text_div.find('textarea').val() as string;
      text_div.html('<div>'+new_comment_val+'</div>');
    }
  } 
}
