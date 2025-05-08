<?php

function local_question_bank_extend_navigation_course(\navigation_node $navigation, \stdClass $course, \context $context) {
    $courseContext = context_course::instance($course->id, MUST_EXIST);
    $addquiz = has_capability('local/question_bank:addquestion', $courseContext);
    $questionreviewed = has_capability('local/question_bank:questionreviewed', $courseContext);
    $approvercap = has_capability('local/question_bank:approvequestion', $courseContext);
    if($addquiz || $questionreviewed || $approvercap){
        $url = new moodle_url("/local/question_bank/index.php", ['courseid' => $course->id]);
        $navigation->add(
            get_string('pluginname', "local_question_bank"),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            null,
            new pix_icon('i/report', '')
        );
    }
}
function local_question_bank_after_require_login() {
    global $PAGE, $COURSE, $CFG;
    if(!is_siteadmin() && $PAGE->pagetype == "question-edit"){
        $courseContext = context_course::instance($COURSE->id, MUST_EXIST);
        $approvercap = has_capability('local/question_bank:approvequestion', $courseContext);
        if($approvercap){

        } else {
            redirect("{$CFG->wwwroot}/my", 'You don\'t have proper permissions to view this page', null, \core\output\notification::NOTIFY_INFO);
            exit;
        }
    }
}
function local_question_bank_before_footer() {
//     global $PAGE;
//    $PAGE->requires->js_init_code("alert('before_footer');");
    global $PAGE, $COURSE, $CFG;
    if(!is_siteadmin()){
        $courseContext = context_course::instance($COURSE->id, MUST_EXIST);
        $approvercap = has_capability('local/question_bank:approvequestion', $courseContext);
        if($approvercap){

        } else {
            echo '<style> 
                .secondary-navigation .moremenu ul li[data-key="questionbank"] {
                    display: none;
                }
            </style>';      
        }
    }
}
