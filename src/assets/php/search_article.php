<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: *');

require_once("config.php");

$response = array();
$response['msg'] = '';
$response['done'] = false; 
//$response['post'] = $_POST;

if(isset($_POST['query']) && $_POST['query'] != ''){
    $mysqli = new mysqli(HOSTNAME,USERNAME,PASSWORD,DATABASE);
    if($mysqli->connect_errno == 0)
    {
        $mysqli->set_charset("utf8mb4");
        $tabella = TABLE_ARTICLES;
        $query = $mysqli->real_escape_string($_POST['query']);
        $sql = <<<SQL
SELECT * FROM `{$tabella}` WHERE `permalink` = '$query';
SQL;
        $result = $mysqli->query($sql);
        if($result !== false){
            if($result->num_rows == 1){
                $article = $result->fetch_assoc();
                $response['article'] = $article;
                $response['done'] = true;
            }//if($result->num_rows == 1){
            else{
                $response['notfound'] = true;
            }
            $result->free_result();
        }//if($result !== false){
        else{
            $response['msg'] = $mysqli->error;
        }
        $mysqli->close();
    }
    else{
        $response['msg'] = $mysqli->connect_error;
    }
}
else{
    $response['msg'] = 'Inserire i dati richiesti per continuare';
}

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>