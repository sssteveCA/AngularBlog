import { Config } from "config";

/* //Backend Host
export const host: string = "http://localhost";

//Angular project relative path
export const angularPath: string = "/angular/ex6/AngularBlog";

//main directory of php files
export const phpFolder: string = "/src/assets/php"; */

/**
 * php script fro account activation
 */
export const activationUrl: string = Config.PHP_SCRIPTS_URL+"/account/attiva.php";

/**
 * blog url
 */
export const blogUrl = '/blog';

/**
 * php script for check if user is authorized to manage a certain article
 */
export const articleAuthorizedUrl: string = Config.PHP_SCRIPTS_URL+"/article/article_authorized.php";

/**
 * php script for get the article comments
 */
export const articleComments: string = Config.PHP_SCRIPTS_URL+"/article/comment/article_comments.php";

/**
 * php script for article view
 */
export const articleView: string = Config.PHP_SCRIPTS_URL+"/article/search_article.php";

/**
 * php script for create an article
 */
export const articleCreateUrl: string = Config.PHP_SCRIPTS_URL+"/account/profile/myArticles/create.php";

/**
 * article delete url script
 */
export const articleDeleteUrl: string = Config.PHP_SCRIPTS_URL+"/account/profile/myArticles/delete.php";

/**
 * article edit url (without param)
 */
export const articleEditUrl: string = "/profile/myArticles/edit";

/**
 * article edit url script
 */
export const articleEditScriptUrl: string = Config.PHP_SCRIPTS_URL+"/account/profile/myArticles/edit.php";

/**
 * php script for send assistance email
 */
export const contactUrl: string = Config.PHP_SCRIPTS_URL+"/contact.php";

/**
 * php script fot create new comment
 */
export const createComment: string = Config.PHP_SCRIPTS_URL+"/article/comment/create.php";

/**
 * Url redirect after successfully deleted the logged account
 */
export const deleteAccountRedirect: string = "/";

/**
 * php script for delete comment
 */
export const deleteComment: string = Config.PHP_SCRIPTS_URL+"/article/comment/delete.php";

//home url of site
//export const homeUrl: string = "http://localhost:4200";

/**
 * background image url
 */
export const imgUrl : string = Config.ANGULAR_MAIN_URL+"/assets/img/background.jfif"; 

/**
 * url redirect after login success
 */
export const loginRedirect: string = "/";

/**
 * url for check the login status of the user
 */
export const loginStatusUrl: string = Config.PHP_SCRIPTS_URL+"/account/loginstatus.php";

/**
 * php script for user login
 */
export const loginUrl: string = Config.PHP_SCRIPTS_URL+"/account/login.php";

/**
 * url redirect after logout
 */
export const logoutRedirect: string = "/";

/**
 * php script for user logout
 */
export const logoutUrl: string = Config.PHP_SCRIPTS_URL+"/account/logout.php";

/**
 * php script for get articles created by specific user
 */
export const myArticlesUrl: string = Config.PHP_SCRIPTS_URL+"/account/profile/myArticles/get.php";

/**
 * url redirect if user not logged try access to poute that need to be authenticated
 */
export const notLoggedRedirect: string = "/login";

/**
 * url redirect if resource not found
 */
export const notFoundUrl: string = '/404';

/**
 * URL to delete the logged account
 */
 export const profileDeleteUrl: string = Config.PHP_SCRIPTS_URL+"/account/profile/info/deleteprofile.php";

/**
 * URL to get the name and the surname of the logged user
 */
export const profileGetNamesUrl: string = Config.PHP_SCRIPTS_URL+"/account/profile/info/getnames.php"

/**
 * URL to get the logged username
 */
export const profileGetUsernameUrl: string = Config.PHP_SCRIPTS_URL+"/account/profile/info/getusername.php";

/**
 * URL to update the name and the surname of the logged user
 */
export const profileUpdateNamesUrl: string = Config.PHP_SCRIPTS_URL+"/account/profile/info/updatenames.php";

/**
 * URL to update the account logged password
 */
 export const profileUpdatePasswordUrl: string = Config.PHP_SCRIPTS_URL+"/account/profile/info/updatepassword.php";

/**
 * URL to update the logged username
 */
 export const profileUpdateUsernameUrl: string = Config.PHP_SCRIPTS_URL+"/account/profile/info/updateusername.php";

/**
 * url for get information of the logged account
 */
export const profileUrl: string = Config.PHP_SCRIPTS_URL+"/account/profile.php";

/**
 * php script for user registration
 */
export const registerUrl: string = Config.PHP_SCRIPTS_URL+"/account/register.php";

/**
 * php script for articles search
 */
export const searchArticles: string = Config.PHP_SCRIPTS_URL+"/article/search_articles.php";

/**
 * php script for comment update
 */
export const commentUpdate: string = Config.PHP_SCRIPTS_URL+"/article/comment/edit.php";