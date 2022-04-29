<?php

require_once('init.php');

function login($mail, $password) {
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM creators WHERE (mail = :mail) AND (password = :password)");
    $sth->execute(array('mail' => $mail, 'password' => $password));
    return $sth->fetch(PDO::FETCH_ASSOC);
}

function getUser($id) {
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM creators WHERE id = :id");

    $sth->execute(array('id' => $id));

    return $sth->fetch(PDO::FETCH_ASSOC);
}

function addUser($name, $mail, $password) {
    //verification si mail est déjà dans BDD
    $PDO = getPDO();
    $sth = $PDO->prepare("INSERT INTO creators(name, mail, password) values( ?, ?, ? )");
	$sth->execute(array($name, $mail, $password));
    

	return listUser();
}

function verifMail($mail){
    $PDO = getPDO();
    $sth= $PDO->prepare("SELECT * FROM creators WHERE mail = :mail");
    $sth->execute(array('mail' => $mail));

    return $sth->fetch(PDO::FETCH_ASSOC);
}

function listUser() {
    $PDO = getPDO();
    $sth = $PDO->query("SELECT * FROM creators");

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

function deleteUser($id) {
    $PDO = getPDO();
    $sth = $PDO->prepare("DELETE FROM creators WHERE id = :id");
    $sth->execute(array('id' => $id));

    return listUser();
}