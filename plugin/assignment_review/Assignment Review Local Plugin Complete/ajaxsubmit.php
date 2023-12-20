<?php 
require_once('../../config.php');
// require_once ('../../lib/filelib.php');

global $DB, $CFG, $USER, $PAGE;

$CFG->libdir."/filelib.php";

//require_once(__DIR__ . '/lib/filelib.php');

$PAGE->requires->jquery();
require_login();


$subid = $_GET['subid'];
$assignId = $_GET['assignid'];
$submitcode = $_GET['submitcode'];




$status = '';


if((!empty($assignId)) && (!empty($subid)))
{

    $query = 'SELECT s.id, ass.name, u.id, u.firstname, u.lastname, u.email, f.filename, f.mimetype, f.contextid, f.component, f.filearea, f.itemid, f.filepath, aso.onlinetext FROM mdl_assign_submission s
    INNER JOIN mdl_user as u on u.id = s.userid
    LEFT JOIN mdl_assign ass on ass.id = s.assignment
    LEFT JOIN mdl_assignsubmission_file asf on asf.submission = s.id AND asf.assignment = s.assignment
    LEFT JOIN mdl_assignsubmission_onlinetext aso on aso.submission = s.id AND aso.assignment = s.assignment
    LEFT JOIN mdl_files f on f.itemid = asf.submission and f.component="assignsubmission_file" AND f.filearea="submission_files" and f.filesize > 0
    WHERE s.assignment = '.$assignId.' AND s.status="submitted" AND s.id = '.$subid.'';



    $assignUser = $DB->get_records_sql($query);

    $fs = get_file_storage();

    foreach ($assignUser as $data) {  

    $onlinetext = $data->onlinetext;
    $filename = $data->filename;
    $mimetype = $data->mimetype;
    $user_id = $data->id;
    $contextid = $data->contextid;
    $component = $data->component;
    $filearea = $data->filearea;
    $itemid = $data->itemid;
    $filepath = $data->filepath;
    $files = $fs->get_area_files($contextid, $component, $filearea, $itemid, false); 
    foreach ($files as $file) {
    $file->copy_content_to($CFG->dataroot . '/temp/'. $file->get_filename());
    }

   }

    $onlinetext = filter_var($onlinetext, FILTER_SANITIZE_STRING);

    //$file_url = moodle_url::make_pluginfile_url($file->get_contextid($contextid), $file->get_component($component), $file->get_filearea($filearea), $file->get_itemid($itemid), $file->get_filepath($filepath), $file->get_filename($filename), false);
 

}
    




if($submitcode=='2'){

         if((!empty($onlinetext)) && (!empty($filename))){ $status_code = 2; }else{ $status_code = 1; }

         $type = 'Text';
         $ch = curl_init();
         $data = json_encode(array(
         "document"  => $onlinetext
         ));
         curl_setopt($ch, CURLOPT_URL, 'https://api.gptzero.me/v2/predict/text');
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

         $headers = array();
         $headers[] = 'Accept: application/json';
         $headers[] = 'X-Api-Key: 236685a8600a267e2c97debceb13688e';
         $headers[] = 'Content-Type: application/json';
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         $result = curl_exec($ch);


         if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
         }else{

            $resArr = array();
            $resArr = json_decode($result,true);
            //print_r($resArr);

            $status_key = '';
            foreach ($resArr as $key => $value) {
            if($key=='documents'){ $status_key='Success';  echo $status = 'Success in saving the data'; }else{ $status_key='Failed';  echo $status = 'Failed in saving the data'; }
            }
            $record = new stdClass();
            $record->sid = $subid;
            $record->user_id = $user_id;
            $record->response = $result;
            $record->type = $type;
            $record->text_string  = $onlinetext;
            $record->status = $status_key;
            $record->status_code = $status_code;

            $DB->insert_record('curl_response', $record, false);

         }
         curl_close($ch);


 }

else
   if($submitcode=='1'){

         if((!empty($onlinetext)) && (!empty($filename))){ $status_code = 2; }else{ $status_code = 1; }
         $type = 'File';
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, 'https://api.gptzero.me/v2/predict/files');
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
         curl_setopt($ch, CURLOPT_HTTPHEADER, [
             'accept: application/json',
             'X-Api-Key: 236685a8600a267e2c97debceb13688e',
             'Content-Type: multipart/form-data',
         ]);

         
         $fianl_path_of_file = $CFG->dataroot.'/temp/'.$filename;
         $cfile = new CURLFile($fianl_path_of_file,$mimetype,$filename);
         $imgdata = array('files' => $cfile);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $imgdata);

         $result = curl_exec($ch);
         
         if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
         }else{

            $resArr = array();
            $resArr = json_decode($result,true);
            //print_r($resArr);

            $status_key = '';
            foreach ($resArr as $key => $value) {
            if($key=='documents'){ $status_key='Success';  echo $status = 'Success in saving the data'; }else{ $status_key='Failed';  echo $status = 'Failed in saving the data'; }
            }
            $record = new stdClass();
            $record->sid = $subid;
            $record->user_id = $user_id;
            $record->response = $result;
            $record->type = $type;
            $record->text_string  = $filename;
            $record->status = $status_key;
            $record->status_code = $status_code;

            $DB->insert_record('curl_response', $record, false);

         }
         curl_close($ch);

         
 }

else{ 
   echo "Something is wrong !"; 
}




   

?>





