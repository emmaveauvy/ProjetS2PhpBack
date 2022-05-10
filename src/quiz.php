<?php

require_once('init.php');

function addQuiz($name, $questions, $id_creator) {
    $PDO = getPDO();
    do {
        $code = rand(1000,9999);
        $sth = $PDO->prepare("SELECT * FROM quiz WHERE (code = ?)");
        $sth->execute(array($code));
        $data = $sth->fetchAll(PDO::FETCH_ASSOC); 
    } while (count($data) != 0); // pas de quiz avec le même code
    
    $sth = $PDO->prepare("INSERT INTO quiz (name, id_creators, code) values (?, ?, ?)");
    $sth->execute(array($name, $id_creator, $code));

    $idQuiz = intval($PDO->lastInsertId());

    //questions
    foreach ($questions as $question){
        $sth = $PDO->prepare("INSERT INTO questions (title, id_quiz) values (?, ?)");
        $sth->execute(array($question['title'], $idQuiz));
        $idQuestion = intval($PDO->lastInsertId());
        
        //responses
        $a = array();
        for ($i=0; $i < count($question['answers']); $i++) {
            array_push($a, $question['answers'][$i]['value']);
            array_push($a, $idQuestion);
            array_push($a, $question['answers'][$i]['isTrue'] == true ? 1 : 0);
        }

        $sth = $PDO->prepare("INSERT INTO responses (title, id_questions, isTrue) values (?, ?, ?), (?, ?, ?), (?, ?, ?), (?, ?, ?)");
        $sth->execute($a);
        
    }

    return true;
}

function updateQuiz($code, $name, $questions) {
    $PDO = getPDO();
    
    $sth = $PDO->prepare("UPDATE quiz SET name = ? WHERE code = ?");
    $sth->execute(array($name, $code));

    //questions
    foreach ($questions as $question){
        $sth = $PDO->prepare("UPDATE questions SET title = ? WHERE id = ?");
        $sth->execute(array($question['title'], $question['id']));
        
        //responses
        for ($i=0; $i < count($question['answers']); $i++) {
            $a = array(); 
            array_push($a, $question['answers'][$i]['value']);
            array_push($a, $question['answers'][$i]['isTrue'] == true ? 1 : 0);
            array_push($a, $question['answers'][$i]['id']);
            $sth = $PDO->prepare("UPDATE responses SET title = ?, isTrue = ? WHERE id = ?");
            $sth->execute($a);
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

function getAnswers($quizCode){
    $questions = getQuestions($quizCode);

    //set all answers to 0
    for ($i=0; $i < count($questions); $i++) {
        for ($j=0; $j < count($questions[$i]['answers']); $j++) { 
            $questions[$i]['answers'][$j]["isTrue"] = "0";
        }
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

function setTime($id, $time){
    $PDO = getPDO();
    $sth = $PDO->prepare("UPDATE questions SET datetime = ? WHERE (id = ?)");
    $sth->execute(array($time, $id));

    return true;
}

function getTableauScore($id) {
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT name, score FROM players WHERE id_quiz=? ORDER BY score DESC LIMIT 0,5");
    $sth->execute(array($id));

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

?>