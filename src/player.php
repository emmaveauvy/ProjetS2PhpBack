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
    $sth = $PDO->prepare("INSERT INTO players(name, id_quiz) values( ?, ?)");
	$sth->execute(array($name, $id_quiz));
}

function listPlayer() {
    $PDO = getPDO();
    $sth = $PDO->query("SELECT name, score FROM players");

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

function gePlayer($id) {
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT name, score FROM players WHERE (id = ?)");
    $sth->execute(array($id));

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

// function addScore($id, $score) {
//     $PDO = getPDO();
//     $sth = $PDO->prepare("UPDATE players SET score = (SELECT score FROM players WHERE (id = :id)) + :score WHERE (id = :id)");//pas sûr que ça fonctionne

//     $sth->execute(array('id' => $id, 'score' => $score));
// }

function deletePlayer($id){
    $PDO = getPDO();
    $sth = $PDO->prepare("DELETE FROM players WHERE id = ?");
    $sth->execute(array($id));
}

//pas testée
function initScore($id){
    $PDO = getPDO();
    $sth = $PDO->prepare("INSERT INTO players(score) VALUES 0 WHERE id = ?");
    $sth->execute(array($id));
}

function updateScore($id, $time){
    $delta = date_diff($time, time());//return false si fail
    
    if($delta){
        $score = 30 - $delta; //points max = 30, min = 15 avec réponse juste
        
        $PDO = getPDO();

        $currentScore = $PDO->query("SELECT score FROM players WHERE (id = $id)");

        $sth = $PDO->prepare("UPDATE score SET score = ? WHERE id = ?");
        $sth->execute(array($currentScore + $score, $id));

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    return false;
}

?>