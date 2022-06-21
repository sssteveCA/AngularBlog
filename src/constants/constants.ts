//php script fro account activation
export const activationUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/attiva.php";

//blog url
export const blogUrl = '/blog';

//php script for check if user is authorized to manage a certain article
export const articleAuthorizedUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/article/article_authorized.php";

//php script for article view
export const articleView: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/article/search_article.php";

//php script for create an article
export const articleCreateUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/profile/myArticles/create.php";

//article edit url (without param)
export const articleEditUrl:string = '/profile/myArticles/edit';

//php script for send assistance email
export const contactUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/contact.php";

//home url of site
export const homeUrl: string = "http://localhost:4200";

//background image url
export const imgUrl : string = "http://localhost:4200/assets/img/background.jfif"; 

//url redirect after login success
export const loginRedirect: string = "/";

//url for check the login status of the user
export const loginStatusUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/loginstatus.php";

//php script for user login
export const loginUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/login.php";

//url redirect after logout
export const logoutRedirect: string = "/";

//php script for user logout
export const logoutUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/logout.php";

//php script for get articles created by specific user
export const myArticlesUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/profile/myArticles/get.php";

//url redirect if user not logged try access to poute that need to be authenticated
export const notLoggedRedirect: string = "login";

//url for get information of the logged account
export const profileUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/profile.php";

//php script for user registration
export const registerUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/register.php";

//php script for articles search
export const searchArticles: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/article/search_articles.php";