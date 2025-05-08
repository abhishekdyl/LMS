<?php 
require_once('../../config.php');
global $DB, $PAGE, $CFG, $OUTPUT; 

$pageurl = $CFG->wwwroot."/local/user_registration/home.php?type=".$type;
$PAGE->requires->jquery();
$PAGE->set_url($CFG->wwwroot.'/local/user_registration/home.php?type='.$type);
// $PAGE->set_pagelayout('standard');
$PAGE->set_title('Registration Form - Home');
$PAGE->set_heading('<div>Course Registration Summary</div>');
$id = required_param('id', PARAM_TEXT);
$tap_id = optional_param('tap_id', '', PARAM_TEXT);
$reg_id = base64_decode($id);
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/user_registration/style.css'));

if($DB->record_exists('lcl_individual_enrollment', array('registration_id'=>$reg_id))) {
     $sql = "SELECT ind.*, c.fullname as fullname, c.shortname as shortname, cc.name as category, reg.payment_status, reg.assessor_status, 
            reg.status, lt.txn_id, lt.order_id, lt.postdata_before, lt.postdata_after, lt.status as tstatus 
            FROM {lcl_individual_enrollment} ind 
            INNER JOIN {lcl_registration} reg ON reg.id = ind.registration_id
            LEFT JOIN {lcl_transection} lt ON lt.registration_id = reg.id
            INNER JOIN {course} c ON c.id = ind.course_id
            INNER JOIN {course_categories} cc ON cc.id = c.category
            WHERE reg.id = '$reg_id'";
            $chk_data = $DB->get_record_sql($sql);
            $html = '';
            if(!empty($chk_data->start_date)){ $start_date =  date('Y-m-d', $chk_data->start_date); }
            if(!empty($chk_data->end_date)){ $end_date = date('Y-m-d', $chk_data->end_date); }
            if(!empty($chk_data->exam_date)){ $exam_date = date('Y-m-d', $chk_data->exam_date); }
            $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Basic Details - Individual</strong></div>
                      <table id="headerTable" class="table table-bordered" style="width: 100%;">
                          <tbody>
                                  <tr><th>Course Name</th> <td>'.$chk_data->fullname.'</td></tr>
                                  <tr><th>Course Level</th> <td>'.$chk_data->level.'</td></tr>
                                  <tr><th>Course Timing</th> <td>'.$chk_data->course_timing.'</td></tr>
                                  <tr><th>Course Venue</th> <td>'.$chk_data->course_location.'</td></tr>
                                  <tr><th>Course Price</th> <td>'.$chk_data->course_price.'</td></tr>
                                  <tr><th>Start Date</th> <td>'.$start_date.'</td></tr>
                                  <tr><th>End Date</th> <td>'.$end_date.'</td></tr>
                                  <tr><th>Exam Date</th> <td>'.$exam_date.'</td></tr>
                          </tbody>
                      </table>';
            $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Personal Details</strong></div>
                      <table id="headerTable" class="table table-bordered" style="width: 100%;">
                          <tbody>
                                  <tr><th>Registration No.</th> <td>'.$reg_id.'</td></tr>
                                  <tr><th>Name</th> <td>'.$chk_data->name.'</td></tr>
                                  <tr><th>Email</th> <td>'.$chk_data->email.'</td> </tr>
                                  <tr><th>Mobile Number</th> <td>'.$chk_data->mobile_number.'</td> </tr>
                                  <tr><th>Alternative Mobile Number</th> <td>'.$chk_data->other_phone.'</td></tr>
                                  <tr><th>Sponsor</th> <td>'.$chk_data->sponsor.'</td> </tr>
                                  <tr><th>Date Of Birth</th> <td>'.date('Y-m-d', $chk_data->date_of_birth).'</td></tr>
                                  <tr><th>CPR</th> <td>'.$chk_data->cpr.'</td></tr>
                                  <tr><th>Job Title</th> <td>'.$chk_data->job_title.'</td></tr>
                                  <tr><th>Referrel By</th> <td>'.$chk_data->referrel_by.'</td></tr>
                                  <tr><th>Major</th> <td>'.$chk_data->major.'</td></tr>
                                  <tr><th>University</th> <td>'.$chk_data->university.'</td></tr>
                          </tbody>
                      </table>';
              
              if($chk_data->level <= 2){
                if($chk_data->payment_status == 0) {
                    $html .= '<div class="row">
                                  <div class="col-md-12">
                                      <h2>Choose a payment method'.$payment_status.'</h2>
                                      <input type="radio" name="tap" value="tap" checked/> <strong>Card payment - tap payment</strong>
                                  </div>
                              </div>';
                    $html .= '<div class="row">
                                  <div class="col-md-12" align="right">
                                    <a id="paymentButton" class="btn btn-primary"
                                        href="'.$CFG->wwwroot.'/local/user_registration/payment.php?id='.base64_encode($reg_id).'">Click
                                        to pay here</a>                        
                                  </div>
                              </div>';
                } elseif ($chk_data->payment_status == 1) {
                    $postdata_after = $chk_data->postdata_after;
                    $unserialize = unserialize($postdata_after);
                    $payment_status = $unserialize['status'];
                    $return_tap_id = $unserialize['id'];
                    $amount = $unserialize['amount'];
                    $currency = $unserialize['currency'];
                    $tap_auto_order = $unserialize['order']->id;
                    $transaction = $unserialize['reference']->transaction;
                    $order = $unserialize['reference']->order;
                    $receipt = $unserialize['receipt']->id;
                    $email = $unserialize['customer']->email;
                    $first_name = $unserialize['customer']->first_name;
                    $transaction_date = $unserialize['activities'][1]->created;
                    $transaction_date = $transaction_date / 1000;
                    $transaction_date = date("Y-m-d", $transaction_date);
                    $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Payment Details</strong></div>
                              <table id="headerTable" class="success-table table table-bordered" style="width: 100%;">
                                  <tbody>
                                        <tr><th>Payment Status</th> <td><p class="text-success">Payment successful!</p></td></tr>
                                        <tr><th>Customer Name</th> <td>'.$first_name.'</td></tr>
                                        <tr><th>Customer Email</th> <td>'.$email.'</td></tr>
                                        <tr><th>Transaction ID</th> <td>'.$transaction.'</td></tr>
                                        <tr><th>Amount</th> <td>'.$amount." ".$currency.'</td></tr>
                                        <tr><th>Transaction Date</th> <td>'.$transaction_date.'</td></tr>
                                  </tbody>
                              </table>';
                    $html .='<button class="btn btn-primary" onclick="printDiv()">Print Here</button>';

                } else {
                  $html .= 'Something is wrong !';
                }

            } else if($chk_data->level > 2) {
                if($chk_data->assessor_status == 0) {
                  $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Application Status</strong></div>
                            <table id="headerTable" class="table table-bordered" style="width: 100%;">
                                  <tbody>
                                        <tr><th>Status</th> <td><p class="text-danger">Application applied successfully. Please check your mail for further updates, Assessor need to approve to proceed next</p></td></tr>
                                  </tbody>
                            </table>';
                } elseif ($chk_data->assessor_status == 1) {
                    if($chk_data->tstatus == 0) {
                        $html .= '<div class="row">
                                      <div class="col-md-12">
                                          <h2>Choose a payment method'.$payment_status.'</h2>
                                          <input type="radio" name="tap" value="tap" checked/> <strong>Card payment - tap payment</strong>
                                      </div>
                                  </div>';
                        $html .= '<div class="row">
                                      <div class="col-md-12" align="right">
                                        <a id="paymentButton" class="btn btn-primary"
                                            href="'.$CFG->wwwroot.'/local/user_registration/payment.php?id='.base64_encode($reg_id).'">Click
                                            to pay here</a>                        
                                      </div>
                                  </div>';
                    } elseif ($chk_data->tstatus == 1) {
                        $postdata_after = $chk_data->postdata_after;
                        $unserialize = unserialize($postdata_after);
                        $payment_status = $unserialize['status'];
                        $return_tap_id = $unserialize['id'];
                        $amount = $unserialize['amount'];
                        $currency = $unserialize['currency'];
                        $tap_auto_order = $unserialize['order']->id;
                        $transaction = $unserialize['reference']->transaction;
                        $order = $unserialize['reference']->order;
                        $receipt = $unserialize['receipt']->id;
                        $email = $unserialize['customer']->email;
                        $first_name = $unserialize['customer']->first_name;
                        $transaction_date = $unserialize['activities'][1]->created;
                        $transaction_date = $transaction_date / 1000;
                        $transaction_date = date("Y-m-d", $transaction_date);
                        $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Payment Details</strong></div>
                                  <table id="headerTable" class="success-table table table-bordered" style="width: 100%;">
                                      <tbody>
                                            <tr><th>Payment Status</th> <td><p class="text-success">Payment successful!</p></td></tr>
                                            <tr><th>Customer Name</th> <td>'.$first_name.'</td></tr>
                                            <tr><th>Customer Email</th> <td>'.$email.'</td></tr>
                                            <tr><th>Transaction ID</th> <td>'.$transaction.'</td></tr>
                                            <tr><th>Amount</th> <td>'.$amount." ".$currency.'</td></tr>
                                            <tr><th>Transaction Date</th> <td>'.$transaction_date.'</td></tr>
                                      </tbody>
                                  </table>';
                        $html .='<button class="btn btn-primary" onclick="printDiv()">Print Here</button>';
                  } else {
                        $html .= 'Something is wrong !';
                  }
                } elseif ($chk_data->assessor_status == 2) {
                    $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Application Status</strong></div>
                              <table id="headerTable" class="table table-bordered" style="width: 100%;">
                                    <tbody>
                                          <tr><th>Status</th> <td><p class="text-danger">An assessor has suggested some modification. Please check your mail for further updates.</p></td></tr>
                                    </tbody>
                              </table>';
                } else {
                  $html .= 'Something is wrong !';
                }
            } else {
              $html .= 'Something is wrong !';
        }
}
if($DB->record_exists('lcl_corporate_enrollment', array('registration_id'=>$reg_id))) {
    $sql = "SELECT cor.*, c.fullname as fullname, c.shortname as shortname, cc.name as category, reg.payment_status, reg.assessor_status, 
            reg.status, lt.txn_id, lt.order_id, lt.postdata_before, lt.postdata_after, lt.status as tstatus FROM {lcl_corporate_enrollment} cor 
            INNER JOIN {lcl_registration} reg ON reg.id = cor.registration_id
            INNER JOIN {course} c ON c.id = cor.course_id
            INNER JOIN {course_categories} cc ON cc.id = c.category
            LEFT  JOIN {lcl_transection} lt ON lt.registration_id = reg.id
            WHERE reg.id = '$reg_id'";
            $chk_data = $DB->get_record_sql($sql);
            $html  = '';
            if(!empty($chk_data->start_date)){ $start_date = date('Y-m-d', $chk_data->start_date); }
            if(!empty($chk_data->end_date)){ $end_date = date('Y-m-d', $chk_data->end_date); }
            if(!empty($chk_data->exam_date)){ $exam_date = date('Y-m-d', $chk_data->exam_date); }
            $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Basic Details - Corporate</strong></div>
                      <table id="headerTable" class="table table-bordered" style="width: 100%;">
                          <tbody>
                                  <tr><th>Course Name</th> <td>'.$chk_data->fullname.'</td></tr>
                                  <tr><th>Course Level</th> <td>'.$chk_data->level.'</td></tr>
                                  <tr><th>Course Timing</th> <td>'.$chk_data->course_timing.'</td></tr>
                                  <tr><th>Course Venue</th> <td>'.$chk_data->course_location.'</td></tr>
                                  <tr><th>Course Price</th> <td>'.$chk_data->course_price.'</td></tr>
                                  <tr><th>Start Date</th> <td>'.$start_date.'</td></tr>
                                  <tr><th>End Date</th> <td>'.$end_date.'</td></tr>
                                  <tr><th>Exam Date</th> <td>'.$exam_date.'</td></tr>
                          </tbody>
                      </table>';
            $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Personal Details</strong></div>
                      <table id="headerTable" class="table table-bordered" style="width: 100%;">
                          <tbody>
                                  <tr><th>Registration No.</th> <td>'.$reg_id.'</td></tr>
                                  <tr><th>Client Name</th> <td>'.$chk_data->client_name.'</td></tr>
                                  <tr><th>Contact Person</th> <td>'.$chk_data->contact_person.'</td></tr>
                                  <tr><th>Job Title</th> <td>'.$chk_data->job_title.'</td></tr>
                                  <tr><th>Email</th> <td>'.$chk_data->email.'</td> </tr>
                                  <tr><th>P.O Box</th> <td>'.$chk_data->po_box.'</td> </tr>
                                  <tr><th>Mobile Number</th> <td>'.$chk_data->mobile_number.'</td> </tr>
                                  <tr><th>Other Number</th> <td>'.$chk_data->work_phone.'</td></tr>
                                  <tr><th>Sponsoring Organisation </th> <td>'.$chk_data->sponsor_organisation.'</td> </tr>
                          </tbody>
                      </table>';


            if($chk_data->level <= 2) {
              if($chk_data->payment_status == 0) {
                  $html .= '<div class="row">
                              <div class="col-md-12">
                                <h2>Choose a payment method</h2>
                                <input type="radio" name="tap" value="tap" checked /> <strong>Card payment - tap payment</strong></br>
                                <input type="radio" name="tap" value="lpo" /> <strong>LPO</strong></br>
                                <input type="radio" name="tap" value="ooc" /> <strong>OOC</strong></br>
                              </div>
                            </div>';
                  $html .= '<div class="row">
                              <div class="col-md-12" align="right">
                                 <button id="fileButton" class="btn btn-success">File Upload</button>                        
                                 <button id="paymentButton" class="btn btn-primary">Click to pay here</button>                        
                              </div>
                            </div>';
                  $html .= '<script type="text/javascript">
                              $(document).ready(function() {
                                $("#fileButton").css("display", "none");
                                $("#paymentButton").click(function() {
                                  window.location.href = "'.$CFG->wwwroot.'/local/user_registration/payment.php?id='.base64_encode($reg_id).'";
                              });
                              $("input[name=tap]").change(function() {
                                  var nameradioval = $("input[name=tap]:checked").val();
                                  if (nameradioval == "lpo" || nameradioval == "ooc") {
                                    $("#paymentButton").prop("disabled", true);
                                    $("#fileButton").css("display", "inline-block");
                                  } else if (nameradioval == "tap") {
                                    $("#paymentButton").prop("disabled", false);
                                    $("#fileButton").css("display", "none");
                                  }
                                });
                              });
                            </script>';
              } elseif ($chk_data->payment_status == 1) {
                  $postdata_after = $chk_data->postdata_after;
                  $unserialize = unserialize($postdata_after);
                  $payment_status = $unserialize['status'];
                  $return_tap_id = $unserialize['id'];
                  $amount = $unserialize['amount'];
                  $currency = $unserialize['currency'];
                  $tap_auto_order = $unserialize['order']->id;
                  $transaction = $unserialize['reference']->transaction;
                  $order = $unserialize['reference']->order;
                  $receipt = $unserialize['receipt']->id;
                  $email = $unserialize['customer']->email;
                  $first_name = $unserialize['customer']->first_name;
                  $transaction_date = $unserialize['activities'][1]->created;
                  $transaction_date = $transaction_date / 1000;
                  $transaction_date = date("Y-m-d", $transaction_date);
                  $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Payment Details</strong></div>
                            <table id="headerTable" class="success-table table table-bordered" style="width: 100%;">
                                <tbody>
                                        <tr><th>Payment Status</th> <td><p class="text-success">Payment successful!</p></td></tr>
                                        <tr><th>Customer Name</th> <td>'.$first_name.'</td></tr>
                                        <tr><th>Customer Email</th> <td>'.$email.'</td></tr>
                                        <tr><th>Transaction ID</th> <td>'.$transaction.'</td></tr>
                                        <tr><th>Amount</th> <td>'.$amount." ".$currency.'</td></tr>
                                        <tr><th>Transaction Date</th> <td>'.$transaction_date.'</td></tr>
                                </tbody>
                            </table>';
                  $html .='<button class="btn btn-primary" onclick="printDiv()">Print Here</button>';

              } else {
                  $html .= 'Something is wrong !';
              }

            } elseif ($chk_data->level > 2) {
                if($chk_data->assessor_status == 0) {
                  $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Application Status</strong></div>
                            <table id="headerTable" class="table table-bordered" style="width: 100%;">
                                  <tbody>
                                        <tr><th>Status</th> <td><p class="text-danger">Application applied successfully. Please check your mail for further updates, Assessor need to approve to proceed next</p></td></tr>
                                  </tbody>
                            </table>';
                } elseif ($chk_data->assessor_status == 1) {
                    if($chk_data->tstatus == 0) {
                        $html .= '<div class="row">
                                    <div class="col-md-12">
                                      <h2>Choose a payment method</h2>
                                      <input type="radio" name="tap" value="tap" checked /> <strong>Card payment - tap payment</strong></br>
                                      <input type="radio" name="tap" value="lpo" /> <strong>LPO</strong></br>
                                      <input type="radio" name="tap" value="ooc" /> <strong>OOC</strong></br>
                                    </div>
                                  </div>';
                        $html .= '<div class="row">
                                    <div class="col-md-12" align="right">
                                       <button id="fileButton" class="btn btn-success">File Upload</button>                        
                                       <button id="paymentButton" class="btn btn-primary">Click to pay here</button>                        
                                    </div>
                                  </div>';
                        $html .= '<script type="text/javascript">
                                    $(document).ready(function() {
                                      $("#fileButton").css("display", "none");
                                      $("#paymentButton").click(function() {
                                        window.location.href = "'.$CFG->wwwroot.'/local/user_registration/payment.php?id='.base64_encode($reg_id).'";
                                    });
                                    $("input[name=tap]").change(function() {
                                        var nameradioval = $("input[name=tap]:checked").val();
                                        if (nameradioval == "lpo" || nameradioval == "ooc") {
                                          $("#paymentButton").prop("disabled", true);
                                          $("#fileButton").css("display", "inline-block");
                                        } else if (nameradioval == "tap") {
                                          $("#paymentButton").prop("disabled", false);
                                          $("#fileButton").css("display", "none");
                                        }
                                      });
                                    });
                                  </script>';
                    } elseif ($chk_data->tstatus == 1) {
                        $postdata_after = $chk_data->postdata_after;
                        $unserialize = unserialize($postdata_after);
                        $payment_status = $unserialize['status'];
                        $return_tap_id = $unserialize['id'];
                        $amount = $unserialize['amount'];
                        $currency = $unserialize['currency'];
                        $tap_auto_order = $unserialize['order']->id;
                        $transaction = $unserialize['reference']->transaction;
                        $order = $unserialize['reference']->order;
                        $receipt = $unserialize['receipt']->id;
                        $email = $unserialize['customer']->email;
                        $first_name = $unserialize['customer']->first_name;
                        $transaction_date = $unserialize['activities'][1]->created;
                        $transaction_date = $transaction_date / 1000;
                        $transaction_date = date("Y-m-d", $transaction_date);
                        $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Payment Details</strong></div>
                                  <table id="headerTable" class="success-table table table-bordered" style="width: 100%;">
                                      <tbody>
                                              <tr><th>Payment Status</th> <td><p class="text-success">Payment successful!</p></td></tr>
                                              <tr><th>Customer Name</th> <td>'.$first_name.'</td></tr>
                                              <tr><th>Customer Email</th> <td>'.$email.'</td></tr>
                                              <tr><th>Transaction ID</th> <td>'.$transaction.'</td></tr>
                                              <tr><th>Amount</th> <td>'.$amount." ".$currency.'</td></tr>
                                              <tr><th>Transaction Date</th> <td>'.$transaction_date.'</td></tr>
                                      </tbody>
                                  </table>';
                        $html .='<button class="btn btn-primary" onclick="printDiv()">Print Here</button>';

                    } else {
                        $html .= 'Something is wrong !';
                  }
                } elseif ($chk_data->assessor_status == 2) {
                  $html .= '<div style="background-color: skyblue; padding: 12px;"><strong>Application Status</strong></div>
                            <table id="headerTable" class="table table-bordered" style="width: 100%;">
                                  <tbody>
                                        <tr><th>Status</th> <td><p class="text-danger">An assessor has suggested some modification. Please check your mail for further updates.</p></td></tr>
                                  </tbody>
                            </table>';
                } else {
                  $html .= 'Something is wrong !';
                }
            } else {
              $html .= 'Something is wrong !';
        }           
}

echo $OUTPUT->header();
echo $html;
echo $OUTPUT->footer();
?>
<script>
    function printDiv() {
        var printContents = document.getElementsByClassName("success-table")[0].outerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>