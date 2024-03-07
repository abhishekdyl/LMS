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
      <div class="col-8"><h3 class="mb-5">Forum list</h3></div>
         <div class="col-4">
             <select name="course" id="course" style="padding:15px">
             <option value="0">Course</option>';
             foreach ($forums as $key) {
             $html .= '<option value="'.$key->course.'">'.$key->fullname.'</option>';
             }
     $html .= '</select>
         </div>
      </div>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" />
      <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" />
      
      <table class="table table-striped table-bordered" id="sorttable" style="width:100%">';
      $html .= '<thead><tr><th>Course</th><th>Forum</th><th>Details</th></tr></thead><tbody>';
      $html .= '</tbody></table>
     
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script>
     
         $(document).ready(function(){
     
           $("#course").change(function(){
              dataTable.draw();
           });
           var dataTable = $("#sorttable").DataTable({
                 "processing": true,
                 "serverSide": true,
                 "serverMethod": "post",
                 "ajax": {
                     "url":"ajax.php",
                     "data": function(data){
                       var courseid = $("#course").val();
                       data.searchByCourse = courseid;
                    }
                 },
                 "columns": [
                    { data: "fullname" },
                    { data: "name" },
                    { data: "link",orderable: false, targets: -1  },
                 ]
              });
         });
         
      </script>
      ';    
   }

echo $OUTPUT->header();
echo $html;
echo $OUTPUT->footer();



?>












