<?php

function getPDO() {

    require_once(__DIR__.'/../config.php');

    try {
        $PDO = new PDO(
            'mysql:host=localhost;dbname='.$NAME_DB.';charset=utf8',
            $USER_DB,
            $PASSWORD_DB
        );
    }
    catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }

    return $PDO;
}


?>