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

/*$small->post('/user', function($request, $response) {

    
    $data = addUser($_POST);
    $response->setData($data);
    
    return $response;
});*/


$small->post('/user', function($request, $response) {

    
    $response->setResponseType('HTML');
    $user = $request->params['user'];
    $response->setData('<p>Hello '.$user.'</p>');
    
    return $response;
    
});






$small->get('user/{id}', function($request, $response) {
    $id = explode('/',$_SERVER['REQUEST_URI']);
    $data = getUser($id[2]);
    $response->setData($data);

    return $response;
});




$small->req('user/{id}', 'delete', function($request, $response) {

    $id = explode('/',$_SERVER['REQUEST_URI']);
    $data = deleteUser($id[2]);
    $response->setData($data);

    return $response;
});