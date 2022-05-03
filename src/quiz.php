<?php

require_once('init.php');

function addQuiz($name, $questions, $id_creator) {
    var_dump($questions);
    $PDO = getPDO();
    do {
        $code = rand(1000,9999);
        $sth = $PDO->prepare("SELECT * FROM quiz WHERE (code = ?)");
        $sth->execute(array($code));
        $data = $sth->fetchAll(PDO::FETCH_ASSOC); 
    } while (count($data) != 0); // pas de quiz avec le même code
    
    $sth = $PDO->prepare("INSERT INTO quiz (name, id_creators, code) values (?, ?, ?)");
    $sth->execute(array($name, $id_creator, $code));

    $quiz = getQuiz($code);

    //questions
    foreach ($questions as $question){
        $sth = $PDO->prepare("INSERT INTO questions (title, id_quiz) values (?, ?)");
        $sth->execute(array($question['title'], intval($quiz[0]['id'])));

        //get question
        $sth = $PDO->prepare("SELECT id FROM questions WHERE (title = ?) AND (id_quiz = ?)");
        $sth->execute(array($question['title'], intval($quiz[0]['id'])));
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        $idQuestion = intval($data[0]['id']);

        //responses
        foreach ($question['answers'] as $answer){
            $sth = $PDO->prepare("INSERT INTO responses (title, id_questions, isTrue) values (?, ?, ?)");
            $sth->execute(array($answer['value'], $idQuestion, $answer['isTrue']));
        }
    }

    return true;
}

function renameQuiz($id, $name){
    $PDO = getPDO();
    $sth = $PDO->prepare("UPDATE quiz SET name = ? WHERE (id = ?)");
    $sth->execute(array($name, $id));
}

function getQuiz($code){
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM quiz WHERE (code = ?)");
    $sth->execute(array($code));

    return $sth->fetchAll(PDO::FETCH_ASSOC);  
}

function getQuestions($quizCode){
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM quiz WHERE (code = ?)");
    $sth->execute(array($quizCode));
    $quiz = $sth->fetchAll(PDO::FETCH_ASSOC);

    //get questions
    $sth = $PDO->prepare("SELECT * FROM questions WHERE (id_quiz = ?)");
    $sth->execute(array(intval($quiz[0]['id'])));
    $questions = $sth->fetchAll(PDO::FETCH_ASSOC);

    for ($i=0; $i < count($questions); $i++) {
        //get answers
        $sth = $PDO->prepare("SELECT * FROM responses WHERE (id_questions = ?)");
        $sth->execute(array(intval($questions[$i]['id'])));
        $questions[$i]["answers"] = $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    return $questions;
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