<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: *');

require_once("config.php");

$response = array();
$response['msg'] = 'Ciao';
$response['done'] = false;
$response['post'] = $_POST;

if(isset($_POST['query']) && $_POST['query'] != ''){
    $mysqli = new mysqli(HOSTNAME,USERNAME,PASSWORD,DATABASE);
    if($mysqli->connect_errno == 0){
        $mysqli->set_charset("utf8mb4");
        $tabella = TABLE_ARTICLES;
        $query = $mysqli->real_escape_string($_POST['query']);
        $sql = <<<SQL
SELECT * FROM `{$tabella}` WHERE `title` LIKE '%{$query}%';
SQL;
        $result = $mysqli->query($sql);
        if($result !== false){
            if($result->num_rows > 0){
                $response['done'] = true;
                $i = 0;
                while(($row = $result->fetch_array(MYSQLI_ASSOC)) != null){
                    $response['articles'][$i] = $row;
                    $i++;
                }
            }//if($result->num_rows > 0){
            else{
                $response['msg'] = 'La ricerca di '.$_POST['query'].' non ha fornito alcun risultato';
            }
            $result->free_result();
        }//if($result !== false){
        else{
            $response['msg'] = $mysqli->error;
        }
        $mysqli->close();
    }//if($mysqli->connect_errno == 0){
    else{
        $response['msg'] = $mysqli->connect_error;
    }

}//if(isset($_POST['query']) && $_POST['query'] != ''){
else{
    $response['msg'] = 'Inserire i dati richiesti per continuare';
}

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>