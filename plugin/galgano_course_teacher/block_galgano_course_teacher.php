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

class block_galgano_course_teacher extends block_base
{

    /**
     * @var bool Flag to indicate whether the header should be hidden or not.
     */
    private $headerhidden = true;

    function init()
    {
        $this->title = get_string('pluginname', 'block_galgano_course_teacher');
    }
    function applicable_formats()
    {
        return array('all' => true);
    }

    function get_content()
    {
        global $DB, $CFG, $PAGE;
        require_once($CFG->dirroot . "/course/externallib.php");
        $this->content = new stdClass;
        // $COURSEAPI = new core_course_external();
            $cousreId =  $id  = optional_param('id', 0, PARAM_INT);
            // $cousreId = 100;
           $quertech=	"SELECT u.id as teacherId, c.id as courseId FROM {user} u INNER JOIN {role_assignments} ra ON ra.userid = u.id INNER JOIN {context} ct ON ct.id = ra.contextid INNER JOIN {course} c ON c.id = ct.instanceid INNER JOIN {role} r ON r.id = ra.roleid WHERE r.id = 3 and c.id = $cousreId";
            $enroltech = $DB->get_records_sql($quertech);
           

            foreach ($enroltech as $erlusers) {                  
            $enlluser = $erlusers->teacherid;
            if ($enlluser) {
                $techdetail = "SELECT * FROM {user} where id=$enlluser";
                $infotech = $DB->get_record_sql($techdetail);
            }
            }
            

            $sqlq = "SELECT * FROM {role_assignments} rc INNER JOIN {context} c ON rc.contextid = c.id WHERE  rc.roleid = 3 AND rc.userid = $infotech->id";
            $allcour = $DB->get_records_sql($sqlq);
           $alluser = array();
           $context_arr=array();
            foreach ($allcour as $user) {
                array_push($context_arr,$user->contextid);
            }

            $usercontexts=implode(',',$context_arr);
            $queryy = "SELECT *, COUNT(userid) as count  FROM {role_assignments} WHERE contextid IN($usercontexts) and roleid = 5";
            $allstudent = $DB->get_record_sql($queryy);

            //  echo "<pre>";
            //  print_r($allstudent->count);
            // die;
            $userpicture = new user_picture($infotech);
            $userpicture->size = 1; // Size f1.
            $img = $userpicture->get_url($PAGE)->out(false);
            
        $html ='
        <style>
            .row{
                display : flex !important;
            }

            .small-center{
                display : flex;
                align-items : center;
                justify-content : center;
            }

            @media(max-width : 567px) {
                .detail-tab {
                    text-align : center;
                }
            }
        </style>
        <div id="teacher-info">
            <div class="card" data-toggle="collapse" data-target="#info-collapse-1" aria-expanded="true" aria-controls="info-collapse-1">
                <div class="card-header" id="info-tab-1">
                    <h3 class="mb-0">Informazioni sul Docente</h3>
                </div>

            <div id="info-collapse-1" class="collapse show" aria-labelledby="info-tab-1" data-parent="#teacher-info">
                <div class="card-body">
                   <div class="row">
                        <div class="col-12 col-lg-4 col-md-3">
                        <div class="small-center">
                            <img class="rounded-circle" alt="avatar1" src="'.$img.'" />
                        </div>
                        </div>
                        <div class="col-12 col-lg-8 col-md-9">
                            <div class="row my-3">
                                <div class="col-6 col-lg-3 col-md-6">
                                    <div class="detail-tab">
                                    <span class="flaticon-profile"></span>
                                    <span>'.$allstudent->count.' Studenti</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3 col-md-4">
                                    <div class="detail-tab">
                                        <span class="flaticon-play-button-1"></span>
                                        <span>'.count($allcour).' Corsi</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 detail-tab">
                                    <h2>
                                    '.$infotech->firstname.' '.$infotech->lastname.'
                                    </h2>
                                    <p>
                                    '.$infotech->description.'
                                    </p>
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
            </div>
        </div>
      ';

        $this->content->text = $html;
    }
}
