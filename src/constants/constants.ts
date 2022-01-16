//url redirect after login success
export const loginRedirect: string = "/";

//php script for user login
export const loginUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/login.php";

//url redirect after logout
export const logoutRedirect: string = "/";

//php script for user logout
export const logoutUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/logout.php";

//url redirect if user not logged try access to poute that need to be authenticated
export const notLoggedRedirect: string = "login";

//url for get information of the logged account
export const profileUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/profile.php";

//php script for user registration
export const registerUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/register.php";