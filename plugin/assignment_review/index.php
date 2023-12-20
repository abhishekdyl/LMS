<style type="text/css">

.active, .collapsible:hover {
    background-color: #ccc;
}

.content {
    padding: 0 18px;
    display: none;
    background-color: #f1f1f1;
    color: black;
    margin-top: 8px;
}

#ovflow {
    overflow-x: scroll;
    height: 400px;
    width: 800px;
}

#tdd {
    font-weight: bold;
    background-color: cadetblue;
    color: chartreuse;
    font-size: 25px;
    text-shadow: 2px 2px #000;
}

</style>
<?php

require_once ('../../config.php');
$CFG->libdir."/filelib.php";

global $DB, $CFG, $USER, $PAGE;

$PAGE->requires->jquery();
require_login();

$current_logged_in_user =  $USER->id;

function dispaly_array_response($json_response)
{

foreach($json_response as $key => $value){

    if($key=='generated_prob'){ $generated_prob=$value; }

    if(is_array($value)){
       dispaly_array_response($value);
    }else{

        if($key!='generated_prob' AND $key!='perplexity'){
  
    ?>

    <tr>
        <td><b><?php echo $key; ?></b></td>
        <td <?php if($generated_prob==1){ echo 'style="background-color: yellow;"'; } ?>><?php echo $value; ?></td>
    </tr>

    <?php

         }

    }
}
}


echo $OUTPUT->header();
// $cousreId =  $id  = optional_param('id', 0, PARAM_INT);
$assignId = optional_param('assignid', 0, PARAM_INT);
// $cousreId = 1;

$quertech=	"SELECT u.id as teacherId, c.id as courseId FROM {user} u INNER JOIN {role_assignments} ra ON ra.userid = u.id INNER JOIN {context} ct ON ct.id = ra.contextid INNER JOIN {course} c ON c.id = ct.instanceid INNER JOIN {role} r ON r.id = ra.roleid WHERE r.id = 3 AND u.id = $current_logged_in_user";
$enroltech = $DB->get_records_sql($quertech);


// $quertech=  "SELECT u.id as teacherId, c.id as courseId FROM {user} u INNER JOIN {role_assignments} ra ON ra.userid = u.id INNER JOIN {context} ct ON ct.id = ra.contextid INNER JOIN {course} c ON c.id = ct.instanceid INNER JOIN {role} r ON r.id = ra.roleid WHERE r.id = 3 and c.id = $cousreId";
// $enroltech = $DB->get_records_sql($quertech);

foreach ($enroltech as $erlusers) {                  
    $enlluser = $erlusers->teacherid;
    if ($enlluser) {
        $techdetail = "SELECT * FROM {user} where id=$enlluser";
        $infotech = $DB->get_record_sql($techdetail);
            $user_name = $infotech->id;
           

    }
}


