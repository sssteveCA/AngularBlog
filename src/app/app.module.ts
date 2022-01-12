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
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
