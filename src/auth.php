<?php

function login($mail, $password) {
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM creators WHERE (mail = :mail) AND (password = :password)");
    $sth->execute(array('mail' => $mail, 'password' => $password));
    return $sth->fetch(PDO::FETCH_ASSOC);
}