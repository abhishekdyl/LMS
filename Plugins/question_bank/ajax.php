<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/accesslib.php');
use qbank_editquestion\external\update_question_version_status;
global $DB,$CFG,$USER,$PAGE,$COURSE;
$smtp_settings = core_user::get_support_user();
$url = new moodle_url('/local/question_bank');
$PAGE->set_url($url);
$systemcontext = context_system::instance(); 
$PAGE->set_context($systemcontext);

if($_POST['action'] == 'removerole'){
    // $systemContext = context_system::instance();
    $courseContext = $_POST['ccid'];
    $userids = $_POST['id'];
    $role = $DB->get_record('role',array('shortname'=>'reviewer'));
    if($courseContext){
        role_unassign($role->id, $userids, $courseContext);
        $responsive = array(
            "status" => true,
            "msg" => 'Successfully remove the role.',
        );
    }else{
        $responsive = array(
            "status" => false,
            "msg" => 'error ! role has not removed.',
        );
    }
    echo json_encode($responsive);
    
}elseif($_POST['action'] == 'rejectreviewed'){ 
    $id = $_POST['id'];
    if($id){
        $locdata = $DB->get_record('local_qbquestions',array('questionid'=>$id));
        if($locdata){
            $obj = new stdClass();
            $obj->id = $locdata->id;    
            $obj->status = 'pending';
            $bbb = $DB->update_record('local_qbquestions',$obj);
            
            $locdata = $DB->get_record_sql('SELECT qb.id,qb.createdby,u.firstname,u.lastname,u.email FROM {local_qbquestions} qb JOIN {user} u ON qb.createdby = u.id WHERE qb.questionid = '.$id);
            if($locdata){
                $messagehtml = get_config('local_question_bank','rejectQuestionContent').'<br/>
                link : '.$CFG->wwwroot.'/local/question_bank/preview.php?id='.$id;
                $fromUser = $smtp_settings->email;
                $subject = get_config('local_question_bank','rejectQuestionSubject');
                $emailuser = new stdClass();
                $emailuser->email = $locdata->email;
                $emailuser->firstname = $locdata->firstname;
                $emailuser->lastname= $locdata->lastname;
                $emailuser->maildisplay = true;
                $emailuser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
                $emailuser->id = $locdata->createdby;
                $emailuser->firstnamephonetic = false;
                $emailuser->lastnamephonetic = false;
                $emailuser->middlename = false;
                $emailuser->username = false;
                $emailuser->alternatename = false;
                email_to_user($emailuser,$fromUser, $subject, $message = '', $messagehtml);
            }

            $response = array(
                "status" => true,
                "msg" => 'Your question rejected by reviewer',
            );
        }else{
            $response = array(
                "status" => false,
                "msg" => 'question is not exist in custom data.',
            );
        }
    }else{
        $response = array(
            "status" => false,
            "msg" => 'failed !!',
        );
    }
    echo json_encode($response);

}elseif($_POST['action'] == 'reviewed'){
    $id = $_POST['id'];
    if($id){
        $locdata = $DB->get_record('local_qbquestions',array('questionid'=>$id));
        if($locdata){
            $obj = new stdClass();
            $obj->id = $locdata->id;    
            $obj->status = 'reviewed';
            $bbb = $DB->update_record('local_qbquestions',$obj);

            $sql = 'SELECT u.* FROM {role} r JOIN {role_assignments} ra on r.id = ra.roleid JOIN {user} u on ra.userid = u.id WHERE r.shortname = "approver"';
            $appuser = $DB->get_records_sql($sql);
            foreach ($appuser as $key => $approval) {
                $messagehtml = get_config('local_question_bank','reviewedQuestionContent').'<br/>
                link : '.$CFG->wwwroot.'/local/question_bank/preview.php?id='.$id;

                $fromUser = $smtp_settings->email;
                $subject = get_config('local_question_bank','reviewedQuestionSubject');
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

            $response = array(
                "status" => true,
                "msg" => 'Now this question reviewed by reviewer.',
            );
        }else{
            $response = array(
                "status" => false,
                "msg" => 'question is not exist in custom data.',
            );
        }
    }else{
        $response = array(
            "status" => false,
            "msg" => 'failed !!',
        );
    }
    echo json_encode($response);
    
}else{
    $id = $_POST['id'];
    $status = $_POST['status'];
    if($id){
        $locdata = $DB->get_record('local_qbquestions',array('questionid'=>$id));
        if(!empty($status)  ){
            $upstatus = update_question_version_status::execute($id, $status);
            if($upstatus['status'] == 1){
                $obj = new stdClass();
                $obj->id = $locdata->id;
                $obj->questionid = $id;
                $obj->status = (($status == 'ready')?'approved':'review again');
                $obj->modifiedby = $USER->id;
                $obj->modifiedtime = time();
                $obj->approveby = $USER->id;
                $obj->approvetime = time();
                $bbb = $DB->update_record('local_qbquestions',$obj);
                if($status == 'draft'){
                    $sql = 'SELECT u.* FROM {role} r JOIN {role_assignments} ra on r.id = ra.roleid JOIN {user} u on ra.userid = u.id WHERE r.shortname = "reviewer"';
                    $revuser = $DB->get_records_sql($sql);
                    foreach ($revuser as $key => $reviewers) {
                
                        $messagehtml = get_config('local_question_bank','reviewAgainQuestionContent').'<br/>
                        link : '.$CFG->wwwroot.'/local/question_bank/preview.php?id='.$id;
                
                        $fromUser = $smtp_settings->email;
                        $subject = get_config('local_question_bank','reviewAgainQuestionSubject');
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
                $response = array(
                    "action" => (($status == 'ready')?'approve':'reject'),
                    "status" => true,
                    "msg" => 'Your question status has been updated',
                );
            }else{
                $response = array(
                    "action" => '',
                    "status" => false,
                    "msg" => 'Your question status failed',
                );
            }
        }
    
    }else{
        $response = array(
            "action" => '',
            "status" => false,
            "msg" => 'error ! question id not found',
        );
    }
    echo json_encode($response);
}



?>
