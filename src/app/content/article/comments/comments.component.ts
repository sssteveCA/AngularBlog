import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AfterViewInit, Component, Input, OnDestroy, OnInit } from '@angular/core';
import { Comment } from 'src/app/models/comment.model';
import * as constants from "../../../../constants/constants";
import { Messages } from 'src/constants/messages';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import MessageDialogInterface from 'src/interfaces/dialogs/messagedialog.interface';
import MessageDialog from 'src/classes/dialogs/messagedialog';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import AddCommentInterface from 'src/interfaces/requests/article/comment/addcomment.interface';
import AddComment from 'src/classes/requests/article/comment/addcomment';
import DeleteCommentInterface from 'src/interfaces/requests/article/comment/deletecomment.interface';
import DeleteComment from 'src/classes/requests/article/comment/deletecomment';
import GetCommentsInterface from 'src/interfaces/requests/article/comment/getcomments.interface';
import GetComments from 'src/classes/requests/article/comment/getcomments';
import UpdateCommentInterface from 'src/interfaces/requests/article/comment/updatecomment.interface';
import UpdateComment from 'src/classes/requests/article/comment/updatecomment';
import { messageDialog } from 'src/functions/functions';
import { Keys } from 'src/constants/keys';
import { LogindataService } from 'src/app/services/logindata.service';
import { UserCookie } from 'src/constants/types';
import { Subscription } from 'rxjs';
import { Router } from '@angular/router';

@Component({
  selector: 'app-comments',
  templateUrl: './comments.component.html',
  styleUrls: ['./comments.component.scss']
})
export class CommentsComponent implements OnInit,AfterViewInit, OnDestroy {

  @Input() permalink: string|null;
  @Input() cookie: UserCookie;
  addComment_url: string = constants.createComment;
  deleteComment_url: string = constants.deleteComment;
  getComments_url: string = constants.articleComments;
  updateComment_url: string = constants.commentUpdate;

   done: boolean;
   error: boolean;
   empty: boolean;
   comments: Comment[];
   message: string;
   newComment: FormControl = new FormControl('',Validators.required);
   oldComment_str: string;
   logged: boolean;
   subscription: Subscription;


  constructor(public http: HttpClient, private loginData: LogindataService, private router: Router) {
    //this.observeFromService();
   }
  ngOnDestroy(): void {
    if(this.subscription != null) this.subscription.unsubscribe();
  }

  ngAfterViewInit(): void {
    this.getCommnents();
  }

  ngOnInit(): void {
    this.loginDataObserver()
    this.loginData.changeLoginData({
      userCookie: {
        token_key: localStorage.getItem('token_key'),
        username: localStorage.getItem('username')
      }
    })
  }

  /**
   * Set the reactive form for add new comment element
   */
  addComment(){
    if(this.newComment.valid){
      const ac_data: AddCommentInterface = {
        comment_text: this.newComment.value,
        http: this.http,
        permalink: this.permalink as string,
        token_key: localStorage.getItem("token_key") as string,
        url: this.addComment_url
      };
      let ac: AddComment = new AddComment(ac_data);
      ac.addComment().then(obj => {
        if(obj[Keys.DONE] === true){
          setTimeout(()=>{
            this.getCommnents();
          },500);
        }//if(obj[Keys.DONE] === true){
        else{
          let md_data: MessageDialogInterface = {
            title: 'Nuovo commento',
            message: obj[Keys.MESSAGE]
          };
          let md: MessageDialog = new MessageDialog(md_data);
          md.bt_ok.addEventListener('click', ()=>{
            md.instance.dispose();
            md.div_dialog.remove();
            document.body.style.overflow = 'auto';
            if(obj[Keys.EXPIRED] == true){
              this.loginData.changeLoginData({
                logout: false, userCookie: {}
              })
            }
          });
        }
      }).catch(err => {
        console.warn(err);
        let md_data: MessageDialogInterface = {
          title: 'Nuovo commento',
          message: Messages.COMMENTNEW_ERROR
        };
        messageDialog(md_data);
      });//this.addCommentPromise(post_values).then(res => {
    }//if(this.newComment.valid){
    else{
      let md_data: MessageDialogInterface = {
        title: 'Nuovo commento',
        message: Messages.INSERTCOMMENT_ERROR
      };
      messageDialog(md_data);
    }
  }

