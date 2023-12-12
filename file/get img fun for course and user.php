<?php
GET IMG OF COURSE IN MOODLE :

function coursecolor($courseid) {
        $basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894', '#0984e3', '#b2bec3', '#fdcb6e', '#fd79a8', '#6c5ce7'];
        $color = $basecolors[$courseid % 10];
        return $color;
    }

    function getcourse_image($courseid) {
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
                $color = $this->coursecolor($course->id);
                $pattern = new \core_geopattern();
                $pattern->setColor($color);
                $pattern->patternbyid($courseid);
                $classes = 'coursepattern';
                $imageurl = $pattern->datauri();
            }
            return $imageurl;
        }




GET COURSE PROGRESS FUNCTION IN MOODLE :
    use core_completion\progress;
    $percentage = progress::get_course_progress_percentage($course, $userid);



GET IMG OF USER IN MOODLE :
    $userpicture = new user_picture($infotech); //(LOOP DATA)
            $userpicture->size = 1; // Size f1.
            $img = $userpicture->get_url($PAGE)->out(false);
OOOOORRRRRRRR
if($infotech->picture==0){ ?>
<img src="<?php echo $img = new moodle_url('/user/pix.php/' . $infotech->id . '/f1.jpg'); ?>">

<?php } else{
$techimg = $DB->get_record_sql("SELECT * FROM {files} WHERE id=$infotech->picture"); ?>           								
<img src="<?php echo $img = new moodle_url('/pluginfile.php/' . $techimg->contextid . '/user/icon/f3'); ?>"> 
<?php } ?>


------------------------------------------------------------------------------------------------------------------------------------------------------



    function coursecolor($courseid) {
        //     // The colour palette is hardcoded for now. It would make sense to combine it with theme settings.
        //     $basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894', '#0984e3', '#b2bec3', '#fdcb6e', '#fd79a8', '#6c5ce7'];

        //     $color = $basecolors[$courseid % 10];
        //     return $color;
        // }

    $course1 = new \course_in_list($course);
            //   foreach ($course1->get_course_overviewfiles() as $file) {
            //     $isimage = $file->is_valid_image();
            //     $courseimage = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'. $file->get_contextid(). '/'. $file->get_component(). '/'. $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            //   }
            //   $color = $this->coursecolor($course->id);
            //   if (!isset($courseimage)) {
            //       $pattern = new \core_geopattern();
            //       $pattern->setColor($color);
            //       $pattern->patternbyid($courseid);
            //       $classes = 'coursepattern';
            //       $courseimage = $pattern->datauri();
            //   }



    use core_completion\progress;
    $percentage = progress::get_course_progress_percentage($course, $userid);

    ?>