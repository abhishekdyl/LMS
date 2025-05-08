<?php

class local_custom_redirect_eventsobserver
{
    //Users observers
    public static function user_created(\core\event\user_created $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
    public static function user_deleted(\core\event\user_deleted $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
    public static function user_password_updated(\core\event\user_password_updated $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
    public static function user_updated(\core\event\user_updated $event)
    {   
        global $CFG;
        $event_data = $event->get_data();

        // echo "<pre>";
        // var_dump(json_encode($event_data));
        // echo $event_data['relateduserid'];
        // die();
        redirect($CFG->wwwroot.'/blocks/mcdean_profile/index.php?id='.$event_data['relateduserid']);
        die();
    }
    
    
    //Chapter observers
    public static function chapter_created(\mod_book\event\chapter_created $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
    public static function chapter_deleted(\mod_book\event\chapter_deleted $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
    public static function chapter_updated(\mod_book\event\chapter_updated $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
    //Course observers
    public static function course_created(\core\event\course_created $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
    public static function course_deleted(\core\event\course_deleted $event)
    {
        $event_data = $event->get_data();
        // var_dump($event_data);
        // die();
    }
    
    public static function course_module_created(\core\event\course_module_created $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
    public static function course_module_deleted(\core\event\course_module_deleted $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
    public static function course_module_updated(\core\event\course_module_updated $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
    public static function course_restored(\core\event\course_restored $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
    public static function course_updated(\core\event\course_updated $event)
    {
        $event_data = $event->get_data();
        // var_dump(json_encode($event_data));
        // die();
    }
    
}