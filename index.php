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

    $data=listUser();
    $response->setData($data);
    
    return $response;
});

$small->post('/user', function($request, $response) {

    $password = md5($request->params['password']);
    $data = addUser($request->params['name'], $request->params['mail'], $password);
    $response->setData($data);
    
    return $response;
});

$small->get('user/{id}', function($request, $response) {
    
    $data = getUser($request->resource['id']);
    $response->setData($data);

    return $response;
});

$small->req('user/{id}', 'delete', function($request, $response) {

    $data = deleteUser($request->resource['id']);
    $response->setData($data);

    return $response;
});