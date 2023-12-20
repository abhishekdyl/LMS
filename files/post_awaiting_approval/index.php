<?php
include_once("../../config.php");
global $DB,$PAGE,$CFG,$USER;
$PAGE->requires->jquery();
if(is_siteadmin()){
   $sql = 'SELECT f.id,f.course,f.name,c.fullname FROM {forum} f INNER JOIN {course} c ON f.course = c.id';
   $forums = $DB->get_records_sql($sql , array());
   $html = '
   <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
   
   <div class="row">
   <div class="col-6"><h5 class="mb-5">Post awaiting approval list</h5></div>
      <div class="col-3">
          <select name="poststatus" id="poststatus" style="padding:15px; width: 100%;">
          <option value="2">All</option>
          <option value="1">Approved</option>
          <option value="0">Awaiting approve</option>
          </select>
      </div>
      <div class="col-3">
          <select name="course" id="course2" style="padding:15px; width: 100%;">
          <option value="0">All Course</option>';
          foreach ($forums as $forumkey) {
          $html .= '<option value="'.$forumkey->course.'">'.$forumkey->fullname.'</option>';
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
           $("#course2").change(function(){
              dataTable.draw();
           });
              var dataTable = $("#sorttable").DataTable({
              "order":[[4, "desc"], [0, "asc"]],
              "processing": true,
              "serverSide": true,
              "serverMethod": "post",
              "ajax": {
                  "url":"'.$CFG->wwwroot.'/blocks/post_awaiting_approval/ajax.php",
                  "data": function(data){
                      data.searchByCourse = $("#course2").val();
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
              ]
           });
      });
      
   </script>
   '; 
   echo $OUTPUT->header();
   echo $html;
   echo $OUTPUT->footer();
}


?>












