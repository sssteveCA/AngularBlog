import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import {Article} from '../../models/article.model';
import * as constants from '../../../constants/constants';

@Component({
  selector: 'app-blog',
  templateUrl: './blog.component.html',
  styleUrls: ['./blog.component.scss']
})
export class BlogComponent implements OnInit {

  url: string = constants.searchArticles;
  articles: Article[] = new Array();

  constructor(public http: HttpClient) { }

  ngOnInit(): void {
  }

  //When user click Search button
  onSearchClick(search: HTMLInputElement): void{
    let val = search.value;
    console.log(val);
    let params = new HttpParams().append('query',val);
    this.http.post(this.url,params,{responseType: 'text'}).subscribe(res => {
      //console.log(res);
      let rJson = JSON.parse(res);
      console.log(rJson);
      if(rJson['done'] == true){
        this.articles = rJson['articles'];
        //console.log(this.articles);
        this.printResult(this.articles);
      }
      else{
        $('#articlesList').html('');
        let divAlert = $('<div>');
        divAlert.addClass("alert alert-danger");
        divAlert.attr('role','alert');
        divAlert.css('text-align','center');
        divAlert.text(rJson['msg']);
        $('#articlesList').append(divAlert);
      }
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
        divR.addClass('row');
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
          //let link = 'http://localhost:4200/blog/'+elem.permalink;
          let link = '/blog/'+elem.permalink;
          window.open(link, '_blank');
        });
        divR.on('mouseenter',(e)=>{
          $(e.target).css({
            cursor : 'pointer',
            'background-color': 'rgba(255,215,0,0.3)', //gold
            opacity : '0.9'
          });
        });
        divR.on('mouseleave',(e)=>{
          $(e.target).css({
            cursor : 'auto',
            'background-color' : 'transparent',
            opacity : '1'
          });
        });
      divCont.append(divR);
    });
    $('#articlesList').append(divCont);
  }

}
