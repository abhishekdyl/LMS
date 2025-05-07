<?php
namespace block_mycustomcrons\task;
/** 
 * An example of a scheduled task.
 */
class mol_coursedocument_expirynotification extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return "MOL Course Document Expiry Notification";
    }
 
    /**
     * Execute the task.
     */
    public function execute() {
        global $DB, $USER,$CFG;
        require_once($CFG->dirroot . '/user/editlib.php');
        require_once($CFG->dirroot . '/message/lib.php');
        require_once($CFG->dirroot . '/lib/moodlelib.php');
        require_once($CFG->dirroot . '/lib/classes/user.php'); 
         echo "helooo";
         $currenttime = time();
        $customfield_field =  $DB->get_record_sql("SELECT * FROM {customfield_field} WHERE shortname = 'mol_expiry_date'");
        $field_id = $customfield_field->id;
        $expirycoursedoc =  $DB->get_records_sql("SELECT cd.* FROM {customfield_data} as cd 
            LEFT JOIN {document_expiry} as de ON de.courseid = cd.instanceid AND de.mailsend_date >= cd.value 
            WHERE cd.fieldid = ? AND cd.value <= ? AND de.id IS NULL",array($field_id, $currenttime));

        foreach ($expirycoursedoc as $expirydoc) {
            $coursedetails = $DB->get_record('course', array('id'=>$expirydoc->instanceid));
            // $messagehtml ="<!DOCTYPE html>
            // <html>
            //     <head>
            //         <title>Mail</title>
            //         <link rel='stylesheet' type='text/css' href='https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
            //     </head>
            //     <body>
            //         <p>Dear <b> Admin </b>,</p>
            //         <br>
            //         <p>please be advised that the MOL document for course : (".$coursedetails->fullname.") has been expired.
            //         <p>Expiry date : ".date("d/m/Y", $expirydoc->value)."</p>

            //         <br>
            //         <p>Please update the required document</p>
            //     </body>
            // </html>";

            $adminEmail = $DB->get_record("user", array("id"=>301));
            $message = new \core\message\message();
            $message->courseid          = 1;
            $message->component         = 'moodle';
            $message->name              = 'instantmessage';
            $message->userfrom          = \core_user::get_noreply_user();
            $message->subject           = 'expired document';
            $message->fullmessageformat = FORMAT_HTML;
            $message->notification      = 1;
            $message->userto            = $adminEmail;
            $message->fullmessagehtml   = "<html>
                <head>
                    <title>Mail</title>
                    <link rel='stylesheet' type='text/css' href='https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
                </head>
                <body>
                    <p>Dear <b> Admin </b>,</p>
                    <br>
                    <p>please be advised that the MOL document for course : (".$coursedetails->fullname.") has been expired.
                    <p>Expiry date : ".date("d/m/Y", $expirydoc->value)."</p>

                    <br>
                    <p>Please update the required document</p>
                </body>
            </html>";
            $message->fullmessage       = 'small message';
            // Actually send the message
            $messageid = message_send($message);
            var_dump($messageid);
            if($messageid){
                $expiredcourse = $DB->get_record('document_expiry', array('courseid'=>$expirydoc->instanceid));
                // print_r($expirydoc);
                // echo 1111;
                // die;
                if($expiredcourse){
                    $updatexpdocment  = new \stdClass();
                    $updatexpdocment->id = $expiredcourse->id;
                    $updatexpdocment->mailsend  =1;
                    $updatexpdocment->mailsend_date=time();
                    $updateRecords=$DB->update_record('document_expiry', $updatexpdocment);
                }else{

                    $expireddocment  = new \stdClass();
                    $expireddocment->courseid = $expirydoc->instanceid;
                    $expireddocment->mailsend  =1;
                    $expireddocment->mailsend_date=time();
                    $expireddocment->created_date=time();
                    $insertRecords=$DB->insert_record('document_expiry', $expireddocment);
                }
                echo "mail send";
            }else{
                echo "mail faild";
            }

        }
// $admins = get_admins();
//             foreach ($admins as $adminusers) {
//                 // echo "<pre>";
//                 // print_r($adminusers);
//                 // echo "</pre>";
//                 echo $adminusers->email ."<br>";
//             }
                    
    }
}