<?php

define("ARTICLE_DATANOTUPDATED", "1");
define("ARTICLE_DATANOTINSERTED", "2");
define("ARTICLE_QUERYERROR", "3");
define("ARTICLE_INVALIDFIELD", "4");
define("ARTICLE_DATANOTSET", "5");
define("ARTICLE_INVALIDDATAFORMAT", "6");
define("ARTICLE_STATEMENTERROR", "7");
define("ARTICLE_INVALIDINDEX", "8");
define("ARTICLE_NORESULT", "9");

class Article{
    private $h; //MySQL connection handle
    private $connect; //true if there is a MySql connection opened
    private $id;
    private $title;
    private $author; //author id that created this article
    private $permalink;
    private $content;
    private $introtext;
    private $categories;
    private $tags;
    private $creation_time;
    private $last_modified;
    private $query; //last SQL query sent
    private $queries; //list of SQL queries executed
    private $errno; //last error code
    public static $campi = array('id','permalink');
    public static $regex = array(
        'id' => '/^[0-9]+$/',
        'tempo' => '/^([0-9]+)\s(2[0-3]|1[0-9]|[0-9])\s([0-5][0-9]|[0-9])\s([0-5][0-9]|[0-9])$/'
    );

    public static function getMultiData($field,$query){
        //retrieve multiple Article object from SELECT query
        $results = array();
        $mysqli = new mysqli(HOSTNAME,USERNAME,PASSWORD,DATABASE);
        if($mysqli->connect_errno == 0){
            $mysqli->set_charset("utf8mb4");
            $tabella = TABLE_ARTICLES;
            $queryE = $mysqli->real_escape_string($query);
            $sql = <<<SQL
SELECT * FROM `{$tabella}` WHERE `{$field}` LIKE '%{$queryE}%';
SQL;
            $result = $mysqli->query($sql);
            if($result !== false){
                if($result->num_rows > 0){
                    $results['done'] = true;
                    $i = 0;
                    while(($row = $result->fetch_array(MYSQLI_ASSOC)) != null){
                        $results['articles'][$i] = $row;
                        $i++;
                    }
                }//if($result->num_rows > 0){
                else
                    $results['msg'] = 'La ricerca di '.$queryE.' non ha fornito alcun risultato';
                $result->free_result();
            }//if($result !== false){
            else
                $results['msg'] = $mysqli->error;
            $mysqli->close();
        }//if($mysqli->connect_errno == 0){
        else
            $results['msg'] = $mysqli->connect_error;
        return $results;
    }

    public function __construct($dati){
        $this->connect = false;
        $mysqlHost=isset($dati['mysqlHost'])? $dati['mysqlHost']:HOSTNAME;
        $mysqlUser=isset($dati['mysqlUser'])? $dati['mysqlUser']:USERNAME;
        $mysqlPass=isset($dati['mysqlPass'])? $dati['mysqlPass']:PASSWORD;
        $mysqlDb=isset($dati['mysqlDb'])? $dati['mysqlDb']:DATABASE;
        $this->table=isset($dati['tabella'])? $dati['tabella']:TABLE_ARTICLES;
        $this->h = new mysqli($mysqlHost,$mysqlUser,$mysqlPass,$mysqlDb);
        if($this->h->connect_errno !== 0){
            throw new Exception("Connessione a MySql fallita: ".$this->h->connect_error);
        }
        $this->h->set_charset("utf8mb4");
        $this->query = null;
        $this->queries = array();
        if(!$this->createDb($mysqlDb)){
            throw new Exception("Errore durante il controllo del database");
        }
        if(!$this->createTable()){
            throw new Exception("Errore durante il controllo della tabella");
        }
        $this->errno = 0;
        $this->connect = true;
        $this->id = isset($dati['id'])? $dati['id']:null;
        $this->title = isset($dati['title'])? $dati['title']:null;
        $this->author = isset($dati['author'])? $dati['author']:null;
        $this->permalink = isset($dati['permalink'])? $dati['permalink']:null;
        $this->content = isset($dati['content'])? $dati['content']:null;
        $this->introtext=isset($dati['introtext'])? $dati['introtext']:null;
        $this->categories=isset($dati['categories'])? $dati['categories']:null;
        $this->tags=isset($dati['tags'])? $dati['tags']:null;
    }

