<?php
$observers = array(
    array(
        'eventname' => '\mod_assign\event\assessable_submitted',
        'includefile' => '/local/assignment_subscription/classes/eventsobserver.php',
        'callback'  => 'local_assignment_subscription_eventsobserver::assessable_submitted',
    )
);
