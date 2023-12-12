<?php
use core_completion\progress;
require_once ('../../config.php');
global $DB, $USER, $CFG, $PAGE;
$PAGE->requires->jquery();    //<!--///////////////////////////////-->

$courseid = required_param('courseid',PARAM_INT);
$cohortid = required_param('cohortid',PARAM_INT);
$cohortname = required_param('name',PARAM_ALPHA);

require_login();
echo $OUTPUT->header();
if(!empty($cohortid) && !empty($courseid)){ 
$cohort_user = $DB->get_records_sql('SELECT * FROM {cohort_members} cm INNER JOIN {user} u on cm.userid = u.id WHERE cohortid = :cohortid', array('cohortid'=>$cohortid));
$coursedata=$DB->get_record('course',array('id'=>$courseid));


echo ' <div class="container">
        <h3>Enroll users of \''.$cohortname.'\' cohort.</h3>
        <table class="table table-striped" id="short_table">
        <thead>
        <tr>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>Progress</th>
        </tr>
        </thead>
        <tbody>';

    foreach ($cohort_user as $key) {
    $percentage = progress::get_course_progress_percentage($coursedata,$key->userid);
    $percentagedata=(!empty($percentage))? $percentage:0;
    // echo "<pre>";
    // print_r($key);
   
     echo'<tr>
            <td>'.$key->firstname.'</td>
            <td>'.$key->lastname.'</td>
            <td>'.$percentagedata.'%</td>
        </tr>';
    
    }
    echo '</tbody>
    </table>
    </div>';

  
}
?>
<link href="https://cdn.datatables.net/v/dt/dt-1.13.4/datatables.min.css" rel="stylesheet"/> <!--////////////////////////////////////////////-->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script> <!--////////////////////////////////////////////-->
<script type="text/javascript">  
    $(document).ready(function(){
    $('#short_table').DataTable();
    });
   
</script> <!--////////////////////////////////////////////-->
<?php
echo $OUTPUT->footer();


?>
