<?php 
defined('MOODLE_INTERNAL') || die();
$OUTPUT->doctype();
$bodyattributes = $OUTPUT->body_attributes([]);
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
];

if (empty($PAGE->layout_options['noactivityheader'])) {
    $header = $PAGE->activityheader;
    $renderer = $PAGE->get_renderer('core');
    $templatecontext['headercontent'] = $header->export_for_template($renderer);
}

// $templatecontext['dynamicdata']='aaaaaaaa';
echo $OUTPUT->render_from_template('theme_customboost/cc_course', $templatecontext);



