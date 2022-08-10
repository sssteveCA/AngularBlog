import { HttpClient } from '@angular/common/http';
import { AfterViewInit, Component, Input, OnInit } from '@angular/core';
import { Comment } from 'src/app/models/comment.model';
import * as constants from "../../../../constants/constants";
import { Messages } from 'src/constants/messages';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import MessageDialogInterface from 'src/classes/messagedialog.interface';
import MessageDialog from 'src/classes/messagedialog';

@Component({
  selector: 'app-comments',
  templateUrl: './comments.component.html',
  styleUrls: ['./comments.component.scss']
})
export class CommentsComponent implements OnInit,AfterViewInit {

  @Input() permalink: string|null;
  url: string = constants.articleComments;

   done: boolean;
   error: boolean;
   empty: boolean;
   comments: Comment[];
   message: string;
   newComment: FormControl = new FormControl('',Validators.required);


  constructor(public http: HttpClient) { }

  ngAfterViewInit(): void {
    //console.log(this.permalink);
    this.getCommnents();
  }

  ngOnInit(): void {

  }

  //Set the reactive form for add new comment element
  addComment(){
    if(this.newComment.valid){

    }//if(this.newComment.valid){
    else{
      let md_data: MessageDialogInterface = {
        title: 'Nuovo commento',
        message: Messages.INSERTCOMMENT_ERROR
      };
      let md: MessageDialog = new MessageDialog(md_data);
      md.bt_ok.addEventListener('click',()=>{
        md.instance.dispose();
        md.div_dialog.remove();
        document.body.style.overflow = 'auto';
      });
    }
  }

  //Get comments of this article
  getCommnents(): void{
    this.http.get(this.url+'?permalink='+this.permalink,{responseType: 'text'}).subscribe(res => {
      console.log(res);
      let json: object = JSON.parse(res);
      this.done = json['done'] as boolean;
      this.empty = json['empty'] as boolean;
      if(!this.empty)
        this.comments = json['comments'] as Comment[];
      console.log(this.done);
      console.log(this.empty);
      console.log(this.comments);
    }, error => {
      console.warn(error);
      this.error = true;
      this.message = Messages.COMMENTLIST_ERROR;
    });
  }


}
