import { Component, Input, OnInit } from '@angular/core';
import { Article } from 'src/app/models/article.model';

@Component({
  selector: 'app-article-list-item',
  templateUrl: './article-list-item.component.html',
  styleUrls: ['./article-list-item.component.scss']
})
export class ArticleListItemComponent implements OnInit {

  @Input() article: Article;

  constructor() { }

  ngOnInit(): void {
  }

  /**
   * Open the clicked post from posts list in a new window
   * @param permalink the permalink of the post
   */
  onClickItem(permalink: string): void{
    let url: string = `blog/${permalink}`;
    window.open(url);
  }

}
