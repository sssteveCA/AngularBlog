//Set this values depending on your environment

export namespace Config{
    //Angular server
    export const ANGULAR_SCHEME: string = "http";
    export const ANGULAR_HOST: string = "localhost";
    export const ANGULAR_PORT:number = 4200;
    export const ANGULAR_MAIN_URL: string = ANGULAR_SCHEME+'://'+ANGULAR_HOST+':'+ANGULAR_PORT;

    //Server for php scripts
    export const PHP_SCHEME: string = "http";
    export const PHP_HOST: string = "localhost";
    export const PHP_PORT:number = 80;
    export const PHP_MAIN_URL: string = PHP_SCHEME+'://'+PHP_HOST+':'+PHP_PORT;

    //Angular project relative path
    export const SITE_PATH: string = "/repo/AngularBlog";

    //main directory of php files
    export const PHP_FOLDER: string = "/assets/php";

    //Main path for php scripts
    export const PHP_SCRIPTS_URL: string = PHP_MAIN_URL+SITE_PATH+PHP_FOLDER;
}
