<?php
require_once('../../../config.php');
global $DB, $USER, $PAGE, $OUTPUT;
$PAGE->requires->jquery();
require_login();
// $PAGE->set_pagelayout('standard');
$pageurl = $CFG->wwwroot."/local/user_registration/admin/index.php";
$PAGE->set_url($CFG->wwwroot.'/local/user_registration/admin/index.php');
$PAGE->set_title('Assessor Panel');
$context = \context_system::instance();
$has_capability = has_capability('local/user_registration:assessor_access', $context, $USER->id);
if (!$has_capability) {
    $urltogo_dashboard = $CFG->wwwroot.'/local/user_registration/';
    redirect($urltogo_dashboard, 'You do not have permission to view this page', null, \core\output\notification::NOTIFY_WARNING);
}
$html = '
<style>
#loader {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    background-color: rgba(255, 255, 255, 0.7);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
} #loader img {
    width: 50px;
    height: 50px;
} 
</style>

<h3><i class="fa-solid fa-bars pr-2" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"></i> Assessor Panel</h3>
<div class="collapse" id="collapseExample">
<ul>
 <li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/index.php">Home</a></li>
 <li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/setting-template.php?type='.base64_encode(1).'">Email template individual</a></li>
 <li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/setting-template.php?type='.base64_encode(2).'">Email template corporate</a></li>
 <li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/setting-logo-address.php?type='.base64_encode(3).'">Email Logo , Address</a></li>
</ul>
</div>
<br>
<h4>Registered User List (Level 3 and above) <span id="dynamic_data_type"></span></h4>
<hr>
<div class="bg-secondary">
   <table class="table text-center">
    <tr>
        <td>
            <input value="Individual" type="button" class="btn btn-danger px-3 py-1">
        </td>
        <td>
            <input value="Corporate" type="button" class="btn btn-danger px-3 py-1">
        </td>
    </tr>
   </table>
</div>
<div class="parentloader">
    <div id="loader">
        <img id="imgg" src="https://media.tenor.com/wpSo-8CrXqUAAAAi/loading-loading-forever.gif" alt="Loading...">
        <p>Loading...</p>
    </div>
</div>
<table class="table table-bordered mt-5" id="headerTable" style="text-align: center;">
    <thead>
        <tr>
            <th>Registration No.</th>
            <th>Name/ Client Name</th>
            <th>Email</th>
            <th>Date</th>
            <th>Payment Status</th>
            <th>Status</th>
            <th>Details</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>  
    </tbody>  
</table>
<link href="'.$CFG->wwwroot.'/local/user_registration/js/dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="'.$CFG->wwwroot.'/local/user_registration/js/dataTables.min.js"></script>
<script>
$.noConflict();
jQuery(document).ready(function($) {
    $(".btn").click(function() {
        var postVar = $(this).val();
        $("#headerTable").DataTable({
            "order":[[4, "desc"], [0, "asc"]],
            "processing": true,
            "serverSide": true,
            "serverMethod": "post",
            "ajax": {
                "url": "'.$CFG->wwwroot.'/local/user_registration/admin/ajax.php",
                "data": function(data){
                 data.postVar = postVar; 
              }
        },
        "drawCallback": function(settings) {
            $("#dynamic_data_type").text(settings.json.type);
        },
        "columns": [
             { data: "reg_no" },
             { data: "name" },
             { data: "email" },
             { data: "date" },
             { data: "payment_status" },
             { data: "status" },
             { data: "details" },
             { data: "action",orderable: true, targets: -1  },
                  ],
        "bDestroy": true
        });
    });
});
</script>';
echo $OUTPUT->header();
echo $html;
echo $OUTPUT->footer();










