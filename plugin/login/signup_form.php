<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * User sign-up form.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once('lib.php');

class login_signup_form extends moodleform implements renderable, templatable {
    function definition() {
        global $USER, $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'createuserandpass', get_string('createuserandpass'), '');

        //----------------------sushil start--------------------------------
        $mform->addElement('text', 'email', "Email Address", 'maxlength="100" size="25", 
                            placeholder="Enter valid email address"');
        $mform->setType('email', core_user::get_property_type('email'));
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email');

        $mform->addElement('text', 'username', 'Username', 'maxlength="100" size="12" autocapitalize="none" , 
                    placeholder="Enter valid email address" ');
        $mform->setType('username', core_user::get_property_type('email'));
        $mform->addRule('username', get_string('missingemail'), 'required', null, 'client'); 
        //----------------------sushil end--------------------------------

        // $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="12" autocapitalize="none"');
        // $mform->setType('username', PARAM_RAW);
        // $mform->addRule('username', get_string('missingusername'), 'required', null, 'client');

        $mform->addElement('password', 'password', get_string('password'), 'maxlength="32" size="12"
        placeholder="Enter valid password"');

        if (!empty($CFG->passwordpolicy)){
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        // $mform->addElement('password', 'confirmpassword', get_string('password'), 'maxlength="32" size="12"');
        $mform->setType('password', core_user::get_property_type('password'));
        $mform->addRule('password', get_string('missingpassword'), 'required', null, 'client');

        $mform->addElement('header', 'supplyinfo', get_string('supplyinfo'),'');



        // $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25"');
        // $mform->setType('email', core_user::get_property_type('email'));
        // $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        // $mform->setForceLtr('email');

        // $mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" size="25"');
        // $mform->setType('email2', core_user::get_property_type('email'));
        // $mform->addRule('email2', get_string('missingemail'), 'required', null, 'client');
        // $mform->setForceLtr('email2');

        //----------------------------sushil start ----------------------------  
        $mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" size="25",
                            placeholder="Enter valid email address"');
        $mform->setType('email2', core_user::get_property_type('email'));
        $mform->addRule('email2', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email2');

        $mform->addElement('html', '<script>
            $("#id_email").keyup(function(){
                console.log($(this).val());
                $("#id_username").val($(this).val());
                $("#id_email2").val($(this).val());
            });
            $("#id_password").keyup(function(){
                $("#fitem_id_passwordpolicyinfo ~ #fitem_id_password #id_password").val($(this).val());
            });
            $(document).ready(function(){
                $("#id_cancel").mousedown(function(){
                    $("#id_acceptterms").prop("checked", "checked");
                    $("#id_generalnews").prop("checked", "checked");
                    $("#id_profile_field_accesscode").val(" ");
                });
            });
            </script><style>
                #fitem_id_username,#fitem_id_email2{display:none;}.fcontainer .fitem_fadvcheckbox .fitemtitle {width: fit-content !important;margin-right: 15px;}.acceptterms_check {clear: both;padding: 0px 0px 20px 20px; } .acceptterms_check#company_group_containar{display:none; }
            </style>');
     
        //----------------------------sushil end ----------------------------

        $namefields = useredit_get_required_name_fields();
        foreach ($namefields as $field) {
            $mform->addElement('text', $field, get_string($field), 'maxlength="100" size="30"');
            $mform->setType($field, core_user::get_property_type('firstname'));
            $stringid = 'missing' . $field;
            if (!get_string_manager()->string_exists($stringid, 'moodle')) {
                $stringid = 'required';
            }
            $mform->addRule($field, get_string($stringid), 'required', null, 'client');
        }


        // ------------------------------  sushil start --------------------------------------
        $allroles = role_fix_names(get_all_roles());
        $rolsedrop = array ();
        $rolsedrop[""] = "Select"; 
        foreach($allroles as $allrole){
            if($allrole->id == 7){
               continue;
            } 
            if($allrole->id <= 8){ 
               continue;
            }if($allrole->id == 13){ 
               continue;
            }if($allrole->id == 14){ 
               continue;
            }if($allrole->id == 15){ 
               continue;
            }if($allrole->id == 16){ 
               continue;
            }if($allrole->id == 18){ 
               continue;
            }if($allrole->id == 20){ 
               continue;
            }if($allrole->id == 23){ 
               continue;
            }
            $rolsedrop["$allrole->id"] = $allrole->localname; 
        }
        $attributes = array();
        //array_push(onchange = "OnChangeEvent(this)")
        $select = $mform->addElement('select', 'userroles', get_string('roles'), $rolsedrop, $attributes);
        $mform->addRule('userroles', "Missing user role", 'required', null, 'client');
        //$mform->setDefault('userroles', '9'); 
        
        if( !empty($CFG->country) ){
            $mform->setDefault('country', $CFG->country);
        }else{
            $mform->setDefault('country', '');
        }
        $mform->addElement('hidden', 'profile_field_companyid', 0);
        // $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="20"');
        // $mform->setType('city', core_user::get_property_type('city'));
        // if (!empty($CFG->defaultcity)) {
        //     $mform->setDefault('city', $CFG->defaultcity);
        // }

        // $country = get_string_manager()->get_list_of_countries();
        // $default_country[''] = get_string('selectacountry');
        // $country = array_merge($default_country, $country);
        // $mform->addElement('select', 'country', get_string('country'), $country);

        // if( !empty($CFG->country) ){
        //     $mform->setDefault('country', $CFG->country);
        // }else{
        //     $mform->setDefault('country', '');
        // }



        profile_signup_fields($mform);


        $manager = new \core_privacy\local\sitepolicy\manager();
        $manager->signup_form($mform);
        $terms_checked = '';
        if($_POST['acceptterms']){
            $terms_checked = " checked ";
        }

        $terms_checked_cg = '';
        if($_POST['companygroup']){
            $terms_checked_cg = " checked ";
        }

        $mform->addElement('html', '<p class="acceptterms_check">Interest areas help to customise content according to what is most relevant to you.</p>');
        $mform->addElement('html', '<div class="acceptterms_check">');

        $mform->addElement('html', '<div class="felement fadvcheckbox" data-fieldtype="advcheckbox"><label for="id_acceptterms"><input name="acceptterms" '.$terms_checked.' type="checkbox" value="1" title="Must accept Terms of Use and Privacy Policy" required id="id_acceptterms"/><span class="req" ><img class="icon " alt="Required field" title="Required field" src="'.$CFG->wwwroot.'/theme/image.php?theme=lambda&amp;component=core&amp;image=req" id="yui_3_17_2_1_1550294776494_100"></span><span>I agree to the <a href="https://www.animalhealthacademy.com.au/staticpages/terms-of-use.php" target="_blank" rel="nofollow"> Terms of Use </a> and <a href="https://www.boehringer-ingelheim.com/au/data-privacy" target="_blank" rel="nofollow">Privacy Policy</a></span></label></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('advcheckbox', 'generalnews', "", 'I consent to receive email communications relating to Animal Health Academy content updates, details of upcoming events and webinars, and other relevant technical communications.', array('group' => 1, "required"=>"required" ), array(0, 1)); 
        $mform->addRule('generalnews', "Required", 'required', null, 'server');


        $mform->addElement('html', '<div class="acceptterms_check" id="company_group_containar">');
        $mform->addElement('html', '<div class="felement fadvcheckbox" data-fieldtype="advcheckbox"><label for="id_companygroup"><input name="companygroup" '.$terms_checked_cg.' type="checkbox" value="1" title="" id="id_companygroup"/><span class="req" ><img class="icon " alt="Required field" title="Required field" src="'.$CFG->wwwroot.'/theme/image.php?theme=lambda&amp;component=core&amp;image=req"></span><span id="company_group_text"></span></label></div>');
        $mform->addElement('html', '</div>');





        // ------------------------------  sushil end --------------------------------------


        if (signup_captcha_enabled()) {
            $mform->addElement('recaptcha', 'recaptcha_element', get_string('security_question', 'auth'));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }

        // Hook for plugins to extend form definition.
        core_login_extend_signup_form($mform);

        // Add "Agree to sitepolicy" controls. By default it is a link to the policy text and a checkbox but
        // it can be implemented differently in custom sitepolicy handlers.
        $manager = new \core_privacy\local\sitepolicy\manager();
        $manager->signup_form($mform);

        // buttons
        $this->add_action_buttons(true, get_string('createaccount'));

    }

    function definition_after_data(){
        $mform = $this->_form;
        $mform->applyFilter('username', 'trim');

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }
    }

    /**
     * Validate user supplied data on the signup form.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        // $errors = parent::validation($data, $files);

        //--------------------sushil start-------------------------
        global $DB;
        $data['email'] = strtolower($data['email']);
        $data['email2'] = strtolower($data['email2']);
        $data['username'] = strtolower($data['username']);
        $data['profile_field_company'] = htmlspecialchars_decode($data['profile_field_company']);
        $errors = parent::validation($data, $files);
        $skippaccesscode = array(12,19,20,21);
        if(!in_array($data['userroles'], $skippaccesscode)){
            if($data['userroles'] == 22 && strtolower($data['profile_field_accesscode']) != 'nexgard') {
                $data['profile_field_accesscode'] = "";
                $errors['profile_field_accesscode'] = "Invalid access code.";
            } else if($data['userroles'] != 22 && strtolower($data['profile_field_accesscode']) != 'myacademy') {
                $data['profile_field_accesscode'] = "";
                $errors['profile_field_accesscode'] = "Invalid access code";
            }
        }

        $match_company = $DB->get_record_sql("select * from mdl_company_list where CONCAT(name, ' - ' ,address)like ?", array($data['profile_field_company']));
        if(!empty($match_company) && !empty($data['profile_field_companyid'])){
            if(strtolower($match_company->external_id) == 'other'){
                if(empty($_POST['other_company'])){
                    $errors['profile_field_company'] = "Please enter your company name";
                }
            }
        } else {
            $errors['profile_field_company'] = "Please select an option from the list"; 
        }
        //--------------------sushil end-------------------------

        // Extend validation for any form extensions from plugins.
        $errors = array_merge($errors, core_login_validate_extend_signup_form($data));

        if (signup_captcha_enabled()) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['g-recaptcha-response'])) {
                $response = $this->_form->_submitValues['g-recaptcha-response'];
                if (!$recaptchaelement->verify($response)) {
                    $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'auth');
                }
            } else {
                $errors['recaptcha_element'] = get_string('missingrecaptchachallengefield');
            }
        }

        $errors += signup_validate_data($data, $files);

        return $errors;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        ob_start();
        $this->display();
        $formhtml = ob_get_contents();
        ob_end_clean();
        $context = [
            'formhtml' => $formhtml
        ];
        return $context;
    }
}
