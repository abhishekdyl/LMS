<?php

class customClass {

    // Get course image
    public static function getcourse_image($courseid) {
        global $DB, $CFG;
        require_once($CFG->dirroot. '/course/classes/list_element.php');
        $course = $DB->get_record('course', array('id' => $courseid));
        $course = new core_course_list_element($course);
        foreach ($course->get_course_overviewfiles() as $file) {
        $isimage = $file->is_valid_image();
        $imageurl = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'. $file->get_contextid(). '/'. $file->get_component(). '/'. $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
        return $imageurl;
        }
        if(empty($imageurl)){
        $color = self::coursecolor($course->id);
        $pattern = new \core_geopattern();
        $pattern->setColor($color);
        $pattern->patternbyid($courseid);
        $classes = 'coursepattern';
        $imageurl = $pattern->datauri();

        
        $svg_collection = array('Untitled_blue.jpg', 'Untitled_gray.jpg', 'Untitled_purple.jpg', 'Untitled_skyblue.jpg');
        $image = array_rand($svg_collection, 1);
        $imageurl  = $CFG->wwwroot."/local/user_registration/src/".$svg_collection[$image];

        }
        return $imageurl;
    }

	// Course color
    public static function coursecolor($courseid) {
        $basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894', '#0984e3', '#b2bec3', '#fdcb6e', '#fd79a8', '#6c5ce7'];
        $color = $basecolors[$courseid % 10];
        return $color;
    }


    public static function template_string($content, $data=null){
        if(is_array($data) || is_object($data)){
            foreach ($data as $key => $value){
                $content = str_ireplace('{a->'.$key.'}', $value, $content);
            }
        } else if(!empty($data)){
            $content = str_ireplace('{a}', $data, $content);
        }
    return $content;
    }

}