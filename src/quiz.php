<?php

require_once('init.php');

function addQuiz($name, $id_creator){
    //vérif connexion à un compte user
    $data=login($request->cookies['mail'], $request->cookies['password']);
    if($data==false) {
        $response->setData(['error'=>"Utilisateur non reconnu"]);
        $response->setResponseCode(403); 
    }else {
        $response->setData($data);
        $PDO = getPDO();
        $sth = $PDO->prepare("INSERT INTO quiz (name, id_creators) VALUES (?, ?)");
        $sth->execute(array($name, $id_creator));
    }
}

function renameQuiz($id, $name){
    $PDO = getPDO();
    $sth = $PDO->prepare("UPDATE quiz SET name = ? WHERE (id = ?)");
    $sth->execute(array($name, $id));
}

function getQuiz($id){
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM quiz WHERE (id = ?)");
    $sth->execute(array($id));

    return $sth->fetchAll(PDO::FETCH_ASSOC);  
}

function listQuiz($id_creator){
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT name, code FROM quiz WHERE (id_creators = ?)");
    $sth->execute(array($id_creator));

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

function deleteQuiz($id){
    $PDO = getPDO();
    $sth = $PDO->prepare("DELETE FROM quiz WHERE id = ?");
    $sth->execute(array($id));
}

?>