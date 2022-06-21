import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import {HttpClientModule} from '@angular/common/http';
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
    EditArticleComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    FormsModule,
    ReactiveFormsModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
