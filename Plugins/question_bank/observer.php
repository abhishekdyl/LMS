<?php

function comment_created(\qbank_comment\event\comment_created $event){
	global $DB, $USER,$CFG;
    $smtp_settings = core_user::get_support_user();
    $sql = 'SELECT u.* FROM {role} r JOIN {role_assignments} ra on r.id = ra.roleid JOIN {user} u on ra.userid = u.id WHERE r.shortname = "approver"';
    $appuser = $DB->get_records_sql($sql);
    foreach ($appuser as $key => $approval) {
        $messagehtml = get_config('local_question_bank','commentQuestionContent').'<br/>
        link : '.$CFG->wwwroot.'/local/question_bank/preview.php?id='.$event->other["itemid"];

        $fromUser = $smtp_settings->email;
        $subject = get_config('local_question_bank','commentQuestionSubject');
        $emailuser = new stdClass();
        $emailuser->email = $approval->email;
        $emailuser->firstname = $approval->firstname;
        $emailuser->lastname= $approval->lastname;
        $emailuser->maildisplay = true;
        $emailuser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
        $emailuser->id = $approval->id;
        $emailuser->firstnamephonetic = false; 
        $emailuser->lastnamephonetic = false;
        $emailuser->middlename = false;
        $emailuser->username = false;
        $emailuser->alternatename = false;
        email_to_user($emailuser,$fromUser, $subject, $message = '', $messagehtml);

    }
}
function question_created(\core\event\question_created $event){
	global $DB, $USER,$CFG;
    $smtp_settings = core_user::get_support_user();
    $sql = 'SELECT u.* FROM {role} r JOIN {role_assignments} ra on r.id = ra.roleid JOIN {user} u on ra.userid = u.id WHERE r.shortname = "reviewer"';
    $revuser = $DB->get_records_sql($sql);
    foreach ($revuser as $key => $reviewers) {

        $messagehtml = get_config('local_question_bank','createQuestionContent').'<br/>
        link : '.$CFG->wwwroot.'/local/question_bank/preview.php?id='.$event->objectid;

        $fromUser = $smtp_settings->email;
        $subject =  get_config('local_question_bank','createQuestionSubject');
        $emailuser = new stdClass();
        $emailuser->email = $reviewers->email;
        $emailuser->firstname = $reviewers->firstname;
        $emailuser->lastname= $reviewers->lastname;
        $emailuser->maildisplay = true;
        $emailuser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
        $emailuser->id = $reviewers->id;
        $emailuser->firstnamephonetic = false;
        $emailuser->lastnamephonetic = false;
        $emailuser->middlename = false;
        $emailuser->username = false;
        $emailuser->alternatename = false;
        email_to_user($emailuser,$fromUser, $subject, $message = '', $messagehtml);

    }
}





?>