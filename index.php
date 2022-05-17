<?php
//framework
require_once('./small-php/small/small.php');

//fichier src
require_once('./src/auth.php');
require_once('./src/user.php');
require_once('./src/quiz.php');
require_once('./src/player.php');


$small = new Small();

$small->get('/', function($request, $response) {

    $response->setData(['message'=>'pong']);
    
    return $response;
});

// AUTH

$small->post('/login', function($request, $response) {

    $data=login($request->params['mail'], md5($request->params['password']));
    if($data==false) {
        $response->setData(['error'=>"Erreur d'identification"]);
        $response->setResponseCode(403); 
    }else {
        $response->setCookie('mail', $data['mail']);
        $response->setCookie('password', $data['password']);
    }
    
    return $response;
});

$small->get('/me', function($request, $response) {

    $data=login($request->cookies['mail'], $request->cookies['password']);
    if($data==false) {
        $response->setData(['error'=>"Utilisateur non reconnu"]);
        $response->setResponseCode(403); 
    }else {
        $response->setData($data);
    }
    
    return $response;
});

$small->get('/logout', function($request, $response) {

    $response->setCookie('mail', 'rine', 1);
    $response->setCookie('password', 'rine', 1);
    
    return $response;
});

// USER

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
        $response->setCookie('mail', $data['mail']);
        $response->setCookie('password', $data['password']);
    }else{
        $response->setData(['error'=>'Un utilisateur est déjà enregistré avec ce mail']);
        $response->setResponseCode(404); 
    }
    return $response;

});

$small->get('/user/{id}', function($request, $response) {
    
    $data = getUser($request->resource['id']);
    $response->setData($data);
    
    //Verification si l'utilisateur avec cet ID existe
    if($data==false){
        $response->setData(['error'=>"L'utilisateur n'existe pas"]);
        $response->setResponseCode(404);    
    }

    return $response;
});

$small->req('/user/{id}', 'delete', function($request, $response) {

    //Verification si l'utilisateur avec cet ID existe
    $data = getUser($request->resource['id']);
    $response->setData($data);
    
    if($data==false){
        $response->setData(['error'=>"L'utilisateur n'existe pas"]);
        $response->setResponseCode(404);    
    }else{
        $data = deleteUser($request->resource['id']);
        $response->setData($data);
    }

    return $response;
});

//PLAYER

$small->post('/player', function($request, $response) {
    if(!verifName($request->params['name'])){
        $data = addPlayer($request->params['name'], $request->params['quizcode']);
        $response->setData($data);

    }else{
        $response->setData(['error'=>'Un joueur a déjà ce pseudo']);
        $response->setResponseCode(404);
    }
    return $response;    
});

// QUIZ

$small->get('/quiz', function($request, $response) {

    //return the user or false
    $user = isConnected($request);
    if(!$user) {
        $response->setData(['error'=>"L'utilisateur n'est pas connecté"]);
        $response->setResponseCode(403);
        return $response;
    }
    
    $data = listQuiz($user['id']);
    $response->setData($data);

    return $response;
});

$small->get('/quiz/{code}', function($request, $response) {
    
    $data = getQuiz($request->resource['code']);
    
    if($data==false){
        $response->setData(['error'=>"Le quiz n'existe pas"]);
        $response->setResponseCode(404);    
    } else {
        $response->setData($data);
    }

    return $response;
});

$small->req('/quiz/update', 'put', function($request, $response) {
    
    $data = updateQuiz($request->params['quizcode'], $request->params['name'], $request->params['questions']);

    if(!$data){
        $response->setData(['error'=>"Erreur dans la mise à jour du quiz"]);
        $response->setResponseCode(404);
    }else{
        $response->setData($data);
    }

    return $response;
});

//Pour l'utilisateur qui présente
$small->get('/question/{quizCode}', function($request, $response) {

    //return the user or false
    $user = isConnected($request);
    if(!$user) {
        $response->setData(['error'=>"L'utilisateur n'est pas connecté"]);
        $response->setResponseCode(403);
        return $response;
    }
    
    $data = getQuestions($request->resource['quizCode']);
    
    if($data==false){
        $response->setData(['error'=>"Le quiz n'existe pas"]);
        $response->setResponseCode(404);    
    } else {
        $response->setData($data);
    }

    return $response;
});

//Pour l'utilisateur qui joue
$small->get('/answers/{quizCode}', function($request, $response) {
    
    $data = getAnswers($request->resource['quizCode']);
    
    if($data==false){
        $response->setData(['error'=>"Le quiz n'existe pas"]);
        $response->setResponseCode(404);    
    } else {
        $response->setData($data);
    }

    return $response;
});

$small->post('/quiz', function($request, $response) {

    //return the user or false
    $user = isConnected($request);
    if(!$user) {
        $response->setData(['error'=>"L'utilisateur n'est pas connecté"]);
        $response->setResponseCode(403);
        return $response;
    }
    
    $data = addQuiz($request->params['name'], $request->params['questions'], $user['id']);

    $response->setData($data);

    return $response;

});

$small->req('/question/start', 'put', function($request, $response) {//question start
    
    $data = setTime($request->params['questionId'], time());

    $response->setData($data);

    return $response;

});

$small->req('/question/end', 'put', function($request, $response) {//question end
    
    $data = setTime($request->params['questionId'], 0);

    $response->setData($data);

    return $response;

});

$small->req('/score', 'put', function($request, $response) {
    
    $data = updateScore($request->params['playerId'], $request->params['idQuestion']);

    $response->setData($data);

    return $response;

});

$small->get('/score/{quizId}', function($request, $response) {
    
    $data = getTableauScore($request->resource['quizId']);

    if(!$data){
        $response->setData(['error'=>"Le quiz n'existe pas"]);
        $response->setResponseCode(404);
    }else{
        $response->setData($data);
    }

    return $response;
});
