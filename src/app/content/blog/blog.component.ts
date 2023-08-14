import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import {Article} from '../../models/article.model';
import * as constants from '../../../constants/constants';
import { Keys } from 'src/constants/keys';
import SearchedArticlesInterface from 'src/interfaces/requests/article/searchedarticles.interface';
import SearchedArticles from 'src/classes/requests/article/searchedarticles';
import { Messages } from 'src/constants/messages';
import { Config } from 'config';

@Component({
  selector: 'app-blog',
  templateUrl: './blog.component.html',
  styleUrls: ['./blog.component.scss']
})
export class BlogComponent implements OnInit {

  blogUrl: string = Config.ANGULAR_MAIN_URL+constants.blogUrl;
  requestFailed: boolean = false;
  searchSpinner: boolean = false;
  spinnerId: string = "news-spinner"
  done: boolean = false;
  empty: boolean = false;
  message: string|null = null;
  url: string = constants.searchArticles;
  articles: Article[] = new Array();
  backlink: string = "/";
  title: string = "Blog";

  constructor(public http: HttpClient) { }

  ngOnInit(): void {
  }

  //When user click Search button
  onSearchClick(search: HTMLInputElement): void{
    this.requestFailed = false;
    let saData: SearchedArticlesInterface = {
      http: this.http,
      query: search.value,
      url: this.url
    }
    let sa: SearchedArticles = new SearchedArticles(saData);
    this.searchSpinner = true;
    sa.searchedArticles().then(res => {
      this.searchSpinner = false;
      this.done = res[Keys.DONE];
      this.empty = res[Keys.EMPTY];
      if(this.done){
        this.requestFailed = false;
        this.articles = res[Keys.DATA];
      }
      else{
        this.requestFailed = true;
        this.message = res[Keys.MESSAGE];
      } 
    }).catch(err => {
      this.searchSpinner = false;
      this.requestFailed = true;
      this.done = false;
      this.message = Messages.ARTICLESEARCH_ERROR;
    });
  }

}
