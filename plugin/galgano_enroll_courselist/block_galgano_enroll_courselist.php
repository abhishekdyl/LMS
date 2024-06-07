<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course summary block
 *
 * @package    block_course_overview_slider
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use core_completion\progress;

class block_galgano_enroll_courselist extends block_base {

    /**
     * @var bool Flag to indicate whether the header should be hidden or not.
     */
    private $headerhidden = true;

    function init() {
        $this->title = get_string('pluginname', 'block_galgano_enroll_courselist');
    }
    function applicable_formats() {
        return array('all' => true);
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
    function get_content () {
        global $DB,$CFG;
        // require_once($CFG->dirroot."/course/externallib.php");
        $this->content = new stdClass;
        // $COURSEAPI = new core_course_external();
        require_once($CFG->dirroot . '/lib/enrollib.php');
        $data = enrol_get_my_courses($fields = null, $sort = "sortorder asc", $limit = 10, $courseids = [], $allaccessible = false, $offset = 0, $excludecourses = []);
        $havemoredata = enrol_get_my_courses($fields = null, $sort = "sortorder asc", $limit = 10, $courseids = [], $allaccessible = false, $offset = 10, $excludecourses = []);
       $html ='
                <style>
                        .img-frame {
                    position: relative;
                    display: block;
                    width: 100%;
                    padding: 0;
                    overflow: hidden;
                }
                .img-frame::before {
                    display: block;
                    content: "";
                }
                .img-frame-21by9::before {
                    padding-top: 42.8571428571%;
                }
                .img-frame-16by9::before {
                    padding-top: 56.25%;
                }
                .img-frame-4by3::before {
                    padding-top: 75%;
                }
                .img-frame-1by1::before {
                    padding-top: 100%;
                }
                .img-frame img {
                    position: absolute;
                    top: 0;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    border: 0;
                    -o-object-fit: cover;
                    object-fit: cover;
                    border-top-left-radius: 8px;
                    border-top-right-radius: 8px;
                }

                .head-lime-clamp{
                    display: -webkit-box;
                    -webkit-box-orient: vertical;
                    -webkit-line-clamp: 2;
                    overflow: hidden;
                    max-height : 50px;
                    min-height : 50px;
                }

                .txt-lime-clamp{
                    display: -webkit-box;
                    -webkit-box-orient: vertical;
                    -webkit-line-clamp: 3;
                    overflow: hidden;
                }

                #myProgresss { 
                    background : #f2f2f2;
                    width : 100%;
                    height : 3px;
                }

                #myBarr{
                    background : #3986b6;
                    height : 100%;
                }

                .theme-btn,
                .theme-btn:hover,
                .theme-btn:focus{
                    background: #3986b6;
                    border: 2px solid #3986b6;
                    color: #fff;
                    padding: 3px 10px;
                    border-radius: 3px;
                }
                .h-progresss{
                    min-height: 50px;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }
                .custom-border{
                    border: 1px solid #e3e3e3;
                    border-bottom-left-radius: 8px;
                    border-bottom-right-radius: 8px;
                }
                .course-name {
                    display: -webkit-box;
                    -webkit-box-orient: vertical;
                    -webkit-line-clamp: 2;
                    overflow: hidden;
                    max-height: 50px;
                    min-height: 50px;
                    color : #0a0a0a;
                    font-family : arial !important;
                    font-weight : 700 !important;
                }

                @media (min-width: 992px){
                    .custom-course-frame{
                        flex: 0 0 20% !important;
                        max-width: 20% !important; 
                    }
                }
                </style> 
                <div class="container">
                <div class="row" id="myenrolledcourse" >';
                foreach ($data as $course ) { 
                    $percentage = progress::get_course_progress_percentage($course, $userid);
                    $percentagedata=(!empty($percentage))? $percentage:0;
                    
                    $html .= '


                        <div class="col-12 col-lg-3 col-md-4 my-3 custom-course-frame">
                            <div class="card">
                                <div class="img-frame img-frame-1by1">
                                     <img src="'.$this->getcourse_image($course->id).'" alt="img"">
                                </div>
                                <div class="card-body custom-border">
                                    <h4 class="custom-title head-lime-clamp">'.$course->fullname.'</h4>
                                    <div class="h-progresss">
                                        <div id="myProgresss">
                                            <div id="myBarr" style="width : '.$percentage.'%;"></div>
                                        </div>
                                                   <p>'.round($percentagedata).'%</p>
                                    </div>  
                                    <a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" class="theme-btn">Visualizza</a>
                                </div>
                            </div>
                        </div>
                        ';
                }
                $html .= '</div>

                '.(!empty($havemoredata)?'<div class="d-flex align-items-center justify-content-center"><a href="javascript:void(0);"  class="more loadmoredata custom-data-load" data-page="1" >Scopri di più <i class="fa fa-angle-right display-6" aria-hidden="true"></i></a></div>':'').'
                    </div>

                    <script type="text/javascript" src="js/jQuery.js"></script>
                    <script type="text/javascript" src="css/slick.js"></script>
                    <script type="text/javascript">

                  $(function(){
                    console.log("aaaaaaaaaa");
                    $("body").on("click",".loadmoredata",function(){
                        console.log("ddddddddd");
                        var page_id=parseInt($(this).attr("data-page"));
                        var that = this;
                        $.ajax({
                            type:"GET",
                            url:"'.$CFG->wwwroot.'/blocks/galgano_enroll_courselist/ajax.php",
                            data:{page:page_id},
                            success:function(response){
                                response = JSON.parse(response);
                                if(response.havemore){
                                    $(that).attr("data-page", (page_id+1));
                                } else {
                                    $(that).hide();
                                }
                                $("#myenrolledcourse").append(response.html);
                                console.log("response- ", response);
                                // $(".reload").append("response")  
                            }
                        });
                    });
                  });
                    </script>
                ';
    

        $this->content->text= $html;    
    
    }

}




 


