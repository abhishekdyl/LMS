<?php


defined('MOODLE_INTERNAL') || die();

    $observers = array(
   
        array(
            'eventname' => '\core\event\user_created',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback'  => 'local_user_registration_eventsobserver_eventsobserver::user_created',
        ),
        array(
            'eventname' => '\core\event\user_deleted',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver_eventsobserver::user_deleted',
        ),
        array(
            'eventname' => '\core\event\user_password_updated',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::user_password_updated',
        ),
        array(
            'eventname' => 'core\event\user_updated',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::user_updated',
        ),
        
        //Chapters
        array(
            'eventname' => '\mod_book\event\chapter_created',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::chapter_created',
        ),
        array(
            'eventname' => '\mod_book\event\chapter_deleted',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::chapter_deleted',
        ),
        array(
            'eventname' => '\mod_book\event\chapter_updated',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::chapter_updated',
        ),
        
        //Course
        array(
            'eventname' => '\core\event\course_created',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::course_created',
        ),
        array(
            'eventname' => '\core\event\course_deleted',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::course_deleted',
        ),
        array(
            'eventname' => '\core\event\course_module_created',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::course_module_created',
        ),
        array(
            'eventname' => '\core\event\course_module_deleted',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::course_module_deleted',
        ),
        array(
            'eventname' => '\core\event\course_module_updated',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::course_module_updated',
        ),
        array(
            'eventname' => '\core\event\course_restored',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::course_restored',
        ),
        array(
            'eventname' => '\core\event\course_updated',
            'includefile' => '/local/user_registration/classes/eventsobserver.php',
            'callback' => 'local_user_registration_eventsobserver::course_updated',
        ),
    );
