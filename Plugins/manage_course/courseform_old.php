
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
        // echo "<pre>";
        // print_r($producttag);
        // echo "</pre>";
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
        // $mform->addElement('select', 'customfield_age_group', 'Age Group', $producttag);

        // $select = $mform->addElement('select', 'pcategory', Category Type, $coursecat);
        // $select->setMultiple(true);

        // $mform->setConstant('pcategory', $category->id);
        //-----------------------------------------------------
        // $mform->addElement('hidden','category', 17);
        // $mform->setConstant('category', $category->id);
        //-----------------------------------------------------

        // $mform->addElement('text','idnumber', get_string('idnumbercourse'),'maxlength="100"  size="10"');
        // $mform->addHelpButton('idnumber', 'idnumbercourse');
        // $mform->setType('idnumber', PARAM_RAW);
        if (!empty($course->id) and !has_capability('moodle/course:changeidnumber', $coursecontext)) {
            $mform->hardFreeze('idnumber');
            $mform->setConstants('idnumber', $course->idnumber);
        }

        $mform->addElement('text', 'customfield_price','Price'); 

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

        if ($overviewfilesoptions = course_overviewfiles_options($course)) {
            $mform->addElement('filemanager', 'overviewfiles_filemanager', get_string('courseoverviewfiles'), null, $overviewfilesoptions);
            $mform->addHelpButton('overviewfiles_filemanager', 'courseoverviewfiles');
            $summaryfields .= ',overviewfiles_filemanager';
        }
        if (!empty($course->id) and !has_capability('moodle/course:changesummary', $coursecontext)) {
            // Remove the description header it does not contain anything any more.
            $mform->removeElement('descriptionhdr');
            $mform->hardFreeze($summaryfields);
        }

        $mform->addElement('textarea', 'customfield_metadescript', 'Meta Description', 'wrap="virtual" rows="10" cols="10"');

        //coursedetail
        // $mform->addElement('header', 'coursedetailshdr', 'Course Details');
        $mform->addElement('header', 'courseformathdr', get_string('type_format', 'plugin'));

        $mform->addElement('hidden', 'customfield_virtual', 1);
        $mform->addElement('hidden', 'customfield_downloadable', 0);
        // $options2 = array(
        //     '1' => 'Simple Product','2' => 'Simple Subscription'
        // );
        // $select = $mform->addElement('select', 'customfield_productype', 'Product Type', $options2);
        // $select->setSelected('1');


        // $fullname =  array_column($usercourse, 'fullname');

        $select = $mform->addElement('autocomplete', 'customfield_upsells', 'Upsells', $fullcourse);
        $mform->setDefault('customfield_upsells', 0);
        
        $select = $mform->addElement('autocomplete', 'customfield_cross_sells', 'Cross-sells',  $fullcourse);
        $mform->setDefault('customfield_cross_sells', 0);

        //courseformat

        $courseformats = get_sorted_course_formats(true);
        $formcourseformats = array();
        foreach ($courseformats as $courseformat) {
            $formcourseformats[$courseformat] = get_string('pluginname', "format_$courseformat");
        }
        if (isset($course->format)) {
            $course->format = course_get_format($course)->get_format(); // replace with default if not found
            if (!in_array($course->format, $courseformats)) {
                // this format is disabled. Still display it in the dropdown
                $formcourseformats[$course->format] = get_string('withdisablednote', 'moodle',
                        get_string('pluginname', 'format_'.$course->format));
            }
        }
        unset($formcourseformats['singleactivity']);

        $mform->addElement('select', 'format', get_string('format'), $formcourseformats );
        $mform->addHelpButton('format', 'format');
        $mform->setDefault('format', 'topics');

        $options5 = range(0, 50);
        $select = $mform->addElement('select', 'numsection', 'Number of sections', $options5);
        $select->setSelected('1');

        $choices = array();
        $choices['0'] = 'Hidden sections are shown as not available';
        $choices['1'] = 'Hidden sections are completely invisible';
        $mform->addElement('select', 'hiddensections', 'Hidden sections', $choices);
        $mform->addHelpButton('visible', 'coursevisibility');
        $mform->setDefault('visible', '0');

        $choices2 = array();
        $choices2['0'] = 'Show all sections on one page';
        $choices2['1'] = 'Show one section per page';
        $mform->addElement('select', 'courselayout', 'Course layout', $choices2);
        $mform->addHelpButton('visible', 'coursevisibility');
        $mform->setDefault('visible', '0');

        // Appearance.
        $mform->addElement('header', 'appearancehdr', get_string('appearance'));
        // if ((empty($course->id) && guess_if_creator_will_have_course_capability('moodle/course:setforcedlanguage', $categorycontext))
        //         || (!empty($course->id) && has_capability('moodle/course:setforcedlanguage', $coursecontext))) {

        //     $languages = ['' => get_string('forceno')];
        //     $languages += get_string_manager()->get_list_of_translations();
        //     $mform->addElement('select', 'lang', get_string('forcelanguage'), $languages);
        //     $mform->setDefault('lang', '0');
        // }   

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

        //completion
        $mform->addElement('header', 'completionhdr', get_string('completion', 'completion'));

        $mform->addElement('selectyesno', 'enablecompletion', get_string('enablecompletion', 'completion'));
        $mform->setDefault('enablecompletion', $courseconfig->enablecompletion);
        $mform->addHelpButton('enablecompletion', 'enablecompletion', 'completion');
 
        $showcompletionconditions = $courseconfig->showcompletionconditions ?? COMPLETION_SHOW_CONDITIONS;
        $mform->addElement('selectyesno', 'showcompletionconditions', get_string('showcompletionconditions', 'completion'));
        $mform->addHelpButton('showcompletionconditions', 'showcompletionconditions', 'completion');
        $mform->setDefault('showcompletionconditions', $showcompletionconditions);

        // //Sections
        // $mform->addElement('header','Section','Sections' );

        // $mform->addElement('text', 'addasection','Add a Section');            
        // $mform->setType('addasection', PARAM_NOTAGS);


        // $repeatarray = array();
        // $repeatarray[] = $mform->createElement('date_time_selector', 'startime', 'Start Time');
        // $repeatarray[] = $mform->createElement('duration', 'limit', 'Duration' );
        // $repeatarray[] = $mform->createElement('hidden', 'optionid', 0);

        // $repeatno = 1;

        // // $repeateloptions = array();
        // // $repeateloptions['limit']['default'] = 0;
        // // $repeateloptions['limit']['disabledif'] = array('limitanswers', 'eq', 0);
        // // $repeateloptions['limit']['rule'] = 'numeric';
        // // $repeateloptions['limit']['type'] = PARAM_INT;

        // // $repeateloptions['option']['helpbutton'] = array('choiceoptions', 'choice');
        // $mform->setType('option', PARAM_CLEANHTML);

        // $mform->setType('optionid', PARAM_INT);

        // $this->repeat_elements($repeatarray, $repeatno,'', 'option_repeats', 'option_add_fields', 1, null, true);

        // Groups
        // $mform->addElement('header','groups', get_string('groupsettingsheader', 'group'));

        // $groupchoices = array();
        // $groupchoices[NOGROUPS] = get_string('groupsnone', 'group');
        // $groupchoices[SEPARATEGROUPS] = get_string('groupsseparate', 'group');
        // $groupchoices[VISIBLEGROUPS] = get_string('groupsvisible', 'group');
        // $mform->addElement('select', 'groupmode', get_string('groupmode', 'group'), $groupchoices);
        // $mform->addHelpButton('groupmode', 'groupmode', 'group');
        // $mform->setDefault('groupmode', $courseconfig->groupmode);

        $mform->addElement('hidden','groupmode', 1);
        $mform->addElement('hidden','id', 0);

        // $groups = array();
        // $groups[0] = get_string('none');
        // $mform->addElement('select', 'defaultgroupingid', get_string('defaultgrouping', 'group'), $groups);
        if(!empty($course->id)){
            $customdata = $this->get_course_metadata($course->id);
            // echo "<pre>";
            // print_r($customdata);
            // die;      
        }
        if($course){
            $course->customfield_virtual = $customdata['virtual'];
            $course->customfield_downloadable = $customdata['downloadable'];
            $course->customfield_age_group = $customdata['age_group'];
            $course->customfield_upsells = $customdata['upsells'];
            $course->customfield_metadescript = $customdata['metadescript'];
            $course->customfield_shortsummary_editor['text'] = $customdata['shortsummary'];
            $course->customfield_price = $customdata['price'];
            $course->customfield_cross_sells = $customdata['cross_sells'];
        }
        
        $this->add_action_buttons();
        $this->set_data($course);

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
        return array();
    }


    // function categories(){
       
    //     return $response;

    // }

}
?>
