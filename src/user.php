<?php

require_once('init.php');

function getUser() {
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM creators WHERE id = :id");
    $sth->execute(array('id' => 1));

    return ['message' => $sth->fetch(PDO::FETCH_ASSOC)];
}