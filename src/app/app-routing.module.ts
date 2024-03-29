import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AttivaComponent } from './account/attiva/attiva.component';
import { LoginComponent } from './account/login/login.component';
import { NewArticleComponent } from './account/profile/new-article/new-article.component';
import { MyArticlesComponent } from './account/profile/my-articles/my-articles.component';
import { ProfileComponent } from './account/profile/profile.component';
import { RegisterComponent } from './account/register/register.component';
import { ArticleComponent } from './content/article/article.component';
import { BlogComponent } from './content/blog/blog.component';
import { ContactsComponent } from './content/contacts/contacts.component';
import { IndexComponent } from './content/index/index.component';
import { NewsComponent } from './content/news/news.component';
import { NotFound404Component } from './content/not-found404/not-found404.component';
import { WhoWeAreComponent } from './content/who-we-are/who-we-are.component';
import { AuthGuard } from './guard/auth.guard';
import { NotAuthGuard } from './guard/not-auth.guard';
import { EditArticleComponent } from './account/profile/edit-article/edit-article.component';
import { InfoComponent } from './account/profile/info/info.component';
import { PrivacyPolicyComponent } from './content/privacy-policy/privacy-policy.component';
import { CookiePolicyComponent } from './content/cookie-policy/cookie-policy.component';
import { TermsComponent } from './content/terms/terms.component';
import { HistoryComponent } from './account/profile/history/history.component';

const routes: Routes = [
  {path : "", component : IndexComponent},
  {path: "blog", component: BlogComponent},
  {path: "chisiamo", component: WhoWeAreComponent},
  {path: "news", component: NewsComponent},
  {path: "contatti", component: ContactsComponent},
  {path: "blog/:article", component: ArticleComponent},
  {path: "privacy_policy", component: PrivacyPolicyComponent},
  {path: "cookie_policy", component: CookiePolicyComponent},
  {path: "terms", component: TermsComponent},
  {path: "login", component: LoginComponent,canActivate:[NotAuthGuard]},
  {path: "register", component: RegisterComponent,canActivate:[NotAuthGuard]},
  {path: "attiva", component: AttivaComponent,canActivate:[NotAuthGuard]},
  {path: "profile", component:ProfileComponent, canActivate:[AuthGuard]},
  {path: "profile/history", component: HistoryComponent, canActivate:[AuthGuard]},
  {path: "profile/info", component: InfoComponent, canActivate:[AuthGuard]},
  {path: "profile/myArticles", component:MyArticlesComponent, canActivate:[AuthGuard]},
  {path: "profile/myArticles/create", component:NewArticleComponent, canActivate:[AuthGuard]},
  {path: "profile/myArticles/edit/:articleId",component: EditArticleComponent, canActivate:[AuthGuard]},
  {path: "404", component: NotFound404Component},
  {path: "**", redirectTo: '/404'}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
