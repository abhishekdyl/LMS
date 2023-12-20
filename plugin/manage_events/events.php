<?php
require_once('../../config.php');
require_once('events_form.php');
require_login();

global $DB, $CFG, $USER;
if(is_siteadmin()){

$editid = optional_param('edit', 0, PARAM_INT);

$users = $DB->get_records('user',array());
$groups = $DB->get_records('utrains_groups',array("deleted" => 0));
$events = $DB->get_records('event',array());

$formoptions = new stdClass;
$formoptions->eventtype->users = $users;
$formoptions->eventtype->groups = $groups;
$formoptions->event = $events;
$formoptions->hasduration = ($events->timeduration > 0);

$mform  = new events_form(null, $formoptions);

if(!empty($editid)){
	if($data = $DB->get_record("manual_events", array("id"=>$editid))){
        $data->description = array("text"=>$data->description,"format"=>$data->format);
        $data->repeats = $data->repeatid;
        $data->timedurationuntil = ($data->timeduration == 0) ? $data->timeduration : ($data->timeduration + $data->timestart);
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die;
		$mform->set_data($data);
	}
}
$eventlog = new stdClass();
if($mform->is_cancelled()) {
    redirect($CFG->wwwroot."/local/manage_events/index.php");
} else if($data = $mform->get_data()){ 
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die;

    if(!empty($data->id)){
    
            $eventlog->id = $data->id;
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

            $formaction_id = $DB->update_record('manual_events',$eventlog);  
            $sql_delete = 'SELECT mev.id as mid, e.* FROM "mdl_manual_events_list" mev INNER JOIN "mdl_event" e ON mev.eid = e.id WHERE mev.meid = '.$data->id.''; 
            $bbb = $DB->get_records_sql($sql_delete,array());  
            foreach ($bbb as $key) {
                $DB->delete_records('event',array("id"=>$key->id));
                $DB->delete_records('manual_events_list',array("id"=>$key->mid));
            }

            for ($i=0; $i < $data->repeats ; $i++) {
                if($i > 0){
                    $eventlog->timestart = strtotime("+1 day", $eventlog->timestart);
                }
                $evid = $DB->insert_record('event',$eventlog);  
                $DB->insert_record('manual_events_list', (object)array("meid"=>$data->id, "eid"=>$evid));  
            }
        
        redirect($CFG->wwwroot."/local/manage_events/index.php", null, "Event Created");

    }else{
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
            
        $formactionid = $DB->insert_record('manual_events',$eventlog);    
       
        for ($i=0; $i < $data->repeats ; $i++) {
            if($i > 0){
                $eventlog->timestart = strtotime("+1 day", $eventlog->timestart);
            }
            $evid = $DB->insert_record('event',$eventlog);  
            $DB->insert_record('manual_events_list', (object)array("meid"=>$formactionid, "eid"=>$evid));  
        }
    
        redirect($CFG->wwwroot."/local/manage_events/index.php", null, "Event Created");
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
    }
    
}


echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();


}

?>