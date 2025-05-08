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
 * @package    block_all_forum_list
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_all_forum_list extends block_base
{

    /**
     * @var bool Flag to indicate whether the header should be hidden or not.
     */
    private $headerhidden = true;

    function init()
    {
        $this->title = get_string('pluginname', 'block_all_forum_list');
    }
    function applicable_formats()
    {
        return array('all' => true);
    }

    function get_content () {
        global $DB,$USER,$CFG,$PAGE;
        require_once($CFG->dirroot."/course/externallib.php");
        $this->content = new stdClass;

        if(is_siteadmin()){
            $sql = 'SELECT f.id,f.course,f.name,c.fullname FROM {forum} f INNER JOIN {course} c ON f.course = c.id';
             $forums = $DB->get_records_sql($sql , array());
             $html = '
             <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
             
             <div class="row">
             <div class="col-8 mb-1"> </div>
                <div class="col-4 mb-1">
                    <select name="course" id="course1" style="padding:15px; width: 100%;">
                    <option value="0">Course</option>';
                    foreach ($forums as $forumdata) {
                    $html .= '<option value="'.$forumdata->course.'">'.$forumdata->fullname.'</option>';
                    }
            $html .= '</select>
                </div>
             </div>          
             <table class="table table-striped table-bordered" id="sorttable1" style="width:100%">
             <thead><tr><th>Course</th><th>Forum</th><th>Details</th></tr></thead><tbody>
             </tbody></table>
            
               <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
               <script>
            
                $(document).ready(function(){
            
                  $("#course1").change(function(){
                     dataTable.draw();
                  });
                  var dataTable = $("#sorttable1").DataTable({
                        "pageLength": 5,
                        paging: false,
                        "processing": true,
                        "serverSide": true,
                        "serverMethod": "post",
                        "ajax": {
                            "url":"'.$CFG->wwwroot.'/blocks/all_forum_list/ajax.php",
                            "data": function(data){
                              var courseid = $("#course").val();
                              data.searchByCourse = courseid;
                           }
                        },
                        "columns": [
                           { data: "fullname" },
                           { data: "name" },
                           { data: "link",orderable: false, targets: -1  },
                        ],
                        "drawCallback": function(settings) {
                            console.log(settings);
                            if(settings._iDisplayLength < settings._iRecordsDisplay){
                                $("[loadmoreforumlist]").show();
                            } else {
                                $("[loadmoreforumlist]").hide();
                            }
                        }
                     });
                });
             </script>
             ';
            $html .= '<div class="text-center"><a class="btn btn-secondary" style="display:none;" loadmoreforumlist href="'.$CFG->wwwroot.'/blocks/all_forum_list/index.php">View more</a></div>';
            $this->content->text= $html;    
        }

    
    }

}
