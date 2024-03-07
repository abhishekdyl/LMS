<?php

require_once("$CFG->libdir/externallib.php");
class local_business_courses extends external_api {
    public static function get_all_courses_parameters() {
        return new external_function_parameters([
                'search' => new external_value(PARAM_RAW, 'query'),
                'page' => new external_value(PARAM_INT, 'Page number'),
                'perpage' => new external_value(PARAM_INT, 'Number per page')
        ]);
    }

    public static function get_all_courses($search,$page,$perpage){
        global $DB, $CFG;
        $coursedata = array();
        if(!empty($search)){
            $sql="SELECT id,fullname as name FROM {course} WHERE fullname LIKE '%$search%'  AND visible = 1 LIMIT 100";
            $coursedata=array_values($DB->get_records_sql($sql,array()));
        } else {
            $sql="SELECT id,fullname as name FROM {course} WHERE visible = 1  LIMIT 100";
            $coursedata=array_values($DB->get_records_sql($sql,array()));
        }
        return $coursedata;
    }

    public static function get_all_courses_returns() {
        global $CFG;
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'=>new external_value(PARAM_INT,'Course id'),
                    'name'=>new external_value(PARAM_RAW,'course Name'),
                )
            ),
            'no record',VALUE_DEFAULT,array()
        );
        
    }
}