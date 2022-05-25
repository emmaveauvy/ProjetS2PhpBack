<?php

require_once('init.php');

function getPlayer($id) {
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM players WHERE id = ?");

    $sth->execute(array($id));

    return $sth->fetch(PDO::FETCH_ASSOC);
}

function addPlayer($name, $id_quiz) {
    $PDO = getPDO();
    $sth = $PDO->prepare("INSERT INTO players(name, id_quiz, score) values( ?, ?, ?)");
	$sth->execute(array($name, $id_quiz, 0));

    return intval($PDO->lastInsertId());//return l'id du player
}

function listPlayer() {
    $PDO = getPDO();
    $sth = $PDO->query("SELECT name, score FROM players");

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

function deletePlayer($id){
    $PDO = getPDO();
    $sth = $PDO->prepare("DELETE FROM players WHERE id = ?");
    $sth->execute(array($id));
}

function updateScore($id, $idQuestion, $idAnswer){
    //recup response
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT id FROM responses WHERE (isTrue = true and id_questions = ?)");
    $sth->execute(array($idQuestion));

    $data = $sth->fetchAll(PDO::FETCH_ASSOC);

    if($data[0]['id'] != $idAnswer){
        return;
    }
    
    $sth = $PDO->prepare("SELECT time FROM questions WHERE (id = ?)");
    $sth->execute(array($idQuestion));

    $data = $sth->fetchAll(PDO::FETCH_ASSOC);

    $delta = strtotime(date('Y-m-d h:i:s')) - strtotime($data[0]['time']);

    $score = 30 - $delta; //points max = 30, min = 15 avec réponse juste

    if($score < 15 || $score > 30){
        return;
    }

    $sth = $PDO->prepare("SELECT score FROM players WHERE (id = ?)");
    $sth->execute(array($id));
    $data = $sth->fetchAll(PDO::FETCH_ASSOC);
    $currentScore = $data[0]["score"];

    $sth = $PDO->prepare("UPDATE players SET score = ? WHERE id = ?"); 
    $sth->execute(array(intval($currentScore) + intval($score), $id));

    return($sth->fetchAll(PDO::FETCH_ASSOC));
}

function verifName($name, $idquiz){
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM players WHERE name = ? AND id_quiz = ?");
    $sth->execute(array($name, $idquiz));

    if($sth->fetchAll(PDO::FETCH_ASSOC)){
        return true;
    }
    return false;
}

function deletePlayers($id_quiz){
    $PDO = getPDO();
    $sth = $PDO->prepare("DELETE FROM players WHERE id_quiz = ?");
    $sth->execute(array($id_quiz));

    return true;
}

?>