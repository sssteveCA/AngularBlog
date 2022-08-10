import { HttpClient } from '@angular/common/http';
import { AfterViewInit, Component, Input, OnInit } from '@angular/core';
import { Comment } from 'src/app/models/comment.model';
import * as constants from "../../../../constants/constants";
import { Messages } from 'src/constants/messages';

@Component({
  selector: 'app-comments',
  templateUrl: './comments.component.html',
  styleUrls: ['./comments.component.scss']
})
export class CommentsComponent implements OnInit,AfterViewInit {

  @Input() permalink: string|null;
  url: string = constants.articleComments;

  private done: boolean;
  private error: boolean;
  private empty: boolean;
  private comments: Comment[];
  private message: string;

  constructor(public http: HttpClient) { }

  ngAfterViewInit(): void {
    console.log(this.permalink);
    this.getCommnents();
  }

  ngOnInit(): void {

  }

  getCommnents(): void{
    this.http.get(this.url+'?permalink='+this.permalink,{responseType: 'text'}).subscribe(res => {
      console.log(res);
      let json: object = JSON.parse(res);
      this.done = json["done"] as boolean;
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
