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

        if ($courseid) {
            $allcourse = $DB->get_records("course", array("id" => $courseid));
        } else {
            $allcourse = $DB->get_records("course", array());
        }

        $courses = array();

        foreach ($allcourse as $key => $course) {

             $cust_field = $DB->get_records_sql("SELECT cf.shortname, cf.name,cf.description,cd.value FROM {customfield_data} cd inner join {customfield_field} cf on cf.id = cd.fieldid WHERE cd.instanceid = $course->id");
            // $contextid = context_course::instance($course->id)->id,
            $duration = $instance->calculateDuration($course->timecreated, time());
            $dur = "Duration: {$duration['days']} days, {$duration['hours']} hours";

            // Create a stdClass object to hold course information
            $retc = new \stdClass();
            $retc->id = $course->id;
            $retc->fullname = $course->fullname;
            $retc->shortname = $course->shortname;
            $retc->summary = $course->summary;
            $retc->custom_fields = $cust_field;
            $retc->timecreated = $course->timecreated;
            $retc->duration = $dur; // Assign the duration to the stdClass object

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
                'summary' => new external_value(PARAM_RAW, 'summary of course'),
                'custom_fields' =>  new external_multiple_structure(
                        new external_single_structure([
                            'shortname' => new external_value(PARAM_TEXT, 'shortname of course'),
                            'name' => new external_value(PARAM_TEXT, 'fullname of course'),
                            'value' => new external_value(PARAM_RAW, 'summary of course', VALUE_OPTIONAL),
                            'description' => new external_value(PARAM_RAW, 'summary of course', VALUE_OPTIONAL)
                        ])
                    ),
                'timecreated' => new external_value(PARAM_TEXT, 'timecreated of course'),
                'duration' => new external_value(PARAM_TEXT, 'duration of course')
            ])
        );
    }

    /**
     * Function to calculate duration between two timestamps
     * @param int $timestamp1 First timestamp
     * @param int $timestamp2 Second timestamp (current time)
     * @return array Duration in days and hours
     */
    function calculateDuration($timestamp1, $timestamp2) {
        // Convert timestamps to DateTime objects
        $dateTime1 = new \DateTime("@$timestamp1");
        $dateTime2 = new \DateTime("@$timestamp2");

        // Calculate the interval between the two dates
        $interval = $dateTime1->diff($dateTime2);

        // Get the total number of days from the interval
        $days = $interval->days;

        // Calculate remaining hours
        $hours = $interval->h;
        if ($days > 0) {
            $day = $hours/24; 
            $hours = $day * 24;// Convert days to hours
        }

        // Return an associative array with total days and remaining hours
        return [
            'days' => $days,
            'hours' => $hours
        ];
    }

}
