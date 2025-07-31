<?php

require_once ('../../config.php');
global $DB, $CFG, $USER, $PAGE;
require_login();

$html = '<div class="col-group">
    <div class="col-dt-4 col-ld-2">
        <div class="card card-wrap">
        <div class="card-top">
            <h5 class="card-title">Manage Courses</h5>
            <p class="card-text">Create a new eLearning course or activity for your private member group.</p>
            </div>
                <a href= "'.$CFG->wwwroot.'/local/manage_course/courselist.php/"><button name="manageCoursed">Manage Course</button> </a>
            </div>
        </div>
    </div>
</div>';

echo $OUTPUT->header();
echo $html;
echo $OUTPUT->footer();


?>