import { HttpClient } from '@angular/common/http';
import { Component, OnInit, AfterViewInit, Input } from '@angular/core';
import { Article } from 'src/app/models/article.model';
import GetLastPosts from 'src/classes/requests/article/getlastposts';
import { Keys } from 'src/constants/keys';
import { Messages } from 'src/constants/messages';
import GetLastPostsInterface from 'src/interfaces/requests/article/getlastposts.interface';
import * as constants from "../../../constants/constants";

@Component({
  selector: 'app-news',
  templateUrl: './news.component.html',
  styleUrls: ['./news.component.scss']
})
export class NewsComponent implements OnInit, AfterViewInit {

  done: boolean = false;
  empty: boolean = false;
  lastPosts: Article[] = [];
  message: string|null = null;
  showSpinner: boolean = true;
  url: string = constants.lastPostsUrl;
  title: string = "News";

  constructor(public http: HttpClient) { }

  ngAfterViewInit(): void {
    this.getLastPosts();
  }

  ngOnInit(): void {
  }

  getLastPosts(): void{
    let glpData: GetLastPostsInterface = {
      http: this.http,
      url: this.url
    }
    let glp: GetLastPosts = new GetLastPosts(glpData);
    glp.getLastPosts().then(res => {
      this.showSpinner = false;
      this.done = res[Keys.DONE];
      this.empty = res[Keys.EMPTY];
      this.message = res[Keys.MESSAGE];
      if(this.done && this.empty == false){
        this.lastPosts = res[Keys.DATA];
      }
    }).catch(err => {
	  this.showSpinner = false;
      this.done = false;
      this.message = Messages.NEWS_ERROR;
    });
  }

}