    public function __destruct(){
        if($this->connect)$this->h->close();
    }

    //getters
    public function getId(){return $this->id;}
    public function getTitle(){return $this->title;}
    public function getAuthor(){return $this->author;}
    public function getPermalink(){return $this->permalink;}
    public function getContent(){return $this->content;}
    public function getIntrotext(){return $this->introtext;}
    public function getCategories(){return $this->categories;}
    public function getTags(){return $this->tags;}
    public function getCrTime(){return $this->creation_time;}
    public function getLastMod(){return $this->last_modified;}
    public function getQuery(){return $this->query;}
    public function getQueries(){return $this->queries;}
    public function getTable(){return $this->table;}
    public function getErrno(){return $this->errno;}

    //create database if not exists
    private function createDb($db){
        $ok = false;
        $this->query = <<<SQL
CREATE DATABASE IF NOT EXISTS {$db};
SQL;
        $this->queries[] = $this->query;
        $create = $this->h->query($this->query);
        if($create !== false)
            $ok = true;
        return $ok;
    }

        //create table if not exists
    private function createTable(){
        $ok = false;
        $this->query = <<<SQL
SHOW TABLES LIKE '{$this->table}';
SQL;
        $this->queries[] = $this->query;
        $show = $this->h->query($this->query);
        if($show !== false){
            if($show->num_rows == 0){
                $this->query = <<<SQL
CREATE TABLE `{$this->table}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `author` int(40) NOT NULL COMMENT 'Author ID of ''usersBlog'' table',
  `permalink` varchar(100) NOT NULL,
  `content` mediumtext NOT NULL,
  `introtext` varchar(250) NOT NULL COMMENT 'This is only the first part of the article content',
  `categories` varchar(500) NOT NULL COMMENT 'categories are separated by '',''',
  `tags` varchar(500) NOT NULL COMMENT 'tags are separated by '',''',
  `creation_time` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permalink` (`permalink`),
  KEY `author` (`author`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4
SQL;
            $this->queries[] = $this->query;
            $create = $this->h->query($this->query);
            if($create !== false)
                $ok = true;
            }//if($show->num_rows == 0){
            else
                $ok = true;
        }//if($show !== false){        
        return $ok;
    }//private function createTable(){

    //retrieve table row specifing an index
    public function getData($index){
        $get = false;
        $this->errno = 0;
        if(in_array($index,Article::$campi)){
            $this->query = <<<SQL
SELECT * FROM `{$this->table}` WHERE `{$index}` = '{$this->{$index}}';
SQL;
            $this->queries[] = $this->query;
            $res = $this->h->query($this->query);
            if($res !== false){
                if($res->num_rows == 1){
                    $row = $res->fetch_assoc();
                    $this->id = $row["id"];
                    $this->title = $row["title"];
                    $this->author = $row["author"];
                    $this->permalink = $row["permalink"];
                    $this->content = $row["content"];
                    $this->introtext = $row["introtext"];
                    $this->categories = $row["categories"];
                    $this->tags = $row["tags"];
                    $this->creation_time = $row["creation_time"];
                    $this->last_modified = $row["last_modified"];
                    $get = true;
                }
                else{
                    $this->errno = ARTICLE_NORESULT;
                }
                $res->free_result();
            }//if($res !== false){
            else{
                $this->errno = ARTICLE_QUERYERROR;
            }
        }//if(in_array($index,ARTICLE::$campi)){
        else{
            $this->errno = ARTICLE_INVALIDINDEX;
        }
        return $get;
    }//private function getData($index){
}


?>