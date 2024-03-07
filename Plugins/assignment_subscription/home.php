<?php 

require_once('../../config.php');
global $DB, $USER, $PAGE;

$PAGE->requires->jquery();

require_login();
$is_siteadmin = is_siteadmin();
$context = \context_system::instance();
$current_logged_in_user =  $USER->id;
$has_capability = has_capability('local/assignment_subscription:grade_student', $context, $current_logged_in_user);
if (!$has_capability) {
$urltogo_dashboard = $CFG->wwwroot.'/local/assignment_subscription/index.php';
redirect($urltogo_dashboard, null, \core\output\notification::NOTIFY_WARNING);
}


$PAGE->set_title('Assignment Subscription');
$PAGE->set_heading('Assignment Subscription');

$query = 'SELECT * FROM {assign_subs_users}';
$all_users = $DB->get_records_sql($query);

$home = $CFG->wwwroot."/local/assignment_subscription/home.php";
$create_subscription = $CFG->wwwroot."/local/assignment_subscription/create_subscription.php";
$view_subscription = $CFG->wwwroot."/local/assignment_subscription/view_subscription.php";
$mark_student = $CFG->wwwroot."/local/assignment_subscription/mark_student.php";
$plugin_setting = $CFG->wwwroot."/local/assignment_subscription/plugin_setting.php";
$settings = $CFG->wwwroot."/admin/settings.php?section=local_assignment_subscription_settings"; 

echo $OUTPUT->header();

?> 

  <?php if($is_siteadmin==1){ ?>

  <div class="container px-2 mt-2">
    <div class="row gx-5">
      <div class="col-md-6 p-3">
       <div class="p-3 border bg-light"><a href="<?php echo $plugin_setting; ?>">Priority Setting</a>
          <br><i>Plugin Configuration (Submission Limit, Assign Tutor Function)</i>
       </div>
      </div>
      <div class="col-md-6 p-3">
        <div class="p-3 border bg-light"><a href="<?php echo $settings; ?>">Stripe Setting</a>
          <br><i>Set Stripe Setting (Published Key, Secret Key, Target - General, Priority)</i>
        </div>
      </div>
      <div class="col-md-6 p-3">
        <div class="p-3 border bg-light"><a href="<?php echo $create_subscription; ?>">Create Subscription</a>
          <br><i>Add New Subscription</i>
        </div>
      </div>
      <div class="col-md-6 p-3">
       <div class="p-3 border bg-light"><a href="<?php echo $view_subscription; ?>">View Subscription</a>
          <br><i>View Subscription List, Edit, Delete Users</i>
       </div>
      </div>
    </div>
  </div>

  <?php } ?>
  

  <div class="container px-2 mt-2">
    <div class="row gx-5">
      <div class="col-md-6 p-3">
        <div class="p-3 border bg-light"><a href="<?php echo $mark_student; ?>">Assignment Submission</a>
          <br><i>A Submission dashboard to access Archive, General and Priority submissions </i>
        </div>
      </div>
    </div>
  </div>

<?php echo $OUTPUT->footer(); ?>
