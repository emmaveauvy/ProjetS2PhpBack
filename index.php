<?php

require_once('./small-php/small/small.php');

$small = new Small();

$small->get('/', function($request, $response) {

    $response->setData(['message'=>'Hello World']);
    
    return $response;
});