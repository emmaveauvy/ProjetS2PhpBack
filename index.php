<?php
//framework
require_once('./small-php/small/small.php');

//fichier src
require_once('./src/user.php');


$small = new Small();

$small->get('/', function($request, $response) {

    $response->setData(['message'=>'pong']);
    
    return $response;
});

$small->get('/user', function($request, $response) {

    $data = getUser();

    $response->setData(['message'=>$data]);
    
    return $response;
});