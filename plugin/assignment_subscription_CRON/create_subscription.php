<?php

require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once("$CFG->libdir/filelib.php");
global $DB, $USER, $PAGE;

$PAGE->requires->jquery();
require_login();
$is_siteadmin = is_siteadmin();
$context = \context_system::instance();
$current_logged_in_user =  $USER->id;
$has_capability = has_capability('local/assignment_subscription:createuser_subscription', $context, $current_logged_in_user);
if (!$has_capability) {
    $urltogo_dashboard = $CFG->wwwroot.'/my/';
    redirect($urltogo_dashboard, 'You do not have permission to view this page', null, \core\output\notification::NOTIFY_WARNING);
}

$uid = optional_param('uid',0, PARAM_INT);
$pageurl = $CFG->wwwroot."/local/assignment_subscription/create_subscription.php?uid=".$uid;
$args = array("uid" => $uid);

$PAGE->set_title('Assignment Subscription');
$PAGE->set_heading('Assignment Subscription');

class create_user extends moodleform {
//Add elements to form
public function definition() {
global $CFG, $DB;

// print_r($this->_customdata);
$uid = $this->_customdata['uid'];

//die();

$periodmenu = enrol_get_period_list();



$mform = $this->_form; 
$edit_subscription = $DB->get_record('assign_subs_users', array('id'=>$uid), '*');


if(!empty($edit_subscription)){

$quertech=  "SELECT * FROM {user} WHERE id=$edit_subscription->userid";
$enroltech = $DB->get_record_sql($quertech);

$mform->addElement('hidden', 'user_id', $edit_subscription->userid);
$mform->addElement('hidden', 'sub_method', $edit_subscription->subscription_method);

$mform->addElement('static', 'user_name', get_string('selected_users','local_assignment_subscription'), $enroltech->firstname." ".$enroltech->lastname);
$subscription_type = $edit_subscription->subscription_type;

// Subscription Method
if($subscription_type == 1){ 
$type = ' (One-Off)'; 
$mform->addElement('static', 'sub_method_static', get_string('sub_method','local_assignment_subscription'), "Online".$type);
}elseif($subscription_type == 2){ 
$type = ' (Recurring)'; 
$mform->addElement('static', 'sub_method_static', get_string('sub_method','local_assignment_subscription'), "Online".$type);
}else{
$mform->addElement('static', 'sub_method_static', get_string('sub_method','local_assignment_subscription'), "Manual");
}

}else{


$array_product = array(""=>"Select Student"); 
$quertech2=  "SELECT u.* FROM {user} u LEFT JOIN {assign_subs_users} asu ON asu.userid = u.id WHERE u.id > 1 AND u.deleted=0 AND asu.id IS NULL";
$allusers = $DB->get_records_sql($quertech2);

foreach ($allusers as $key => $value){
    $username = $value-> firstname." ".$value-> lastname." (".$value-> email.")";
    $array_product[$value->id] = $username;
}


// Subscription User
// $select = $mform->addElement('select', 'user_id', get_string('sel_users','local_assignment_subscription'), $array_product, 'required');
$mform->addElement('autocomplete', 'user_id', get_string('sel_users','local_assignment_subscription'), $array_product, array(                                                                                                           
    'multiple' => false,                                                  
    'noselectionstring' => "Select Student",                                                                
));
$mform->addRule('user_id', "please select student", 'required', null, 'client');

// $options = array('Manual Subscription' => 'Manual Subscription');
// $select = $mform->addElement('select', 'sub_method', get_string('sub_method','local_assignment_subscription'), $options, 'required');
// $select->setSelected('1');

$sub_status = $mform->addElement('static', 'sub_method', get_string('sub_method','local_assignment_subscription'));
$mform->setDefault('sub_method', 'Manual Subscription'); 
$sub_status->freeze();

}


// Subscription Status
$sub_status = $mform->addElement('static', 'sub_status', get_string('sub_status','local_assignment_subscription'));
$mform->setDefault('sub_status', 'Active'); 
$sub_status->freeze();


// Subscription Starts 
$mform->addElement('date_time_selector', 'sub_starts', get_string('sub_starts','local_assignment_subscription'), array('optional' => true));
$mform->addRule('sub_starts', "Subscription start time is required", 'required', null, 'client');
$mform->setDefault('sub_starts', time());


// Subscription Duration
// $mform->addElement('duration', 'sub_duration', get_string('sub_duration','local_assignment_subscription'));
// $mform->setDefault('sub_duration', '15552000'); 
unset($periodmenu['']);
$mform->addElement('select', 'sub_duration', get_string('sub_duration', 'local_assignment_subscription'), $periodmenu);
$mform->setDefault('sub_duration', $duration);
$mform->disabledIf('sub_duration', 'sub_starts[enabled]', 'notchecked', 1);
$mform->disabledIf('sub_duration', 'sub_ends[enabled]', 'checked', 1);


// Subscription Ends
$mform->addElement('date_time_selector', 'sub_ends', get_string('sub_ends','local_assignment_subscription'), array('optional' => true));
$mform->setDefault('sub_ends', time());

// Subscription Created
$current_datetime = date('l, d F Y, h:i A');
$sub_created = $mform->addElement('static', 'sub_created', get_string('sub_created','local_assignment_subscription'));
$mform->setDefault('sub_created', $current_datetime); 
$sub_created->freeze();


if (($edit_subscription->created_date) != ($edit_subscription->modified_date)) {
$linkcontent = '<a data-toggle="modal" data-target="#myModal" style="color:blue;">View</a>';
$mform->addElement('static', 'update_history', get_string('update_history','local_assignment_subscription'), $linkcontent);
}else{
$update_history = $mform->addElement('static', 'update_history', get_string('update_history','local_assignment_subscription'));
$mform->setDefault('update_history', 'None'); 
$update_history->freeze();
}


// Setting the default values
if (!empty($edit_subscription)){
if(($edit_subscription->end_date > time())==1){ $status = 'Active'; }else{ $status = 'Inactive'; }
$mform->setDefault('sub_ends', $edit_subscription->end_date);
$mform->setDefault('sub_duration', $edit_subscription->subscription_duration);
$mform->setDefault('sub_starts', $edit_subscription->start_date);
$mform->setDefault('sub_status', $status);
$mform->setDefault('cost', $edit_subscription->cost);
$mform->setDefault('sub_method', $edit_subscription->subscription_method);
}


// if(empty($edit_subscription)){

// }


if (!empty($edit_subscription)) {
//Subscription Submit 
$this->add_action_buttons();
}else{
//Subscription Submit 
$this->add_action_buttons( $cancel = true, $submitlabel='Create User');
}    

}


//Custom validation should be added here
function validation($data, $files) {
    $errors = array();
    if(empty($data['user_id'])){
        $errors['user_id'] = "Student is required";
    }
    if(empty($data['sub_starts'])){
        $errors['sub_starts'] = "Subscription start time is required";
    }

    $errors = parent::validation($data, $files);

    if (!empty($data['sub_starts']) and !empty($data['sub_ends'])) {
        if ($data['sub_starts'] >= $data['sub_ends']) {
            $errors['sub_ends'] = "End date should be greater then start date";
        }
    }

    return $errors;
}
}


