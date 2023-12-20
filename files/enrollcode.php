<?php 


$query = 'SELECT * FROM {enrol} WHERE enrol = "manual" AND courseid = '.$id;
    $enrollmentID = $DB->get_record_sql($query);
    if(!empty($enrollmentID->id)) {
        if (!$DB->record_exists('user_enrolments', array('enrolid'=>$enrollmentID->id, 'userid'=>$USER->id))) {
            $timestart  = time(); 
            $timeend = 0; 
            $enrol_manual = enrol_get_plugin('manual');
            $enrol_manual->enrol_user($enrollmentID, $USER->id, 3, time(), 0);
            add_to_log($id, 'course', 'enrol', '../enrol/users.php?id='.$id, $id, $USER->id); //there should be userid somewhere!
        }
    }