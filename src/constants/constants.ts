//Backend Host
export const host: string = "http://localhost";

//Angular project relative path
export const angularPath: string = "/angular/ex6/AngularBlog";

//php script fro account activation
export const activationUrl: string = host+angularPath+"/src/assets/php/account/attiva.php";

//blog url
export const blogUrl = '/blog';

//php script for check if user is authorized to manage a certain article
export const articleAuthorizedUrl: string = host+angularPath+"/src/assets/php/article/article_authorized.php";

//php script for get the article comments
export const articleComments: string = host+angularPath+"/src/assets/php/comment/article_comments.php";

//php script for article view
export const articleView: string = host+angularPath+"/src/assets/php/article/search_article.php";

//php script for create an article
export const articleCreateUrl: string = host+angularPath+"/src/assets/php/account/profile/myArticles/create.php";

//article delete url script

export const articleDeleteUrl: string = host+angularPath+"/src/assets/php/account/profile/myArticles/delete.php";

//article edit url (without param)
export const articleEditUrl: string = "/profile/myArticles/edit";

//article edit url script
export const articleEditScriptUrl: string = host+angularPath+"/src/assets/php/account/profile/myArticles/edit.php";

//php script for send assistance email
export const contactUrl: string = host+angularPath+"/src/assets/php/contact.php";

//home url of site
export const homeUrl: string = "http://localhost:4200";

//background image url
export const imgUrl : string = homeUrl+"/assets/img/background.jfif"; 

//url redirect after login success
export const loginRedirect: string = "/";

//url for check the login status of the user
export const loginStatusUrl: string = host+angularPath+"/src/assets/php/account/loginstatus.php";

//php script for user login
export const loginUrl: string = host+angularPath+"/src/assets/php/account/login.php";

//url redirect after logout
export const logoutRedirect: string = "/";

//php script for user logout
export const logoutUrl: string = host+angularPath+"/src/assets/php/account/logout.php";

//php script for get articles created by specific user
export const myArticlesUrl: string = host+angularPath+"/src/assets/php/account/profile/myArticles/get.php";

//url redirect if user not logged try access to poute that need to be authenticated
export const notLoggedRedirect: string = "/login";

//url for get information of the logged account
export const profileUrl: string = host+angularPath+"/src/assets/php/account/profile.php";

//php script for user registration
export const registerUrl: string = host+angularPath+"/src/assets/php/account/register.php";

//php script for articles search
export const searchArticles: string = host+angularPath+"/src/assets/php/article/search_articles.php";