$mform = new create_user($pageurl, $args);

if ($mform->is_cancelled()) {

// If cancelled then redirect
$urltogo = $CFG->wwwroot.'/local/assignment_subscription/view_subscription.php';
redirect($urltogo);

} else if ($fromform = $mform->get_data()) {
    //     echo "<pre>";
    // print_r($fromform);
    // die;


    // Storing the post variables
    $user_id = $fromform ->user_id;
    $sub_method = $fromform ->sub_method;
    $sub_status = $fromform ->sub_status;
    $sub_starts = $fromform ->sub_starts;
    $sub_duration = $fromform ->sub_duration;
    $sub_ends = $fromform ->sub_ends;
    $sub_created = $fromform ->sub_created;
    $update_history = $fromform ->update_history;
    if(!empty($sub_ends)){
        $date1=date_create(date("Y-m-d", $sub_starts));
        $date2=date_create(date("Y-m-d", $sub_ends));
        $diff=date_diff($date1,$date2);
        $sub_duration = $diff->format("%a");
    } else if(!empty($sub_duration)) {
        $sub_ends = $sub_starts + $sub_duration;
    } else {
        $sub_duration = 0;
        $sub_ends = 0;
    }


    // Checking user data exist or not
    $chk_user=  "SELECT * FROM {assign_subs_users} WHERE userid=$user_id";
    $row_user = $DB->get_record_sql($chk_user);

        $record_ins = new stdClass();
        $record_ins -> userid = $user_id;
        $record_ins -> start_date = $sub_starts;
        $record_ins -> end_date = $sub_ends;
        $record_ins -> subscription_method = $sub_method;
        $record_ins -> subscription_duration = $sub_duration;
        $record_ins -> status = 1;
        $record_ins -> cost = 0;
        $record_ins -> created_date = strtotime(date("d F Y H:i:s"));
        $record_ins -> modified_date = strtotime(date("d F Y H:i:s"));
        $record_ins -> modified_by = $current_logged_in_user;
        $record_ins -> update_history = $update_history;
        $record_ins -> subscription_type = 0;

    if(!empty($row_user->id)){

        $record_upd = new stdClass();
        $record_upd -> userid = $row_user->userid;
        $record_upd -> start_date = $row_user->start_date;
        $record_upd -> end_date = $row_user->end_date;
        $record_upd -> cost = $row_user->cost;
        $record_upd -> subscription_method = $row_user->subscription_method;
        $record_upd -> subscription_duration = $row_user->subscription_duration;
        $record_upd -> date_of_update = strtotime(date("d F Y H:i:s"));
        // if($row_user -> modified_date != $row_user -> created_date){
        //     $record_upd -> subscription_method = "Manual Subscription";
        // }        
        $record_ins -> created_date = $row_user->created_date;
        $record_ins -> modified_date = strtotime(date("d F Y H:i:s"));
        $record_ins -> subscription_method = "Manual Subscription";
        $record_ins -> cost = $row_user->cost;
        $record_ins -> update_history = $update_history;
        $record_ins -> subscription_type = $row_user->subscription_type;
        $record_ins -> stripe_canceled_status = $row_user->stripe_canceled_status;

        $DB -> insert_record('assign_subs_history', $record_upd, false); // Insert in secondary table 
        $DB -> delete_records('assign_subs_users', array('id' => $row_user->id)); //  Delete from primary table

    }


    $DB -> insert_record('assign_subs_users', $record_ins, false); // Insert in primary table
    $urltogo = $CFG->wwwroot.'/local/assignment_subscription/view_subscription.php'; // Redirect to page
    redirect($urltogo, 'User subscription created successfully',  \core\output\notification::NOTIFY_SUCCESS);


}

