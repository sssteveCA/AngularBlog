import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ArticleComponent } from './content/article/article.component';
import { BlogComponent } from './content/blog/blog.component';
import { ContactsComponent } from './content/contacts/contacts.component';
import { IndexComponent } from './content/index/index.component';
import { NewsComponent } from './content/news/news.component';
import { NotFound404Component } from './content/not-found404/not-found404.component';
import { WhoWeAreComponent } from './content/who-we-are/who-we-are.component';

const routes: Routes = [
  {path : "", component : IndexComponent},
  {path: "blog", component: BlogComponent},
  {path: "chisiamo", component: WhoWeAreComponent},
  {path: "news", component: NewsComponent},
  {path: "contatti", component: ContactsComponent},
  {path: "blog/:article", component: ArticleComponent},
  {path: "404", component: NotFound404Component},
  {path: "**", redirectTo: '/404'}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