  /**
   * delete comment event
   * @param event 
   */
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
      let dd_data: DeleteCommentInterface = {
        comment_id: comment_id,
        http: this.http,
        token_key: localStorage.getItem('token_key') as string,
        url: this.deleteComment_url
      };
      let dd: DeleteComment = new DeleteComment(dd_data);
      dd.deleteComment().then(obj => {
        if(obj[Keys.DONE] === true){
          //delete operation executed
          setTimeout(()=>{
            this.comments = this.comments.filter((comment)=> comment.id != dd.comment_id)
          },500);
        }//if(obj[Keys.DONE] === true){
        else{
          //Error during comment delete
          let md_data: MessageDialogInterface = {
            title: 'Elimina commento',
            message: obj[Keys.MESSAGE]
          };
          messageDialog(md_data);
        }
      }).catch(err => {
        console.warn(err);
        let md_data: MessageDialogInterface = {
          title: 'Elimina commento',
          message: Messages.COMMENTDELETE_ERROR
        };
        messageDialog(md_data);
      });
    });
    cd.bt_no.addEventListener('click',()=>{
      cd.instance.dispose();
      cd.div_dialog.remove();
      document.body.style.overflow = 'auto';
    });
  }

  /**
   * Get comments of this article
   */
  getCommnents(): void{
    let gc_data: GetCommentsInterface = {
      http: this.http,
      permalink: this.permalink as string,
      token_key: localStorage.getItem('token_key') as string|null,
      url: this.getComments_url
    };
    let gc: GetComments = new GetComments(gc_data);
    gc.getComments().then(obj => {
      this.done = obj[Keys.DONE] as boolean;
      this.empty = obj[Keys.EMPTY] as boolean;
      this.error = obj['error'] as boolean;
      this.message = obj[Keys.MESSAGE] as string;
      if(!this.empty)
        this.comments = obj['comments'] as Comment[];
    }).catch(err => {
      console.warn(err);
      this.error = true;
      this.message = Messages.COMMENTLIST_ERROR;
    });
  }

  loginDataObserver(): void{
    this.subscription = this.loginData.loginDataObservable.subscribe(loginData => {
      if(loginData.userCookie && loginData.userCookie.token_key != null && loginData.userCookie.username != null)
        this.logged = true;
      else{
        this.logged = false;
        if(loginData.logout == false)
          this.router.navigateByUrl(constants.notLoggedRedirect);
      }
        
    })
  }

  updateComment(event): void{
    let link: JQuery = $(event.target);
    let input: JQuery = link.siblings('input');
    let comment_id: string = input.val() as string;
    let comment_div: JQuery = link.parents('.comment');
    let text_div: JQuery = comment_div.find('.text');
    let textarea_inside: boolean = text_div.find('textarea').length > 0 ? true : false;
    if(textarea_inside == false){
      //If element is a div turn into a textarea
      let comment_text: string = text_div.children('div').html() as string;
      this.oldComment_str = comment_text;
      text_div.html('<div><textarea style="resize: vertical; width: 100%;">'+comment_text+'</textarea></div>');
    }
    else{
      //If element is not a div turn it into  a div
      let new_comment_val: string = text_div.find('textarea').val() as string;
      const uc_data: UpdateCommentInterface = {
        comment_id: comment_id,
        http: this.http,
        new_comment: new_comment_val,
        old_comment: this.oldComment_str,
        token_key: localStorage.getItem("token_key") as string,
        url: this.updateComment_url
      };
      let ec: UpdateComment = new UpdateComment(uc_data);
      ec.updateComment().then(obj => {
        if(obj[Keys.DONE] == true){
          text_div.html('<div>'+obj['comment']+'</div>');
        }
        else{
          let md_data: MessageDialogInterface = {
            title: 'Modifica commento',
            message: obj[Keys.MESSAGE]
          }
          let md: MessageDialog = new MessageDialog(md_data);
          md.bt_ok.addEventListener('click', ()=>{
            md.instance.dispose();
            md.div_dialog.remove();
            document.body.style.overflow = 'auto';
            text_div.html('<div>'+this.oldComment_str+'</div>');
          });
        }
      }).catch(err => {
        const md_data: MessageDialogInterface = {
          title: 'Modifica commento',
          message: Messages.COMMENTUPDATE_ERROR
        };
        let md: MessageDialog = new MessageDialog(md_data);
        md.bt_ok.addEventListener('click', ()=>{
          md.instance.dispose();
          md.div_dialog.remove();
          document.body.style.overflow = 'auto';
          text_div.html('<div>'+this.oldComment_str+'</div>')
        });
      });
      //text_div.html('<div>'+new_comment_val+'</div>');
    }
  } 
}
