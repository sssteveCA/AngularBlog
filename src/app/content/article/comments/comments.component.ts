import { HttpClient } from '@angular/common/http';
import { AfterViewInit, Component, Input, OnInit } from '@angular/core';
import * as constants from "../../../../constants/constants";

@Component({
  selector: 'app-comments',
  templateUrl: './comments.component.html',
  styleUrls: ['./comments.component.scss']
})
export class CommentsComponent implements OnInit,AfterViewInit {

  @Input() permalink: string|null;
  url: string = constants.articleComments;

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
      console.log(json);
    }, error => {
      console.warn(error);
    });
  }


}
