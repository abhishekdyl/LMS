<?php
require_once('../../config.php');
global $DB, $CFG ,$PAGE ,$USER;
require_login();
$PAGE->requires->jquery(); 
$id = optional_param('id', 0, PARAM_INT); 

if($id){
   
//  die;   

    function get_course_metadata($id) {
        $handler = \core_customfield\handler::get_handler('core_course', 'course');
        $datas = $handler->get_instance_data($id, true);
        $metadata = [];
        foreach ($datas as $data) {
            //echo 'data: '.$data->get_value();
            if (empty($data->get_value())) {
                continue;
            }
            $metadata[$data->get_field()->get('shortname')] = $data->get_value();
        }
        return $metadata;
    }

    function get_wp_url(){
		return get_config('local_manage_course','wpurl');
	}

    function getcourse_image($courseid) {
	    global $DB, $CFG, $USER;
	    //$imageurl= "/wp-content/uploads/2021/05/no-image-220x220.jpg";
	    require_once($CFG->dirroot. '/course/classes/list_element.php');
	    $course = $DB->get_record('course', array('id' => $courseid));
	    $course = new \core_course_list_element($course);
	    foreach ($course->get_course_overviewfiles() as $file) {
	        $isimage = $file->is_valid_image();
	        $token = get_user_key('core_files', $USER->id);
	        $imageurl = file_encode_url("$CFG->wwwroot/tokenpluginfile.php", '/'.$token.'/'. $file->get_contextid(). '/'. $file->get_component(). '/'. $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
	        return $imageurl;
	    }
	    return $imageurl;
	}

        $dataa = $DB->get_record('wpproduct',array('courseid'=>$id));
        if($dataa->approval == 0){
            $updatedobj = new stdClass(); 
            $updatedobj->id = $dataa->id; 
            $updatedobj->courseid = $id; 
            $updatedobj->approval = 1; 
            $aa = $DB->update_record('wpproduct', $updatedobj, $bulk=false);
        }
        
        $wpsql = 'SELECT c.*, wp.categoryid as wpcategory FROM {course} c INNER JOIN {wpproduct} wp ON c.id = wp.courseid where c.id = ?';
        $coursedata = $DB->get_record_sql($wpsql, array($id));

        if($coursedata){
            $customfield = get_course_metadata($id);
            $coursedata->image = getcourse_image($id);
            $coursedata->wpcategory 			= $customfield['wpcategory'];
            $coursedata->virtual 				= $customfield['virtual'];
            $coursedata->price 					= $customfield['price'];
            $coursedata->age_group 				= trim($customfield['age_group']);
            $coursedata->shortsummary_editor 	= $customfield['shortsummary'];
            $coursedata->metadescript 			= $customfield['metadescript'];
            $coursedata->upsells 				= $customfield['upsells'];
            $coursedata->cross_sells 			= $customfield['cross_sells']; 
            $coursedata->attribute            = json_decode($dataa->post_data); 
            // echo "<pre>";
            // //print_r($dataa);
            // print_r(json_encode($coursedata));
            // echo "<pre>";
            // die;
            $wpurl= get_wp_url();
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL =>$wpurl.'/wp-content/plugins/sync-course/product-course-add.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($coursedata),
                CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
        }
    //-------------------------Group and activity data ------------------------------
        $allsectiondata = $DB->get_records("custome_groups", array("courseid" => $id));
        if($allsectiondata){
            $datatopush = array();
            foreach ($allsectiondata as $skey => $course_sections_data) {
                $userdata = $DB->get_record('user', array('id' => $course_sections_data->teacher));
                $course_sections_data->teacheremail = $userdata->email;
                $course_sections_data->post_data = json_decode($course_sections_data->post_data);
                $course_sections_data->starttime = $course_sections_data->post_data->starttime;
                $course_sections_data->endtime = $course_sections_data->post_data->endtime;
                array_push($datatopush, $course_sections_data);
            }
        
            $wpurl = get_config('local_coursesync', 'wpurl'); // Render data from plugin setting.
            $postdata = array("allsessions" => $datatopush, "courseid" => $id);
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                CURLOPT_URL => $wpurl . '/wp-content/plugins/sync-course/product-course-section-add.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postdata),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
                )
            );
            $response = curl_exec($curl);
        }

}


if(is_siteadmin($USER->id)){
    $coursesql = 'SELECT wpp.courseid,wpp.approval,c.fullname,c.startdate,cat.name,u.firstname,u.lastname FROM {wpproduct} wpp INNER JOIN {course} c ON wpp.courseid = c.id INNER JOIN {course_categories} cat ON c.category = cat.id INNER JOIN {user} u ON wpp.userid = u.id;';
    $data = $DB->get_records_sql($coursesql,array());  


    echo $OUTPUT->header();
$html = '
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
<table class="table table-stripped" id="table_filter">
            <thead>
                <tr>
                    <th>Sno.</th>
                    <th>Course</th>
                    <th>Category</th>
                    <th>Teacher</th>
                    <th>Course start Date</th>
                    <th>Approval</th>
                </tr>
            </thead><tbody>'; 
        $i = 1; 
foreach ($data as $courses) {


    $html .='<tr>
                <td>'.$i.'</td>
                <td><a href="/course/view.php?id='.$courses->courseid.'">'.$courses->fullname.'</a></td>
                <td>'.$courses->name.'</td>
                <td>'.$courses->firstname.' '.$courses->lastname.'</td>
                <td>'.($courses->startdate?date("d F Y h:i A", $courses->startdate):"").'</td>
                <td>';
                if($courses->approval == 0 ){
                    $html .='<a href="/local/manage_course/custom_courselist.php?id='.$courses->courseid.'">To Approve  </a>';
                }else{
                    $html .='<b>Approved  </b>';
                }
        $html .='<a href="/local/manage_course/feedback.php?id='.$courses->courseid.'"> | Feedback</a>
                </th>
            </tr>';
    // echo '<pre>';
    // print_r($courses); <td><a href="/local/manage_course/custom_courselist.php?id='.$courses->courseid.'">'.(($courses->approval == 0 )?"To Approve":"Approved").'</a></th>
    // echo '</pre>';
$i++;
}
$html .='</tbody></table>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
$(function(){
    $("#table_filter").DataTable();
});
</script>';
echo $html;
echo $OUTPUT->footer();




}