<?php

require_once('init.php');

function getUser($id) {
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM creators WHERE id = :id");
    $sth->execute(array('id' => $id));

    return ['message' => $sth->fetch(PDO::FETCH_ASSOC)];
}

function addUser($name, $mail, $passeword){
    $PDO = getPDO();
    $sth = $PDO->prepare("INSERT INTO creators(name, mail, passeword) values( ?, ?, ? )");
	$sth->execute(array($name, $mail, $passeword));
	return listUser();
}

function listUser(){
    $PDO = getPDO();
    $sth = $PDO->query("SELECT * FROM creators");
    return $sth->fetchall();
}

function deleteUser($id){
    $PDO = getPDO();
    $sth = $PDO->prepare("DELETE * FROM creators WHERE id = :id");
    $sth->execute(array('id' => $id));
    return listUser();

}