<?php

namespace App\Api;

use PDO;
use App\SQLiteConnection;

$url = $_SERVER['REQUEST_URI'];

$dbInstance = new SQLiteConnection();
$dbConn = $dbInstance->connect();

if($url == '/polls' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $polls = getAllPolls($dbConn);
    echo json_encode($polls);
}

if($url == '/polls' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    //TODO
}

if(preg_match("/polls\/([0-9]+)\/answers/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'GET'){
    $pollId = $matches[1];
    $poll = getAnswers($dbConn, $pollId);
    echo json_encode($poll, JSON_NUMERIC_CHECK);
} elseif (preg_match("/polls\/([0-9]+)/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'GET'){
    $pollId = $matches[1];
    $poll = getPoll($dbConn, $pollId);

    echo json_encode($poll);
}

if(preg_match("/polls\/([0-9]+)/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'PUT'){
    //TODO
}

if(preg_match("/polls\/([0-9]+)/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'DELETE'){
    //TODO
}

function getPoll($db, $id) {
    $statement = $db->prepare("SELECT * FROM polls where id=:id");
    $statement->bindValue(':id', $id);
    $statement->execute();

    return $statement->fetch(PDO::FETCH_OBJ);
}

function getAnswers($db, $id) {
    $statement = $db->prepare("SELECT answers.id, answers.name, answers.poll_id, answers.votes 
      FROM answers INNER JOIN polls ON polls.id = answers.poll_id 
      WHERE polls.id=:id");
    $statement->bindValue(':id', $id);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getAllPolls($db) {
    $statement = $db->prepare("SELECT * FROM polls");
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}