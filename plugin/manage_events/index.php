<?php
require_once('../../config.php');
global $DB, $CFG, $USER, $PAGE;
$PAGE->requires->jquery();
require_login();
if(is_siteadmin()){
  
  $html ='
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
  <style>
  .fbutton{
    padding:10px 20px; 
    margin:5px;
  }
  </style>
  <div  style="text-align: end;"> <a href="'.$CFG->wwwroot.'/local/manage_events/events.php"><button class="btn btn-primary fbutton">Add Event</button></a> </div>
      <table class="table" id="sotable">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Description</th>
                <th>Event Date</th>
                <th>Action</th>
                <th></th>
            </tr>
        </thead>
        <tbody>';
  $html .='</tbody></table>

  <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
  <script>

  $(document).ready(function(){
      var deleteid = 0;
      var editid = 0;
    $(document).on("click", ".del", function(){
      deleteid = $(this).val();
      dataTable.draw();
    });  
    $(document).on("click", ".edit", function(){
      editid = $(this).val();
      dataTable.draw();
    });

    var dataTable = $("#sotable").DataTable({
          "order":[[2, "desc"], [0, "asc"]],
          "paging": true,
          "processing": true,
          "serverSide": true,
          "serverMethod": "post",
          "ajax": {
              "url":"ajax.php",
              "data": function(data){
                data.del_event_id = deleteid;
                data.edit_event_id = editid;
                deleteid = 0;
                editid = 0;
              }
          },
          "columns": [
              { data: "name" },
              { data: "description" },
              { data: "timestart" },
              { data: "edit",orderable: false, targets: -1  },
              { data: "link",orderable: false, targets: -1  },
          ]
      });
    

  });
  
  </script>';


  echo $OUTPUT->header();   
  echo $html;   
  echo $OUTPUT->footer();
}