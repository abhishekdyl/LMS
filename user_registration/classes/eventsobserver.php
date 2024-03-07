<?php



class local_user_registration_eventsobserver
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
        // redirect($CFG->wwwroot.'/blocks/mcdean_profile/index.php?id='.$event_data['relateduserid']);
        // die();
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
    global $CFG;
    require ($CFG->dirroot.'/local/user_registration/qbk/apiCall.php');
 
    $restobj = new stdClass();
    $restobj->fullname = $event->other['fullname'];
    $restobj->description = $_POST['summary_editor']['text'];
    $restobj->price = $_POST['customfield_price'];
    $taxprice = ($_POST['customfield_price'] + ($_POST['customfield_price']/10)); // To add 10% amount of tax amount 
    $restobj->taxprice = $taxprice;
    $restobj->starttime = time();
        
    $item = makeAPICall('createItem', ['itemName' => $event->other['shortname'],'restData'=> $restobj]);
    echo "<pre>-------------";
    print_r($item);
    echo "</pre>";
    die;

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