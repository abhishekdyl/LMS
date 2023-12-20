<?php
require_once('../../config.php');
require_once('events_form.php');
global $DB, $CFG, $USER;

$users = $DB->get_records('user',array());
$groups = $DB->get_records('utrains_groups',array("deleted" => 0));
$events = $DB->get_records('event',array());

$formoptions = new stdClass;
$formoptions->eventtype->users = $users;
$formoptions->eventtype->groups = $groups;
$formoptions->event = $events;
$formoptions->hasduration = ($events->timeduration > 0);

$mform  = new events_form(null, $formoptions);
if($mform->is_cancelled()) {
    redirect($CFG->wwwroot."/local/manage_events/index.php");
} else if($data = $mform->get_data()){
    $eventlog = new stdClass();
   
    $data = $mform->get_data();
        $eventlog->name = $data->name;
        $eventlog->description = $data->description['text'];
        $eventlog->format = $data->description['format'];
        if(!empty($data->userid)){
            $eventlog->userid = $data->userid;
        }
        if(!empty($data->groupid)){
            $eventlog->groupid = $data->groupid;
        }
        $eventlog->repeatid = $data->repeats;
        $eventlog->eventtype = $data->eventtype;
        $eventlog->timestart = $data->timestart;
        if($data->duration > 0){
            $eventlog->timeduration = $data->timedurationuntil- $data->timestart;
        }else{
            $eventlog->timeduration = $data->duration;
        }
        $eventlog->location = $data->location;
    // echo "<pre>";
    // print_r($eventlog);
    // echo "</pre>";
    //     die;
        
    $formactionid = $DB->insert_record('manual_events',$eventlog);    
   
    for ($i=0; $i < $data->repeats ; $i++) {
        if($i > 0){
            $eventlog->timestart = strtotime("+1 day", $eventlog->timestart);
        }
        $evid = $DB->insert_record('event',$eventlog);  
        $DB->insert_record('manual_events_list', (object)array("meid"=>$formactionid, "eid"=>$evid));  
    }

    redirect($CFG->wwwroot."/local/manage_events/events.php", null, "Event Created");
}

echo $OUTPUT->header();
$mform->display();

echo $OUTPUT->footer();




?>