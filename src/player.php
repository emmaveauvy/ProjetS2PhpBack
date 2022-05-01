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

function addScore($id, $score) {
    $PDO = getPDO();
    $sth = $PDO->prepare("UPDATE players SET score = (SELECT score FROM players WHERE (id = :id)) + :score WHERE (id = :id)");//pas sûr que ça fonctionne

    $sth->execute(array('id' => $id, 'score' => $score));
}

function deletePlayer($id){
    $PDO = getPDO();
    $sth = $PDO->prepare("DELETE FROM players WHERE id = ?");
    $sth->execute(array($id));
}

?>