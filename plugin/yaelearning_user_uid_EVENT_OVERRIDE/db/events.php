<?php

$observers = array(
    
    array(
        'eventname' => '\core\event\user_created',
        'includefile' => 'local/yaelearning_user_uid/observer.php',
        'callback' => 'user_created',
        'internal' => false 
     ),
)

?>