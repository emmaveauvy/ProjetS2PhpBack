<?php

function login($mail, $password) {
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM creators WHERE (mail = :mail) AND (password = :password)");
    $sth->execute(array('mail' => $mail, 'password' => $password));
    return $sth->fetch(PDO::FETCH_ASSOC);
}

function isConnected($request) {
    $data=login($request->cookies['mail'], $request->cookies['password']);
    return $data;
}