echo $OUTPUT->header();

?>

<style>
.col-md-3 {
  flex: 0 0 25%;
  max-width: 25%;
  text-align: right;
}
</style>

<?php if(!empty($uid)){ ?>

<h2 style="margin-left: 20px;">Edit User Account</h2><br>

<?php }else{ ?>

<h2 style="margin-left: 20px;">Create New Priority User</h2><br>

<?php } ?>

<?php

    $mform->display();

?>



<!-- Custom Modal Start-->
<div id="myModal" class="modal fade" role="dialog">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <table class="table table-striped table-bordered" style="width:100%" >
        <thead>
            <tr>
                <th>Date of Update </th>
                <th>Subscription Start date</th>
                <th>Subscription End date</th>
                <th>Subscription Duration</th>
                <th>Subscription Method</th>
            </tr>
        </thead>
        <tbody>
                <?php 
                $users_subscription = $DB->get_record('assign_subs_users', array('id'=>$uid), '*');
                if($users_subscription){
                    ?>
                <tr>
                    <td><?php echo date("d/m/Y", $users_subscription->modified_date); ?></td>
                    <td><?php echo date("d/m/Y", $users_subscription->start_date); ?></td>
                    <td><?php echo date("d/m/Y", $users_subscription->end_date); ?></td>
                    <td><?php 
                    
                        if(!empty($users_subscription->end_date)){
                        $datem1=date_create(date("Y-m-d", $users_subscription->start_date));
                        $datem2=date_create(date("Y-m-d", $users_subscription->end_date));
                        $diffm=date_diff($datem1,$datem2);
                        echo $daysm = $diffm->format("%a days");
                        } else {
                        echo $daysm = "N/A";
                        }
                    
                
                    ?></td>
                    <td><?php echo $users_subscription->subscription_method; ?></td>
                </tr>
    
                    <?php                
                }
                $users_subscription_history = "SELECT * FROM {assign_subs_history} WHERE userid=$users_subscription->userid order by id desc";
                $row_users_subscription_history = $DB->get_records_sql($users_subscription_history);
                foreach ($row_users_subscription_history as $value) { 
                ?>
            <tr>
                <td><?php echo date("d/m/Y", $value->date_of_update); ?></td>
                <td><?php echo date("d/m/Y", $value->start_date); ?></td>
                <td><?php echo date("d/m/Y", $value->end_date); ?></td>
                <td><?php 
                
                    if(!empty($value->end_date)){
                    $datem1=date_create(date("Y-m-d", $value->start_date));
                    $datem2=date_create(date("Y-m-d", $value->end_date));
                    $diffm=date_diff($datem1,$datem2);
                    echo $daysm = $diffm->format("%a days");
                    } else {
                    echo $daysm = "N/A";
                    }
                
            
                ?></td>
                <td><?php echo $value->subscription_method; ?></td>
            </tr>

                <?php } ?>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>
<!-- Custom Modal End-->


<?php

echo $OUTPUT->footer();






