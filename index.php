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

    $data=verifMail($request->params['mail']);
    $response->setData($data);

    if($data==false){
        $password = md5($request->params['password']);
        $data = addUser($request->params['name'], $request->params['mail'], $password);
        $response->setData($data);
    }else{
        $response->setData('<p>This mail has already be registred</p>');
        $response->setResponseCode(404); 
    }
    return $response;

});

$small->get('user/{id}', function($request, $response) {
    
    $data = getUser($request->resource['id']);
    $response->setData($data);
    
    //Verification si l'utilisateur avec cet ID existe
    if($data==false){
        $response->setData('<p>No user with this ID</p>');
        $response->setResponseCode(404);    
    }

    return $response;
});

$small->req('user/{id}', 'delete', function($request, $response) {

    //Verification si l'utilisateur avec cet ID existe
    $data = getUser($request->resource['id']);
    $response->setData($data);
    
    if($data==false){
        $response->setData('<p>No user with this ID</p>');
        $response->setResponseCode(404);    
    }else{
        $data = deleteUser($request->resource['id']);
        $response->setData($data);
    }

    return $response;
});