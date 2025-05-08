<?php
include_once("../../../config.php");
global $DB,$PAGE,$CFG,$USER;
$draw = $_POST['draw'];
$rowstart = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchByCourse = $_POST['searchByCourse']; // Search value
$searchByStatus = $_POST['searchByStatus']; // Search value
$post_var = strtolower($_POST['postVar']);
## Search 
// $searchQuery = array("reg.type = '$post_var' AND reg.status = 0 AND reg.assessor_status != 3");
// if($searchValue != ''){
// $searchQuery[] = "(ind.name LIKE '%$searchValue%' OR cor.name LIKE '%$searchValue%' OR reg.id LIKE '%$searchValue%' OR ind.email LIKE '%$searchValue%' OR cor.email LIKE '%$searchValue%') ";
// }
// $searchQuerystring = "";
// if(!empty($searchQuery)){
//   $searchQuerystring = " WHERE ".implode(" AND ", $searchQuery )." ";
// } 
// $orderQuery = " ORDER BY reg.id desc";
$allforums = array();
if(strtolower($_POST['postVar']) == 'individual'){
  $sql = "SELECT reg.id, reg.created_date, reg.type, reg.payment_status, reg.assessor_status, reg.status as rstatus, ind.name as name, ind.level, ind.email, lt.txn_id, lt.order_id, lt.postdata_before, lt.postdata_after, lt.status as tstatus FROM {lcl_registration} reg 
  INNER JOIN {lcl_individual_enrollment} ind ON  reg.id = ind.registration_id
  LEFT JOIN {lcl_transection} lt ON lt.registration_id = reg.id
  WHERE reg.type = '$post_var' AND ind.level > '2'  ORDER BY reg.id DESC";
  $sql_count = "SELECT COUNT(reg.id) FROM {lcl_registration} reg 
  INNER JOIN {lcl_individual_enrollment} ind ON  reg.id = ind.registration_id
  WHERE reg.type = '$post_var' AND ind.level > '2' ORDER BY reg.id DESC";
  // $sql_count = "SELECT COUNT(reg.id) FROM {lcl_registration} reg 
  // INNER JOIN {lcl_individual_enrollment} ind ON  reg.id = ind.registration_id
  // {$searchQuerystring} {$orderQuery} LIMIT {$rowstart}, {$rowperpage}";
  // $type = ' - Individual';
}
if(strtolower($_POST['postVar']) == 'corporate'){
  $sql = "SELECT reg.id, reg.created_date, reg.type, reg.payment_status, reg.assessor_status, reg.status as rstatus, cor.client_name as name, cor.level, cor.email, lt.txn_id, lt.order_id, lt.postdata_before, lt.postdata_after, lt.status as tstatus FROM {lcl_registration} reg 
  INNER JOIN {lcl_corporate_enrollment} cor ON reg.id = cor.registration_id
  LEFT JOIN {lcl_transection} lt ON lt.registration_id = reg.id
  WHERE reg.type = '$post_var' AND cor.level > '2'  AND reg.assessor_status != 3 ORDER BY reg.id DESC";
  $sql_count = "SELECT COUNT(reg.id) FROM {lcl_registration} reg 
  INNER JOIN {lcl_corporate_enrollment} cor ON reg.id = cor.registration_id
  WHERE reg.type = '$post_var' AND cor.level > '2'  AND reg.assessor_status != 3 ORDER BY reg.id DESC";
  // $sql_count = "SELECT COUNT(reg.id) FROM {lcl_registration} reg 
  // INNER JOIN {lcl_corporate_enrollment} cor ON reg.id = cor.registration_id
  // {$searchQuerystring} {$orderQuery} LIMIT {$rowstart}, {$rowperpage}";
  // $type = ' - Corporate';
}
$allforums = $DB->get_records_sql($sql, array());
$iTotalRecords = $DB->get_field_sql($sql_count);
$iTotalDisplayRecords = $DB->get_field_sql($sql_count);
foreach ($allforums as $key => $data) {
  $check_customfield_field_foundation_level = $DB->get_record("customfield_field", array('shortname'=>'foundation_level'));
  $options_json_decode = json_decode($check_customfield_field_foundation_level->configdata);
  $final_option_list = explode("\n",$options_json_decode->options);
  if($data->level < '3'){ $level = 0; }
  if($data->level > '2'){ $level = 1; }
  if($data->assessor_status == '0'){ $assessor_status = "<span class='text-success' id='blink".$data->id."'>New</span>"; }
  if($data->assessor_status == '1'){ $assessor_status = "<span class='text-success'>Accepted</span>"; }
  if($data->assessor_status == '2'){ $assessor_status = "<span class='text-danger'>Pending for modification</span>"; }
  if($data->assessor_status == '3'){ $assessor_status = "<span class='text-danger'>Rejected</span>"; }
  $lcl_modification_form = $DB->get_record('lcl_modification_form',array('registration_id'=> $data->id));
   $data->reg_no = $data->id;
   $data->name = $data->name;
   $data->email = $data->email;
   $data->date = date('M d, Y',$data->created_date);
   $remind = '';
   if($data->tstatus == 1){
       $data->payment_status = "<span id='paymentstatus" . $data->id . "'>Paid</span>";
       $approve = "<a type='button' class='approve".$data->id." mx-2' ><i id='spiner".$data->id."' class='fa fa-spinner fa-spin d-none'></i> Final Approve </a>";
   } else {
       $remind = "<a type='button' class='remind".$data->id." mx-2' data-toggle='tooltip' data-placement='top' title='Remind in email'> <i class='fa fa-bell' aria-hidden='true'></i> </a>";
       $data->payment_status = "<span onclick='showLoader()' id='paymentstatus" . $data->id . "'>Not paid ".$remind."</span>";
       $approve = "";
   }
   $data->status = "<span id='statustd" . $data->id . "'>" . $assessor_status . "</span>";
   $data->details = "<a href='".$CFG->wwwroot."/local/user_registration/admin/userdetails.php?id=".base64_encode($data->id)."' class='mx-2' target='_blank'>View</a>";
   
   if($data->rstatus == 1){
   $data->action = "<span style='color: blue;'>User created and enrolled</span>";
   }else{
   $data->action = "<div class='btn-group'>
                      <a type='button' class='accept".$data->id." mx-2' > Accept </a>
                      <a type='button' data-toggle='modal' data-target='#exampleModal".$data->id."' style='color: #0f47ad;'> Modify </a> 
                        <div class='modal fade' id='exampleModal".$data->id."' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                          <div class='modal-dialog' role='document'>
                            <div class='modal-content'>
                              <div class='modal-header'>
                                <h5 class='modal-title' id='exampleModalLabel'>Modification Suggest: ".$data->name."</h5>
                                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                  <span aria-hidden='true'>&times;</span>
                                </button>
                              </div>
                                <form>
                                <div class='modal-body'>
                                    <div class='mb-3'>
                                      <textarea class='form-control' rows='8' cols='100' id='suggestion".$data->id."'>".$lcl_modification_form->modification."</textarea>
                                    </div>
                                    <span id='suggestion_status".$data->id."'></span>
                                </div>
                                <div class='modal-footer'>
                                  <input type='hidden' value='".$data->id."' name='reg_id' id='reg_id".$data->id."'>
                                  <input type='hidden' value='".base64_encode($data->email)."' name='email'  id='email".$data->id."'>
                                  <button type='button' class='modify".$data->id." btn btn-primary' name='submit'><i id='spinersave".$data->id."' class='fa fa-spinner fa-spin d-none'></i> Save & Send</button>
                                </div>
                                </form>
                                </div>
                            </div>
                            </div>
                        </div>
                      <a type='button' class='reject".$data->id." mx-2' ><i id='spiner".$data->id."' class='fa fa-spinner fa-spin d-none'></i> Reject </a>
                      ".$approve." 
                      <script>
                       $('.approve".$data->id."').button().click(function(){
                        if (confirm('Are you sure !')) {
                        $('#loader').css('display', 'inline-block'); 
                          $.ajax({
                             url: '".$CFG->wwwroot."/local/user_registration/admin/enrolment.php',
                             type: 'POST',
                             data: { reg_id: $('#reg_id".$data->id."').val(), level: 2},
                             success: function(response) {
                                    $('#loader').css('display', 'none'); 
                                    $('#statustd".$data->id."').html('User created');
                                }
                              });
                           }
                        });
                        $('.accept".$data->id."').button().click(function(){
                        if (confirm('Are you sure !')) {
                        $('#loader').css('display', 'inline-block'); 
                          $.ajax({
                             url: '".$CFG->wwwroot."/local/user_registration/admin/accept.php',
                             type: 'POST',
                            data: { reg_id: $('#reg_id".$data->id."').val(), email: $('#email".$data->id."').val()},
                             success: function(response) {
                             	  $('#loader').css('display', 'none'); 
                                 var responsedata = JSON.parse(response);
                                 $('#statustd".$data->id."').html(responsedata.statustd);
                                 $('#status').html(responsedata.status);
                             }
                           });
                           }
                        });
                        $('.modify".$data->id."').button().click(function(){
                           $('#spinersave".$data->id."').removeClass('d-none');
                           $('#spinersave".$data->id."').addClass('inline-block'); 
                           $.ajax({
                              url: '".$CFG->wwwroot."/local/user_registration/admin/modify.php',
                              type: 'POST',
                              data: { reg_id: $('#reg_id".$data->id."').val(), email: $('#email".$data->id."').val(), post_var: $('#suggestion".$data->id."').val()},
                              success: function(response) {
                                  var responsedata = JSON.parse(response);
                                  $('#statustd".$data->id."').html(responsedata.statustd);
                                  $('#suggestion_status".$data->id."').html(responsedata.status);
                                  $('#suggestion".$data->id."').val(responsedata.value);
                                  $('#spinersave".$data->id."').removeClass('inline-block');
                                  $('#spinersave".$data->id."').addClass('d-none'); 
                              }
                            });
                        });
                        $('.reject".$data->id."').button().click(function(){
                          if (confirm('Are you sure !')) {
                             $('#loader').css('display', 'inline-block'); 
                              $.ajax({
                              url: '".$CFG->wwwroot."/local/user_registration/admin/reject.php',
                              type: 'POST',
                              data: { reg_id: $('#reg_id".$data->id."').val(), email: $('#email".$data->id."').val() },
                                success: function(response) {
                                 $('#loader').css('display', 'none'); 
                                var responsedata = JSON.parse(response);
                                $('#statustd".$data->id."').html(responsedata.statustd);
                                $('#status').html(responsedata.status);
                              }
                              });
                           } 
                         });
                        $('.remind".$data->id."').button().click(function(){
                            $('#loader').css('display', 'inline-block'); 
                            $.ajax({
                                url: '".$CFG->wwwroot."/local/user_registration/admin/remind.php',
                                type: 'POST',
                                data: { reg_id: $('#reg_id".$data->id."').val(), email: $('#email".$data->id."').val()  },
                                success: function(response) {
                                     var responsedata = JSON.parse(response);
                                     setTimeout(function () { 
                                        $('#status').html(responsedata.status);
                                        $('#loader').css('display', 'none');
                                     }, 3000);
                                }
                            });
                         });
                        function blink_text() {
                             $('#blink".$data->id."').fadeOut(500);
                             $('#blink".$data->id."').fadeIn(500);
                        }
                        setInterval(blink_text, 1000);
                      </script>";
   			}
       $allforums[$key] = $data;
  }
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $iTotalRecords,
    "iTotalDisplayRecords" => $iTotalDisplayRecords,
    "aaData" => array_values($allforums),
    'type'=>$type
);
echo json_encode($response);

