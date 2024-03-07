<?php
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');
global $DB,$PAGE,$USER;
$PAGE->requires->jquery();
$context = \context_system::instance();
$has_capability = has_capability('local/user_registration:assessor_access', $context, $USER->id);
if (!$has_capability) {
    $urltogo_dashboard = $CFG->wwwroot.'/local/user_registration/';
    redirect($urltogo_dashboard, 'You do not have permission to view this page', null, \core\output\notification::NOTIFY_WARNING);
}





class setting_logo_address_form extends moodleform {
    // Define the form
    function definition() {
		global $CFG, $DB;	

        $type = $this->_customdata['type'];
        $url = $this->_customdata['url'];
		$mform =& $this->_form; 
    	$attr = $mform->getAttributes();
 		$attr['enctype'] = "multipart/form-data";
 		$mform->setAttributes($attr);
    
    
    
		$mform->addElement('html', '<h3><i class="fa-solid fa-bars pr-2" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"></i> Assessor Panel</h3>
			<div class="collapse" id="collapseExample">
			<ul>
 				<li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/index.php">Home</a></li>
 				<li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/setting-template.php?type='.base64_encode(1).'">Email template individual</a></li>
 				<li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/setting-template.php?type='.base64_encode(2).'">Email template corporate</a></li>
 				<li><a href="'.$CFG->wwwroot.'/local/user_registration/admin/setting-logo-address.php?type='.base64_encode(3).'">Email Logo , Address</a></li>
			</ul>
			</div>
			<br><h4>Set Email Template (Logo, Address)</h4><hr><br>');
    	
		$mform->addElement('textarea', 'address', 'Address', 'wrap="virtual" rows="5"');
    	$mform->addElement('static', 'note', '', "<div>Note: Upload logo to sent during email header</div>");
		$mform->addElement('html', '<link rel="stylesheet" href="'.$CFG->wwwroot.'/local/user_registration/style3.css"></link>');
		$mform->addElement('static', 'drop_drag', 'Logo', '<div class="box">
        												   <i class="fa fa-arrow-circle-o-down fa-3x m-2"></i>
           							 				 	   <label>
                											<span>You can drag and drop files here to add them.</span>
                												<input class="box__file" type="file" name="content" required/>
            						 						</label>
            											   <div class="file-list"></div>
        												</div>');
		$mform->addElement('html', '<script src="'.$CFG->wwwroot.'/local/user_registration/js/custom-drop-drag.js"></script>');
        $mform->addElement('hidden', 'type', $type);    
        $mform->addElement('hidden', 'url', $url);    
        $lcl_logo_address = $DB->get_record('lcl_logo_address', array('type' => base64_decode($type)));
    	if(!empty($lcl_logo_address)) { 
    		$table = '<table class="table table-striped">
        					<thead>
                            <tr>
                                <th>Sl.</th>
                                <th>File</th>
                                <th>Address</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>';
    						$i = 1;
    
            	 $table .= '<tr>
                            	<td>'.$i++.'</td>
                                <td><a href="'.$CFG->wwwroot.'/local/user_registration/temp/'.$lcl_logo_address->logo.'" target="_blank">'.$lcl_logo_address->logo.'</a></td>
                                <td>'.$lcl_logo_address->address.'</td>
                                <td><a href="'.$CFG->wwwroot.'/local/user_registration/admin/remove-file.php?id='.base64_encode($lcl_logo_address->id).'">
                                <i class="fa fa-times text-danger" aria-hidden="true"></i></a></td>
                           </tr>';
                            
        	$table .= '</tbody></table>';
    		$mform->addElement('static', 'table', '', $table);
        
		}    
    
        	$this->add_action_buttons(false, "Upload");
    }
}