<?php

namespace block_mycustomcrons\external;

use \external_function_parameters;
use \external_multiple_structure;
use \external_single_structure;
use \external_value;

require_once("{$CFG->libdir}/externallib.php");

class get_allcourse extends \external_api {

    public static function execute($courseid = 0) {
        global $CFG, $DB;

        $instance = new self(); // Create an instance of the class to access non-static methods

        $courses = array();

        // Get all courses or a specific course if courseid is provided
        if ($courseid) {
            $allcourse = $DB->get_records("course", array("id" => $courseid));
        } else {
            $allcourse = $DB->get_records_sql('SELECT * FROM {course}');
        }

        foreach ($allcourse as $key => $course) {
            $array_available_dates = [];

            // Get course details
            $getcourse = $DB->get_records_sql("SELECT c.id,c.fullname,c.shortname,c.startdate,c.enddate FROM {course} c WHERE c.fullname='".$course->fullname."'");
            
            foreach ($getcourse as $getvalue) {
                $ttime = [];
                $available_dates = new \stdClass;
                $custData = $DB->get_records_sql("SELECT cf.name,cf.shortname,cd.value FROM {customfield_data} cd INNER JOIN {customfield_field} cf ON cf.id = cd.fieldid WHERE cd.instanceid = $getvalue->id AND cf.shortname IN ('price', 'exam_date', 'course_venue', 'starttime_hour', 'starttime_minute', 'endtime_hour', 'endtime_minute')");

                foreach ($custData as $custValue) {
                    if ($custValue->shortname == 'price' || $custValue->shortname == 'exam_date' || $custValue->shortname == 'course_venue') {
                        if ($custValue->shortname == 'price') {
                            $available_dates->total_fees = $custValue->value;
                        } else {
                            $available_dates->{$custValue->shortname} = $custValue->value;
                        }
                    } else {
                        $ttime[$custValue->shortname] = $custValue->value;
                    }
                }

                $available_dates->starting = date('F, d, Y', $getvalue->startdate);
                $available_dates->ending = date('F, d, Y', $getvalue->enddate);
                $available_dates->time = sprintf("%02d", $ttime['starttime_hour']).":".sprintf("%02d", $ttime['starttime_minute'])."-".sprintf("%02d", $ttime['endtime_hour']).":".sprintf("%02d", $ttime['endtime_minute']);
                $available_dates->registration = $CFG->wwwroot.'/local/user_registration/index.php?id='.$getvalue->id;
                
                array_push($array_available_dates, $available_dates);
            }

            // Create a stdClass object to hold course information
            $retc = new \stdClass();
            $retc->id = $course->id;
            $retc->fullname = $course->fullname;
            $retc->shortname = $course->shortname;
            $retc->img = "<img src='".$instance->getcourse_image($course->id)."' alt='img'>"; // Use instance method to get course image
            $retc->summary = $course->summary;
            $retc->available_dates = $array_available_dates;

            array_push($courses, $retc);
        }

        return $courses;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_OPTIONAL),
        ]);
    }

    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'id of course'),
                'fullname' => new external_value(PARAM_TEXT, 'fullname of course'),
                'shortname' => new external_value(PARAM_TEXT, 'shortname of course'),
                'img' => new external_value(PARAM_RAW, 'summary of course'),
                'summary' => new external_value(PARAM_RAW, 'summary of course'),
                'available_dates' =>  new external_multiple_structure(
                        new external_single_structure([
                            'starting' => new external_value(PARAM_TEXT, 'starting date of course'),
                            'ending' => new external_value(PARAM_TEXT, 'ending date of course'),
                            'price' => new external_value(PARAM_TEXT, 'price of course', VALUE_OPTIONAL),
                            'exam_date' => new external_value(PARAM_TEXT, 'exam date of course', VALUE_OPTIONAL),
                            'course_venue' => new external_value(PARAM_RAW, 'course venue of course', VALUE_OPTIONAL),
                            'time' => new external_value(PARAM_TEXT, 'time of course', VALUE_OPTIONAL),
                            'total_fees' => new external_value(PARAM_TEXT, 'total fees of course', VALUE_OPTIONAL),
                            'registration' => new external_value(PARAM_TEXT, 'registration link of course', VALUE_OPTIONAL)
                        ])
                    ),
            ])
        );
    }

    function coursecolor($courseid) {
        $basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894', '#0984e3', '#b2bec3', '#fdcb6e', '#fd79a8', '#6c5ce7'];
        $color = $basecolors[$courseid % 10];
        return $color;
    }

    function getcourse_image($courseid) {
        global $DB, $CFG;
        require_once($CFG->dirroot. '/course/classes/list_element.php');
        $course = $DB->get_record('course', array('id' => $courseid));
        $course = new \core_course_list_element($course);
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $imageurl = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'. $file->get_contextid(). '/'. $file->get_component(). '/'. $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            return $imageurl;
        }
        if(empty($imageurl)){
            $color = $this->coursecolor($course->id); // Use $this to call non-static method within the class
            $pattern = new \core_geopattern();
            $pattern->setColor($color);
            $pattern->patternbyid($courseid);
            $classes = 'coursepattern';
            $imageurl = $pattern->datauri();
        }
        return $imageurl;
    }
}

