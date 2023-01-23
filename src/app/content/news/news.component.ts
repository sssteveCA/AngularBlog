import { HttpClient } from '@angular/common/http';
import { Component, OnInit, AfterViewInit } from '@angular/core';
import GetLastPosts from 'src/classes/requests/article/getlastposts';
import GetLastPostsInterface from 'src/interfaces/requests/article/getlastposts.interface';
import * as constants from "../../../constants/constants";

@Component({
  selector: 'app-news',
  templateUrl: './news.component.html',
  styleUrls: ['./news.component.scss']
})
export class NewsComponent implements OnInit, AfterViewInit {

  showSpinner: boolean = true;
  url: string = constants.lastPostsUrl;

  constructor(public http: HttpClient) { }

  ngAfterViewInit(): void {
    let glpData: GetLastPostsInterface = {
      http: this.http,
      url: this.url
    }
    let glp: GetLastPosts = new GetLastPosts(glpData);
    glp.getLastPosts().then(res => {
      this.showSpinner = false;
    });
  }

  ngOnInit(): void {
  }

}
