import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import {Article} from '../../models/article.model';
import * as constants from '../../../constants/constants';
import { Keys } from 'src/constants/keys';
import SearchedArticlesInterface from 'src/interfaces/requests/article/searchedarticles.interface';
import SearchedArticles from 'src/classes/requests/article/searchedarticles';
import { Messages } from 'src/constants/messages';

@Component({
  selector: 'app-blog',
  templateUrl: './blog.component.html',
  styleUrls: ['./blog.component.scss']
})
export class BlogComponent implements OnInit {

  requestFailed: boolean = false;
  searchSpinner: boolean = false;
  done: boolean = false;
  empty: boolean = false;
  message: string|null = null;
  url: string = constants.searchArticles;
  articles: Article[] = new Array();

  constructor(public http: HttpClient) { }

  ngOnInit(): void {
  }

  //When user click Search button
  onSearchClick(search: HTMLInputElement): void{
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
        this.printResult(this.articles);
      }
      else{
        this.requestFailed = true;
        this.message = res[Keys.MESSAGE];
      } 
    }).catch(err => {
      this.searchSpinner = false;
      this.done = false;
      this.message = Messages.ARTICLESEARCH_ERROR;
      this.requestFailed = true;
    });
  }

  //print the articles list in blog page
  printResult(res: Article[]): void{
    $('#articlesList').html('');
    let divR, divC;
    let title,intro;
    let multi = false;
    if(res.length > 1)
      multi = true;
    let divCont = $('<div>');
    divCont.addClass('container');
    res.forEach(function(elem, index){
      /*console.log(index);
      console.log(elem);*/
        divR = $('<div>');
        divR.addClass('row row-article');
          divC = $('<div>');
          divC.addClass('col col-md-8 offset-md-2');
            title = $('<h3>');
            title.addClass('title');
            title.text(elem.title);
            title.css('text-align','center');
          divC.append(title);
            intro = $('<div>');
            intro.addClass('intro');
            intro.text(elem.introtext);
          divC.append(intro);
        if(multi && (index <= res.length - 2 )){
          //If there are more than one article and it's not the last iteration
          divR.css('border-bottom','1px solid black');
        }
        divR.css('margin','20px 0px');
        divR.append(divC);
        divR.on('click',function(){
          //go to article link if user clicks on the div
          let link = '/blog/'+elem.permalink;
          window.open(link, '_blank');
        });
      divCont.append(divR);
    });
    $('#articlesList').append(divCont);
    let rows = $('.row-article');
    rows.on('mouseenter',(e)=>{
      $(e.currentTarget).css({
        cursor : 'pointer',
        'background-color': 'rgba(255,215,0,0.3)', //gold
        opacity : '0.9'
      });
    });
    rows.on('mouseleave',(e)=>{
      $(e.currentTarget).css({
        cursor : 'auto',
        'background-color' : 'transparent',
        opacity : '1'
      });
    });
  }

}
