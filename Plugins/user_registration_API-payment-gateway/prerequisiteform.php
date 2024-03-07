<?php
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');
global $DB, $USER, $PAGE;
$PAGE->requires->jquery();
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
echo '<style>.form-control-static { width: 100% !important; }</style>';
class prerequisite_form extends moodleform {
    // Define the form
    function definition() {
		global $CFG, $DB, $USER;	
        $id = $this->_customdata['id'];
    	$reg_id = base64_decode($id);
        $url = $this->_customdata['url'];
		$mform =& $this->_form; 
    	$attr = $mform->getAttributes();
 		$attr['enctype'] = "multipart/form-data";
 		$mform->setAttributes($attr);
    	if($DB->record_exists('lcl_corporate_enrollment', array('registration_id' => $reg_id))){
        	$data = $DB->get_record('lcl_corporate_enrollment', array('registration_id' => $reg_id));
        	$course_id = $data->course_id;
        	$enrollment_id = $data->id;
            $prerequisite = $DB->get_record('customfield_field', array('shortname' => 'prerequisite'));
            $prerequisitetype = $DB->get_record('customfield_field', array('shortname' => 'prerequisitetype'));
        	$customdata_prerequisitetype = $DB->get_record('customfield_data', array('fieldid' => $prerequisitetype->id, 'instanceid' => $course_id)); 
        	if($customdata_prerequisitetype) {            
	            $prerequisitetype_set = $customdata_prerequisitetype->value-1;
            	$prerequisitetype_data = explode("\n", json_decode($prerequisitetype->configdata)->options);
            	$typenameprerequisite = $prerequisitetype_data[$prerequisitetype_set];
            }

            $certificates = $DB->get_record('customfield_field', array('shortname' => 'certificates')); 
            $customdata_prerequisite = $DB->get_record('customfield_data', array('fieldid' => $prerequisite->id, 'instanceid' => $course_id)); 
            if($customdata_prerequisite->value == 2) {
				$customdata_certificates = $DB->get_record('customfield_data', array('fieldid' => $certificates->id, 'instanceid' => $course_id)); 	                                            
				$certificate_data = $customdata_certificates->value;
            	$certificate_data = explode("\n", $certificate_data);
				$arrnew = array();
            	$typename = array();
            	$key = 0; 
            	foreach($certificate_data as $data){
               		 $data = trim(strip_tags($data));
            		 $prerequisite_data = $DB->record_exists('lcl_prerequisite_data', array('registration_id' => $reg_id, 'type'=> $key));
                	 $typename[$key] = $data; 
    				 if(!$prerequisite_data){
                     	if($data !=''){
	                	 	$arrnew[$key] = $data;
                        }
                     }
                	 $key++;
                }
            }                                                                               
        }

    	if($DB->record_exists('lcl_individual_enrollment', array('registration_id' => $reg_id))){
        	$data = $DB->get_record('lcl_individual_enrollment', array('registration_id' => $reg_id));
        	$course_id = $data->course_id;
        	$enrollment_id = $data->id;
            $prerequisite = $DB->get_record('customfield_field', array('shortname' => 'prerequisite'));                                     
        	$prerequisitetype = $DB->get_record('customfield_field', array('shortname' => 'prerequisitetype'));
        	$customdata_prerequisitetype = $DB->get_record('customfield_data', array('fieldid' => $prerequisitetype->id, 'instanceid' => $course_id)); 
        	if($customdata_prerequisitetype) {            
	            $prerequisitetype_set = $customdata_prerequisitetype->value-1;
            	$prerequisitetype_data = explode("\n", json_decode($prerequisitetype->configdata)->options);
            	$typenameprerequisite = $prerequisitetype_data[$prerequisitetype_set];
            }
            $certificates = $DB->get_record('customfield_field', array('shortname' => 'certificates')); 
            $customdata_prerequisite = $DB->get_record('customfield_data', array('fieldid' => $prerequisite->id, 'instanceid' => $course_id));
			if($customdata_prerequisite->value == 2) {
				$customdata_certificates = $DB->get_record('customfield_data', array('fieldid' => $certificates->id, 'instanceid' => $course_id)); 	                                            
				$certificate_data = $customdata_certificates->value;
            	$certificate_data = explode("\n", $certificate_data);
				$arrnew = array();
            	$typename = array();
            	$key = 0; 
            	foreach($certificate_data as $data){
               		 $data = trim(strip_tags($data));
            		 $prerequisite_data = $DB->record_exists('lcl_prerequisite_data', array('registration_id' => $reg_id, 'type'=> $key));
                	 $typename[$key] = $data; 
    				 if(!$prerequisite_data){
                	 	if($data !=''){
	                	 	$arrnew[$key] = $data;
                        }
                     }
                	 $key++;
                }
            }
        }
        $options = $arrnew;
    	$prerequisite_datas = $DB->get_records('lcl_prerequisite_data', array('registration_id' => $reg_id));
    	if(trim($typenameprerequisite) === 'File Upload') {
            	$mform->addElement('select', 'certificate', 'Select Documents/ Certificate type to upload', $options, array('id' => 'certificateid'));
            	$mform->addElement('html', '<script>
                                       		 $("#certificateid").change(function() {
                                           		var vall = $(this).val();
                                           		 $("#type").val(vall);
                                         		 });
                                   			 </script>');
            	$mform->addElement('static', 'note', '', "<div>Note: Upload the required file one by one</div>");
            	$mform->addElement('html', '<link rel="stylesheet" href="'.$CFG->wwwroot.'/local/user_registration/style3.css"></link>');
            	$mform->addElement('static', 'drop_drag', '', '<div class="box">
                                                           <i class="fa fa-arrow-circle-o-down fa-3x m-2"></i>
                                                           <label>
                                                            <span>You can drag and drop files here to add them.</span>
                                                                <input class="box__file" type="file" name="content" required/>
                                                            </label>
                                                           <div class="file-list"></div>
                                                        </div>');
            	$mform->addElement('html', '<script src="'.$CFG->wwwroot.'/local/user_registration/js/custom-drop-drag.js"></script>');
        		$mform->addElement('hidden', 'type', array_keys($arrnew)[0], array("id"=> "type"));    

        } else if (trim($typenameprerequisite) == 'Ask Question'){
			$count = 1;
        	foreach($options as $key => $value) {
				$mform->addElement('static', 'ask_question', "Question $count", $value);
            	$mform->addElement('textarea', "content$key", 'Answer');
            	$count++;
			}
        } else if (trim($typenameprerequisite) == 'Personal Info'){
        	$count = 1;
			foreach($options as $key => $value) {
				$mform->addElement('static', 'personal_info', "Inquiry $count", $value);
            	$mform->addElement('textarea', "content$key", 'Answer');
				$count++;
            }
        }else{
        	echo "something is wrong";
        }
        if(count($prerequisite_datas)>0) { 
        $table = '<table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>Field</th>
                            <th>Content</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>';
                $i = 1;
                foreach($prerequisite_datas as $data) { 
                    $table .= '<tr>
                                    <td>'.$i++.'</td>
                                    <td>'.$typename[$data->type].'</td>';
                    if (trim($typenameprerequisite) === 'File Upload'){
                        $table .= '<td><a href="'.$CFG->wwwroot.'/local/user_registration/temp/'.$data->content.'" target="_blank">'.$data->content.'</a></td>
                                   <td><a href="'.$CFG->wwwroot.'/local/user_registration/remove-file.php?fileid='.base64_encode($data->id).'&id='.base64_encode($reg_id).'">
                                    <i class="fa fa-times text-danger" aria-hidden="true"></i></a></td>';
                    }elseif(trim($typenameprerequisite) == 'Ask Question' || trim($typenameprerequisite) == 'Personal Info'){
                        $table .= '<td>'.$data->content.'</td>
                                   <td><a href="'.$CFG->wwwroot.'/local/user_registration/remove-file.php?fileid='.base64_encode($data->id).'&id='.base64_encode($reg_id).'">
                                    <i class="fa fa-times text-danger" aria-hidden="true"></i></a></td>';
                    }else{
                        $table .= '<td colspan=2></td>';
                    }

                 $table .=  '</tr>';
                            }
            $table .= '</tbody>
                 </table>';
        $mform->addElement('static', 'table', 'Uploaded data:', $table);
        } 
  

        $mform->addElement('hidden', 'id', $id);    
        $mform->addElement('hidden', 'url', $url);    
        $mform->addElement('hidden', 'course_id', $course_id);    
        $mform->addElement('hidden', 'enrollment_id', $enrollment_id);    
        $mform->addElement('hidden', 'prerequisitetype', trim($typenameprerequisite));    
        
    	$count_total = count($typename);
        $count_uploaded = count($prerequisite_datas);
		
    
    	if ($count_total == $count_uploaded) {
        		if($_SESSION['course_level_final'] == 1) {
	        		$mform->addElement('button', 'intro', '<a href="'.$CFG->wwwroot.'/local/user_registration/home.php?id='.base64_encode($reg_id).'" style="text-decoration: none !important;">Next & Continue</a>');
            	} else {
	        		$mform->addElement('button', 'intro', '<a href="'.$CFG->wwwroot.'/local/user_registration/application_form.php?id='.base64_encode($reg_id).'" style="text-decoration: none !important;">Next & Continue</a>');
            	}
        }else {
        	$this->add_action_buttons(false, "Submit");
        }
    }
}