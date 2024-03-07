<?php
require_once('../../config.php');
global $DB, $CFG, $USER, $PAGE;
require_once("$CFG->libdir/formslib.php");
class customcourses_form extends moodleform {
    public function definition() {
        $mform = $this->_form; // Don't forget the underscore! 
        global $DB,$CFG;
        $course        = $this->_customdata['course']; // this contains the data of this form
        $fullcourse        = $this->_customdata['fullcourse']; // this contains the data of this form
        $category      = $this->_customdata['category'];
        $wpcategory = $this->_customdata['wpcategory'];
        $returnto = $this->_customdata['returnto'];
        $returnurl = $this->_customdata['returnurl'];
        $course_template = $this->_customdata['course_template'];
        $systemcontext   = context_system::instance();
        $categorycontext = context_coursecat::instance($category->id);
        if (!empty($course->id)) {
            $coursecontext = context_course::instance($course->id);
            $context = $coursecontext;
        } else {
            $coursecontext = null;
            $context = $categorycontext;
        }       
        //--------------------------------------------------
        $wpurl = get_config('local_manage_course','wpurl');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $wpurl.'/product-category-listing/',///wp-content/themes/buddyboss-theme-child/category.php
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: mailchimp_landing_site=https%3A%2F%2Fstaging.lemons-aid.com%2Fproduct-category-listing%2F'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $coursecat = json_decode($response, true);
        // print_r($coursecat);
        //--------------------END------------------------------
        //---------------API for get Wordpress Product Tag List-----------------------------------
        $curl2 = curl_init();
        curl_setopt_array($curl2, array(
            CURLOPT_URL => $wpurl.'/product-tags-list/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: mailchimp_landing_site=https%3A%2F%2Fstaging.lemons-aid.com%2Fproduct-category-listing%2F'
            ),
        ));
        $responsetag = curl_exec($curl2);
        curl_close($curl2);
        $producttag = json_decode($responsetag, true);
        //--------------END----------//
        //-------API for get Wordpress Attributes List------//
        $curl3 = curl_init();
        curl_setopt_array($curl3, array(
            CURLOPT_URL => $wpurl.'/product-attributes/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: mailchimp_landing_site=https%3A%2F%2Fstaging.lemons-aid.com%2Fproduct-category-listing%2F'
            ),
        ));
        $responsetag = curl_exec($curl3);
        curl_close($curl3);
        $producattributes = json_decode($responsetag, true);
        $attributeslist = array("SELECT");
        foreach ($producattributes as $attributes) {
            $attributesvlu = $attributes['attribute_name'];
            $attributeslist[$attributesvlu] = $attributes['attribute_label'];
        }
        //--------------END-------------------------
        // Form definition with new course defaults.  
        $mform->addElement('header','general', get_string('general', 'form'));

        $mform->addElement('hidden', 'returnto', null);
        $mform->setType('returnto', PARAM_ALPHANUM);
        $mform->setConstant('returnto', $returnto);

        $mform->addElement('hidden', 'returnurl', null);
        $mform->setType('returnurl', PARAM_LOCALURL);
        $mform->setConstant('returnurl', $returnurl);

        $mform->addElement('text', 'fullname',get_string('fullnamecourse')); 
        $mform->addHelpButton('fullname', 'fullnamecourse');       
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');                 
        $mform->setType('fullname', PARAM_NOTAGS);
        if (!empty($course->id) and !has_capability('moodle/course:changefullname', $coursecontext)) {
            $mform->hardFreeze('fullname');
            $mform->setConstant('fullname', $course->fullname);
        }
        
        $mform->addElement('text', 'shortname',get_string('shortnamecourse')); 
        $mform->addHelpButton('shortname', 'shortnamecourse');
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');    
        $mform->setType('shortname', PARAM_NOTAGS);
        if (!empty($course->id) and !has_capability('moodle/course:changeshortname', $coursecontext)) {
            $mform->hardFreeze('shortname');
            $mform->setConstant('shortname', $course->shortname);
        }

        $select = $mform->addElement('autocomplete', 'pcategory', 'Category Type', $coursecat);
        if(!empty($wpcategory)){
            $mform->setDefault('pcategory', $wpcategory);
        }else{
            $select->setSelected('1');
        }

        $options = array(                                                                                                           
            'multiple' => true,                                                  
            'noselectionstring' => 'Select Multiple category',                                                                
        );
        $select = $mform->addElement('autocomplete', 'customfield_age_group', 'Age Group', $producttag, $options);      
            $couse_tamplate = $this->callcourse('NEWBERY TEMPLATE');
            $couse_tamplate1 = $this->callcourse('Trail Format Course Template');
            $couse_tamplate2 = $this->callcourse('Weekly Format Course Template');
            $couse_tamplate3 = $this->callcourse('Topics Format Course Template');
            $couse_tamplate4 = $this->callcourse('Tiles Format Course Template');
            
            $formcourseformats = array();
            $formcourseformats[0] = 'Select Templete';
            $formcourseformats[$couse_tamplate->id] = $couse_tamplate->name;
            $formcourseformats[$couse_tamplate1->id] = $couse_tamplate1->name;
            $formcourseformats[$couse_tamplate2->id] = $couse_tamplate2->name;
            $formcourseformats[$couse_tamplate3->id] = $couse_tamplate3->name;
            $formcourseformats[$couse_tamplate4->id] = $couse_tamplate4->name;

            $mform->addElement('select', 'format1', 'Course Template', $formcourseformats );
            if(!empty($course_template)){
                $mform->setDefault('format1', $course_template);
            }
        if (!empty($course->id) and !has_capability('moodle/course:changeidnumber', $coursecontext)) {
            $mform->hardFreeze('idnumber');
            $mform->setConstants('idnumber', $course->idnumber);
        }

        $mform->addElement('select', 'attributes', 'Attributes', $attributeslist);

        $attrhtml = '<div id="additionalattributes">';
        if(!empty($course->id)){
            $customdata = $this->get_course_metadata($course->id);
           $dataa = $DB->get_record('wpproduct',array('courseid'=>$course->id));
           if($dataa){
               $d= json_decode($dataa->post_data);
               foreach ($d as $dk => $dv) {
                    $attrhtml.= '<div><lebel>'.$dk.'</lebel><select name="attribute['.$dk.']" id="element_'.$dk.'">';
                    foreach ($producattributes as $pkey => $pvalue) {
                        if($pvalue['attribute_name']==$dk){
                           // echo "<pre>";
                            foreach ($pvalue['child'] as $pvalues) {
                            // print_r($pvalues);
                                $attrhtml .='<option value="'.$pvalues['slug'].'">'.$pvalues['name'].'</option>';
                            }
                        }
                    }

                    $attrhtml.= '</select><span type="button" style="cursor: pointer !important;" data-id="attribute['.$dk.']" class="removeattribute"> X </span></div>';
               }

           }
        }
        $attrhtml .= '</div><script type="text/javascript">var allatributes = '.$responsetag.'; </script>';
        $mform->addElement('html', $attrhtml);

        $mform->addElement('text', 'customfield_price','Price'); 

        if ($overviewfilesoptions = course_overviewfiles_options($course)) {
            $mform->addElement('filemanager', 'overviewfiles_filemanager', get_string('courseoverviewfiles'), null, $overviewfilesoptions);
            $mform->addHelpButton('overviewfiles_filemanager', 'courseoverviewfiles');
            $summaryfields .= ',overviewfiles_filemanager';
        }

        $mform->addElement('hidden', 'startdate', get_string('startdate'));
        $mform->addHelpButton('startdate', 'startdate');
        $date = (new DateTime())->setTimestamp(usergetmidnight(time()));
        $date->modify('+1 day');
        $mform->setDefault('startdate', $date->getTimestamp());
        
        //description
        // $mform->addElement('header', 'descriptionhdr', get_string('description'));
        // $mform->setExpanded('descriptionhdr');

        $mform->addElement('editor','customfield_shortsummary_editor', 'Short Summary', null);
        // $mform->setConstant('customfield_shortsummary_editor', $course->shortname);

        $mform->addElement('editor','summary_editor', 'Course Description', null);
        $mform->addHelpButton('summary_editor', 'coursesummary');
        $mform->setType('summary_editor', PARAM_RAW);
        $summaryfields = 'summary_editor';

        
        
        if (!empty($course->id) and !has_capability('moodle/course:changesummary', $coursecontext)) {
            // Remove the description header it does not contain anything any more.
            $mform->removeElement('descriptionhdr');
            $mform->hardFreeze($summaryfields);
        }

        
        $mform->addElement('textarea','customfield_syllabus', 'Syllabus', 'wrap="virtual" rows="10" cols="10"');
        $mform->addRule('customfield_syllabus', 'Must be less than 1300 characters', 'maxlength', '1300', 'client');

        $mform->addElement('textarea', 'customfield_metadescript', 'Meta Description', 'wrap="virtual" rows="10" cols="10"');
        $mform->addRule('customfield_metadescript', 'Must be less than 137 characters', 'maxlength', '137', 'client');

        $mform->addElement('header', 'courseformathdr', 'Marketing');

        $mform->addElement('hidden', 'customfield_virtual', 1);
        $mform->addElement('hidden', 'customfield_downloadable', 0);

        $select = $mform->addElement('autocomplete', 'customfield_upsells', 'Upsells', $fullcourse);
        $mform->setDefault('customfield_upsells', 0);
        
        $select = $mform->addElement('autocomplete', 'customfield_cross_sells', 'Cross-sells',  $fullcourse);
        $mform->setDefault('customfield_cross_sells', 0);

        // Appearance.
        $mform->addElement('header', 'appearancehdr', get_string('appearance'));


        $mform->addElement('hidden','lang', 'Language');
        $mform->setDefault('lang', 'en');

        $options6 = range(0, 10);
        $mform->addElement('select', 'newsitems', get_string('newsitemsnumber'), $options6);
        $courseconfig = get_config('moodlecourse'); // YESNO from moodle form
        $mform->setDefault('newsitems', $courseconfig->newsitems);
        $mform->addHelpButton('newsitems', 'newsitemsnumber');

        $mform->addElement('selectyesno', 'showgrades', get_string('showgrades'));
        $mform->addHelpButton('showgrades', 'showgrades');
        $mform->setDefault('showgrades', $courseconfig->showgrades);

        $mform->addElement('selectyesno', 'showreports', get_string('showreports'));
        $mform->addHelpButton('showreports', 'showreports');
        $mform->setDefault('showreports', $courseconfig->showreports);

        $mform->addElement('selectyesno', 'showactivitydates', get_string('showactivitydates'));
        $mform->addHelpButton('showactivitydates', 'showactivitydates');
        $mform->setDefault('showactivitydates', $courseconfig->showactivitydates);
        $mform->addElement('hidden','enablecompletion', 1);
        $mform->addElement('hidden','showcompletionconditions', 1);
        $mform->addElement('hidden','groupmode', 1);
        $mform->addElement('hidden','id', 0);
        if($course){
            $course->customfield_virtual = $customdata['virtual'];
            $course->customfield_downloadable = $customdata['downloadable'];
            $course->customfield_age_group = $customdata['age_group'];
            $course->customfield_upsells = $customdata['upsells'];
            $course->customfield_metadescript = $customdata['metadescript'];
            $course->customfield_syllabus = $customdata['syllabus'];
            $course->customfield_shortsummary_editor['text'] = $customdata['shortsummary'];
            $course->customfield_price = $customdata['price'];
            $course->customfield_cross_sells = $customdata['cross_sells'];
            $course->attributes = json_decode($dataa->post_data);
        }
        
        $this->add_action_buttons();
        $this->set_data($course);

    }

    function callcourse($shortname){
        global $DB;
       $coursql = 'SELECT * FROM {course} WHERE `shortname` LIKE "%'.$shortname.'%"';
        $course = $DB->get_record_sql($coursql);
        $obj = new stdClass();
        $obj->id = $course->id; 
        $obj->name = $course->fullname; 
        return $obj;
    }


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

    //Custom validation should be added here
    function validation($data, $files) {
        global $DB;
        $errors = array();
        if(empty($data['id'])){
            $dbquery = $DB->get_record_sql('SELECT * FROM {course} WHERE `shortname`=?',array($data['shortname']));
            if(!empty($dbquery)){
                    $errors['shortname'] = "Shotname is already used.";
            }
        }
        // if(strlen($data['customfield_metadescript']) > 1300){   
        //     $errors['customfield_metadescript'] = "Your massage text is too huge.";
        // }
        // if(strlen($data['customfield_syllabus']) > 1300){
        //     $errors['customfield_syllabus'] = "Your massage text is too huge.";
        // }
        return $errors;
    } 
}
?>

