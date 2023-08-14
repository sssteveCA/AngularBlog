import { Component, Input, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Article } from 'src/app/models/article.model';

@Component({
  selector: 'app-article-list-item',
  templateUrl: './article-list-item.component.html',
  styleUrls: ['./article-list-item.component.scss']
})
export class ArticleListItemComponent implements OnInit {

  @Input() article: Article;
  @Input() blogUrl: string;

  constructor(private router: Router) { }

  ngOnInit(): void {
  }

  /**
   * Open the clicked post from posts list in a new window
   * @param permalink the permalink of the post
   */
  onClickItem(permalink: string): void{
    let url: string = `blog/${permalink}`;
    this.router.navigateByUrl(url);
  }

}
