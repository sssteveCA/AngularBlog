import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import {HttpClient, HttpClientModule} from '@angular/common/http';
import { Router } from '@angular/router';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { MenuComponent } from './menu/menu.component';
import { FooterComponent } from './footer/footer.component';
import { IndexComponent } from './content/index/index.component';
import { BlogComponent } from './content/blog/blog.component';
import { WhoWeAreComponent } from './content/who-we-are/who-we-are.component';
import { NewsComponent } from './content/news/news.component';
import { ContactsComponent } from './content/contacts/contacts.component';
import { SidebarComponent } from './content/sidebar/sidebar.component';
import { NotFound404Component } from './content/not-found404/not-found404.component';
import { ArticleComponent } from './content/article/article.component';
import { RegisterComponent } from './account/register/register.component';
import { LoginComponent } from './account/login/login.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { AttivaComponent } from './account/attiva/attiva.component';
import { ProfileComponent } from './account/profile/profile.component';
import { MyArticlesComponent } from './account/profile/my-articles/my-articles.component';
import { NewArticleComponent } from './account/profile/new-article/new-article.component';
import { EditArticleComponent } from './account/profile/edit-article/edit-article.component';
import { CommentsComponent } from './content/article/comments/comments.component';
import { InfoComponent } from './account/profile/info/info.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import {MatInputModule} from '@angular/material/input';
import {MatButtonModule} from '@angular/material/button';
import {MatCheckboxModule} from '@angular/material/checkbox';
import { UsernameComponent } from './account/profile/info/username/username.component';
import { PasswordComponent } from './account/profile/info/password/password.component';
import { DeleteAccountComponent } from './account/profile/info/delete-account/delete-account.component';
import { NamesComponent } from './account/profile/info/names/names.component';
import { PrivacyComponent } from './privacy/privacy.component';
import { PrivacyPolicyComponent } from './content/privacy-policy/privacy-policy.component';
import { CookiePolicyComponent } from './content/cookie-policy/cookie-policy.component';
import { TermsComponent } from './content/terms/terms.component';
import { PolicyItemComponent } from './menu/policy-item/policy-item.component';
import { EmailComponent } from './account/profile/info/email/email.component';
import { ArticleListItemComponent } from './content/article/article-list-item/article-list-item.component';
import { CookieBannerComponent } from './cookie-banner/cookie-banner.component';



@NgModule({
  declarations: [
    AppComponent,
    MenuComponent,
    FooterComponent,
    IndexComponent,
    BlogComponent,
    WhoWeAreComponent,
    NewsComponent,
    ContactsComponent,
    SidebarComponent,
    NotFound404Component,
    ArticleComponent,
    RegisterComponent,
    LoginComponent,
    AttivaComponent,
    ProfileComponent,
    MyArticlesComponent,
    NewArticleComponent,
    EditArticleComponent,
    CommentsComponent,
    InfoComponent,
    UsernameComponent,
    PasswordComponent,
    DeleteAccountComponent,
    NamesComponent,
    PrivacyComponent,
    PrivacyPolicyComponent,
    CookiePolicyComponent,
    TermsComponent,
    PolicyItemComponent,
    EmailComponent,
    ArticleListItemComponent,
    CookieBannerComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    FormsModule,
    ReactiveFormsModule,
    BrowserAnimationsModule,
    MatInputModule,
    MatButtonModule,
    MatCheckboxModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
