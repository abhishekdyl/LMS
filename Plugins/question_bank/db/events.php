<?php


$observers = array(
    
    array(
        'eventname' => '\qbank_comment\event\comment_created',
        'includefile' => 'local/question_bank/observer.php',
        'callback' => 'comment_created',
        'internal' => false 
     ), 
    array(
        'eventname' => '\core\event\question_created',
        'includefile' => 'local/question_bank/observer.php',
        'callback' => 'question_created',
        'internal' => false 
     ),
)



?>