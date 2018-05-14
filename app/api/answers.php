<?php

namespace App\Api;

use PDO;
use App\SQLiteConnection;

$url = $_SERVER['REQUEST_URI'];

$dbInstance = new SQLiteConnection();
$dbConn = $dbInstance->connect();

if(preg_match("/answers\/([0-9]+)/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $votes = $_POST['votes'];
    $answerId = $matches[1];
    updateAnswer($votes, $dbConn, $answerId);

    $answer = getAnswer($dbConn, $answerId);
    echo json_encode($answer);
}

if(preg_match("/answers\/([0-9]+)/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'GET'){
    $answerId = $matches[1];
    $answer = getAnswer($dbConn, $answerId);

    echo json_encode($answer);
}

function getAnswer($db, $id) {
    $statement = $db->prepare("SELECT * FROM answers where id=:id");
    $statement->bindValue(':id', $id);
    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

function bindAllValuesAnswer($statement, $params){
    $allowedFields = ['votes'];

    foreach($params as $param => $value){
        if(in_array($param, $allowedFields)){
            $statement->bindValue(':'.$param, $value);
        }
    }

    return $statement;
}

function getParamsAnswer($input) {
    $allowedFields = ['votes'];

    $filterParams = [];
    foreach($input as $param => $value){
        if(in_array($param, $allowedFields)){
            $filterParams[] = "$param=:$param";
        }
    }

    return implode(", ", $filterParams);
}

function updateAnswer($votes, $db, $answerId){
    $sql = "
          UPDATE answers 
          SET votes=:votes 
          WHERE id=:id
           ";

    $statement = $db->prepare($sql);
    $statement->bindValue(':id',$answerId);
    $statement->bindValue(':votes',$votes);
    //bindAllValuesAnswer($statement, $input);

    $statement->execute();

    return $answerId;
}