if($user_name == $current_logged_in_user){

$assignment = $DB->get_records('assign',array ());

$html = '<label class="h4" for="assign">Select Assignment:</label>
        <select class="h5" name="assign" id="assign" style="padding: 5px; width: 40%;">
        <option>Choose</option>';
foreach ($assignment as $key) {
$html.= '<option value="'.$key->id.'">'.$key->name.'</option>';
}
$html.= '</select>';

echo $html;

if(!empty($assignId)){

 $query = 'SELECT s.*, ass.name, u.firstname, u.lastname, u.email, f.filename, f.mimetype, aso.onlinetext, s.userid FROM mdl_assign_submission s
    INNER JOIN mdl_user as u on u.id = s.userid
    LEFT JOIN mdl_assign ass on ass.id = s.assignment
    LEFT JOIN mdl_assignsubmission_file asf on asf.submission = s.id AND asf.assignment = s.assignment
    LEFT JOIN mdl_assignsubmission_onlinetext aso on aso.submission = s.id AND aso.assignment = s.assignment
    LEFT JOIN mdl_files f on f.itemid = asf.submission AND f.component="assignsubmission_file" AND f.filearea="submission_files" and f.filesize > 0

    WHERE s.assignment = '.$assignId.' AND s.status="submitted"';

    $assignUser = $DB->get_records_sql($query);

    ?>


    <table class="table table-dark table-striped">
        <tr>
            <th><strong>Sl No.</strong></th>
            <th><strong>Student Name</strong></th>
            <th><strong>Assignment</strong></th>
            <th><strong>File Name</strong></th>
            <th style="width: 40%;"><strong>Overall likelihood of AI: </strong></th>
        </tr>

        <?php

        $i = 1;
        foreach ($assignUser as $data) {

            $sub_id = $data->id;
            $onlinetext = $data->onlinetext;
            $filename = $data->filename;
            $mimetype = $data->mimetype;
            $user_id = $data->userid;
            $ass_id = $assignId;


            $sqli_query_curl_text_response = 'SELECT * FROM `mdl_curl_response` WHERE sid='.$sub_id.' AND user_id='.$user_id.'';
            $curl_text_response = $DB->get_records_sql($sqli_query_curl_text_response);

            
            $file_res = 0;
            $text_res = 0;
            

            foreach ($curl_text_response as $data_text) 
            { 

                $curl_response_type = $data_text->type;
                $curl_user_id = $data_text->user_id;
                $curl_status_code = $data_text->status_code;

                if($curl_response_type=='File'){ $file_res++; }
                if($curl_response_type=='Text'){ $text_res++; }
                

            }

if($i==1){
            ?>

    <tr>
        <td colspan="4" style="text-align: center;"><small style="color: #00ff7e;">By clicking here it will analyse for all the student's submission at once.</small></td>

        <td>
        <?php  if(!empty($filename) && ($file_res==0)){ ?>
            <button id="btnassign_file<?php echo $i; ?>" class="btn btn-sm btn-warning" data-submissionid="<?php echo $data->id; ?>" onclick="myfunc<?php echo $i; ?>('<?php echo $sub_id; ?>','<?php echo $ass_id; ?>','1')">Bulk File Submission Analyse</button>
        <?php }if(!empty($onlinetext) && ($text_res==0)){ ?>
            <button id="btnassign_text<?php echo $i; ?>" class="btn btn-sm btn-warning" data-submissionid="<?php echo $data->id; ?>" onclick="myfunc<?php echo $i; ?>('<?php echo $sub_id; ?>','<?php echo $ass_id; ?>','2')">Bulk Text Submission Analyse</button>
        <?php } ?>
        </td>

    </tr>

<?php } ?>

    <tr>

        <td><?php echo $i; ?></td>
        <td><?php echo $data->firstname.''.$data->lastname; ?></td>
        <td><?php echo $data->name; ?></td>
        <td><?php echo $data->filename; ?></td>

        <td>


        <?php  if(!empty($filename) && ($file_res==0)){ ?>
            <button id="btnassign_file<?php echo $i; ?>" class="btn btn-sm btn-primary" data-submissionid="<?php echo $data->id; ?>" onclick="myfunc<?php echo $i; ?>('<?php echo $sub_id; ?>','<?php echo $ass_id; ?>','1')">File Submission Analyse</button>
        <?php }if(!empty($onlinetext) && ($text_res==0)){ ?>
            <button id="btnassign_text<?php echo $i; ?>" class="btn btn-sm btn-info" data-submissionid="<?php echo $data->id; ?>" onclick="myfunc<?php echo $i; ?>('<?php echo $sub_id; ?>','<?php echo $ass_id; ?>','2')">Text Submission Analyse</button>
        <?php }if($curl_user_id==$user_id){ ?>

            <button type="button"  class="collapsible btn btn-sm btn-success" data-bs-target="#demo" >View Analysis</button>

        <?php } ?>

        <div class="content">
            <div id="ovflow">
            <table class="table" >

                <thead>
                    <tr>
                        <th scope="col"><b>#</b></th>
                        <td scope="row"><b>Response</b></td>
                    </tr>

                </thead>
                <tbody style="margin-top: 10px;">

                    <?php 

                    foreach ($curl_text_response as $data_text) { 

                        $curl_response = $data_text->response;
                        $curl_response = json_decode($curl_response, true);
                        $curl_response_type = $data_text->type;
                        

                    ?>

                    <tr>
                        <td colspan="3" align="center" id="tdd"><?php if($curl_response_type=='File'){ echo 'File Submission Analysis'; }if($curl_response_type=='Text'){ echo 'Text Submission Analysis'; } ?></td>
                    </tr>
                    
                    <?php echo dispaly_array_response($curl_response); ?>




                    <?php } ?>



                </tbody>
            </table>
            </div>

        </div>


        </td>

</tr>

        <!-- Make the script dynamic so that button operation can be performed separatly -->

        <script type="text/javascript">
            function myfunc<?php echo $i; ?>(para1, para2, para3){
                var redi="<?php echo $CFG->wwwroot.'/local/assignment_review/index.php?assignid='?>"+ para2;

                if(para3 == '1'){
                var btnassign_file = document.getElementById('btnassign_file<?php echo $i; ?>');
                btnassign_file.disabled = true;
                btnassign_file.innerText = 'Fetching data...';
                }


                if(para3 == '2'){
                var btnassign_text = document.getElementById('btnassign_text<?php echo $i; ?>');
                btnassign_text.disabled = true;
                btnassign_text.innerText = 'Fetching data...';
                }

                $.ajax({
                   url: 'ajaxsubmit.php?subid='+para1+'&assignid='+para2+'&submitcode='+para3,
                   type: 'post',
                   success: function(response){

                    //alert(response);
                    window.location.href=redi;

                   }
                });
            }
        </script>  

        <?php 

        $i++;

        }


   ?>
   </table>

<?php
}
}  
echo $OUTPUT->footer();




?>


<script type="text/javascript">
        $(document).ready(function() {
            $("body").on("change",'#assign', function() {
                var id =$(this).val();
                var url="<?php echo $CFG->wwwroot.'/local/assignment_review/index.php?assignid='?>"+ id;
                //console.log('url ',url);
                window.location.href=url;
            });
    
        });
</script>  





<script>
        var coll = document.getElementsByClassName("collapsible");
        var i;

        for (i = 0; i < coll.length; i++) {
          coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            if (content.style.display === "block") {
              content.style.display = "none";
            } else {
              content.style.display = "block";
            }
          });
        }
</script>


