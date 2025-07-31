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
 * @package    block_post_awaiting_approval
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_post_awaiting_approval extends block_base
{

    /**
     * @var bool Flag to indicate whether the header should be hidden or not.
     */
    private $headerhidden = true;

    function init()
    {
        $this->title = get_string('pluginname', 'block_post_awaiting_approval');
    }
    function applicable_formats()
    {
        return array('all' => true);
    }

    function get_content () {
        global $DB,$USER,$CFG,$PAGE;
        if(is_siteadmin()){
            require_once($CFG->dirroot."/course/externallib.php");
            $this->content = new stdClass;

                $sql = 'SELECT f.id,f.course,f.name,c.fullname FROM {forum} f INNER JOIN {course} c ON f.course = c.id';
                 $forums = $DB->get_records_sql($sql , array());
                 $html = '
                 <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
                 
                 <div class="row">
                 <div class="col-6 mb-1"> </div>
                    <div class="col-3 mb-1 ">
                        <select name="poststatus" id="poststatus" style="padding:15px; width: 100%;">
                        <option value="2">All</option>
                        <option value="1">Approved</option>
                        <option value="0">Awaiting approve</option>
                        </select>
                    </div>
                    <div class="col-3 mb-1 ">
                        <select name="course" id="course" style="padding:15px; width: 100%;">
                        <option value="0">All Course</option>';
                        foreach ($forums as $key) {
                        $html .= '<option value="'.$key->course.'">'.$key->fullname.'</option>';
                        }
                $html .= '</select>
                    </div>
                 </div>
                 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" />
                 <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" />
                 
                 <table class="table table-striped table-bordered" id="sorttable" style="width:100%">';
                 $html .= '<thead><tr><th>Course</th><th>Forum</th><th>discuss</th><th>Status</th><th>Date</th><th>Details</th></tr></thead><tbody>';
                 $html .= '</tbody></table>
                
                   <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
                   <script>
                
                    $(document).ready(function(){
                
                        $("#poststatus").change(function(){
                            dataTable.draw();
                         });
                         $("#course").change(function(){
                            dataTable.draw();
                         });
                            var dataTable = $("#sorttable").DataTable({
                            "order":[[4, "desc"], [0, "asc"]],
                            "pageLength": 5,
                            paging: false,
                            "processing": true,
                            "serverSide": true,
                            "serverMethod": "post",
                            "ajax": {
                                "url":"'.$CFG->wwwroot.'/blocks/post_awaiting_approval/ajax.php",
                                "data": function(data){
                                    data.searchByCourse = $("#course").val();
                                    data.searchByStatus = $("#poststatus").val();
                                }
                            },
                            "columns": [
                               { data: "fullname" },
                               { data: "name" },
                               { data: "subject" },
                               { data: "approved" },
                               { data: "modified" },
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
                $html .= '<div class="text-center"><a class="btn btn-secondary" style="display:none;" loadmoreforumlist href="'.$CFG->wwwroot.'/blocks/post_awaiting_approval/index.php">View more</a></div>';
            $this->content->text= $html;    
        }

    
    }

}
