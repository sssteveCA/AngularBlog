<?php

require_once("../cors.php");
require_once("../config.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/models_errors.php");
require_once("../interfaces/article/articlelist_errors.php");
require_once("../vendor/autoload.php");
require_once("../class/models.php");
require_once("../class/article/articlelist.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Article\ArticleList;

$response = array();
$response['msg'] = 'Ciao';
$response['done'] = false;
$field = 'title';

if(isset($_POST['query']) && $_POST['query'] != ''){
    $query = $_POST['query'];
    try{
        $al = new ArticleList();
        $filter = array(
            'title' => [
                '$regex' => '/'.$query.'/i'
            ]
        );
        $found = $al->articlelist_get($filter);
        if($found){
            //At least one article found
            $response['articles'] = $al->getResults();
        }//if($found){
        else
            $response['msg'] = 'La ricerca di '.$query.' non ha fornito alcun risultato';
    }catch(Exception $e){

    }
}//if(isset($_POST['query']) && $_POST['query'] != ''){
else 
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response);
?>