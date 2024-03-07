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
 * user signup page.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->libdir . '/authlib.php');
require_once('lib.php');

if (!$authplugin = signup_is_enabled()) {
    print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
}

$PAGE->set_url('/login/signup.php');
$PAGE->set_context(context_system::instance());

// If wantsurl is empty or /login/signup.php, override wanted URL.
// We do not want to end up here again if user clicks "Login".
if (empty($SESSION->wantsurl)) {
    $SESSION->wantsurl = $CFG->wwwroot . '/';
} else {
    $wantsurl = new moodle_url($SESSION->wantsurl);
    if ($PAGE->url->compare($wantsurl, URL_MATCH_BASE)) {
        $SESSION->wantsurl = $CFG->wwwroot . '/';
    }
}

if (isloggedin() and !isguestuser()) {
    // Prevent signing up when already logged in.
    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
    $logout = new single_button(new moodle_url('/login/logout.php',
        array('sesskey' => sesskey(), 'loginpage' => 1)), get_string('logout'), 'post');
    $continue = new single_button(new moodle_url('/'), get_string('cancel'), 'get');
    echo $OUTPUT->confirm(get_string('cannotsignup', 'error', fullname($USER)), $logout, $continue);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

// If verification of age and location (digital minor check) is enabled.
if (\core_auth\digital_consent::is_age_digital_consent_verification_enabled()) {
    $cache = cache::make('core', 'presignup');
    $isminor = $cache->get('isminor');
    if ($isminor === false) {
        // The verification of age and location (minor) has not been done.
        redirect(new moodle_url('/login/verify_age_location.php'));
    } else if ($isminor === 'yes') {
        // The user that attempts to sign up is a digital minor.
        redirect(new moodle_url('/login/digital_minor.php'));
    }
}

// Plugins can create pre sign up requests.
// Can be used to force additional actions before sign up such as acceptance of policies, validations, etc.
core_login_pre_signup_requests();

$mform_signup = $authplugin->signup_form();

if ($mform_signup->is_cancelled()) {
    redirect(get_login_url());

} else if ($user = $mform_signup->get_data()) {
    // Add missing required fields.
    // echo '<pre>';
    // print_r($user);die();
    $user = signup_setup_new_user($user);

    // Plugins can perform post sign up actions once data has been validated.
    core_login_post_signup_requests($user);

    $authplugin->user_signup($user, true); // prints notice and link to login/index.php
    exit; //never reached
}


$newaccount = get_string('newaccount');
$login      = get_string('login');

$PAGE->navbar->add($login);
$PAGE->navbar->add($newaccount);

$PAGE->set_pagelayout('login');
$PAGE->set_title($newaccount);
$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();
?>
<style type="text/css">
    .fitem.intrest_options {
        border-bottom: 1px solid #fff;
        line-height: 30px;
        padding-left: 0px;
        margin-left: 10px;
        margin-right: 20px;
        font-size: 20px;
    }
    .custommodel{
        display: flex;
        position: fixed;
        top: 0px;
        width: 100%;
        left: 0px;
        height: 100%;
        z-index: 999;
        background: #0006;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        color: #555555;
    }
    .custommodel.active{
        visibility: visible;
        opacity: 1;        
    }
    .custommodel > div {
        background: #fff;
        padding: 30px;
        max-width: 450px;
        box-shadow: 1px 1px 8px 1px #000;
    }
    .custommodel .model_close {
        text-align: center;
        margin-top: 15px;
    }
    .custommodel .model_close button{
        padding: 5px 50px;
    }
    .custregcomp>span {
        /* text-decoration: underline; */
        color: #EBA600;
        cursor: pointer;
    }
    .accesscode_help{
        color: #ffffff !important;
    }
    .custregcomp>span:hover, .acceptterms_check a:hover, #fitem_id_profile_field_accesscode a:hover {
        color: #ffffff;
        transition: all 0.3s ease 0.1s;
        text-decoration: none;
    }
    #fitem_id_passwordpolicyinfo{
        margin-top: -20px;
        position: relative;
    }
    #fitem_id_generalnews{
        position: relative;
    }
    #fitem_id_generalnews .fitemtitle{
        position: absolute;
        left: 40px;
    }
    #fitem_id_generalnews .felement label{
        padding-left: 10px;
    }
    #fitem_id_userroles.hidefrmuser{
        position: relative;
    }
    #fitem_id_userroles.hidefrmuser::before{
        position: absolute;
        width: 100%;
        top: 0px;
        left: 0px;
        height: 100%;
        min-height: 50px;
    }
    #fitem_id_profile_field_company .felement{
        position: relative;
    }
    #countrybrowsers {
        position: absolute;
        top: 36px;
        background: #fff;
        /*padding: 5px 0px;*/
        color: #000;
        border-radius: 2px;
        z-index: 999;
        /*right: 16px;*/
        width: 95%;
        left: 0px;
    }
    #countrybrowsers ul {
        margin: 0px!important;
        max-height: 350px;
        overflow-y: scroll;
    }
    #countrybrowsers li {
        padding: 5px;
        list-style: none;
        border-bottom: 1px solid #aaa;
    }
    #countrybrowsers li.background {
        background-color: #ddd;
    }
    @media only screen and (max-width: 600px) {
        .custommodel > div {
            width: 80%;
        }
    }
    #company_group_containar>.fadvcheckbox>label{
        display: inline-flex;
    }
    #company_group_containar #id_companygroup{
        position: relative;
        top: 7px;
    }


</style>


<!-- <div class="countrymissingmodel"><div><div>Please type in the company name as "other" or "not listed" in order to complete the registration</div> -->
<div class="countrymissingmodel custommodel">
    <div>
        <div><p>Please select your workplace from the list that appears when you enter your postcode, suburb or company name. If your company isn’t in the list, type ‘Other’ as the company name, then select “Other – company’.</p><p>If you encounter any technical issues, please email <a href='mailto:CustomerCare.Australia@boehringer-ingelheim.com'>CustomerCare.Australia@boehringer-ingelheim.com</a> or call <a href='tel:1800 808 691'>1800 808 691</a>. </p> </div>
        <div class="model_close">
            <p class="btn " style="padding: 5px 50px;">OK</p>
        </div>
    </div>
</div>
<div class="errorpasscodemodel custommodel">
    <div>
        <div>The passcode you have entered is incorrect, please check with your store manager or Boehringer Ingelhiem Territory manager and try again.</div>
        <div class="model_close">
            <p class="btn " style="padding: 5px 50px;">OK</p>
        </div>
    </div>
</div>
<div class="erroruserrolemodel custommodel">
    <div>
        <div>Your company is currently only registered for Rural Reseller user accounts at Animal Health Academy. If you think to be in error, please contact Customer Care on <a href='tel:1800 808 691'>1800 808 691</a></div>
        <div class="model_close">
            <p class="btn" style="padding: 5px 50px;">OK</p>
        </div>
    </div>
</div>
<div class="invalidroleforagencymodel"><div><div></div>
<script
  src="https://code.jquery.com/jquery-2.2.4.js"
  integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI="
  crossorigin="anonymous"></script>

<script src="<?php echo $CFG->wwwroot.'/login/companyfilter.js?ver='.date("Ymd");?>"></script>
<script type="text/javascript">
    var companyvalidation = {
        agency:0,
        passcode:""
    };
    var selectedCompanyText, liSelected;
    $(document).ready(function(){
        $("#id_profile_field_company ").attr("placeholder","Enter your postcode or suburb");
        var aaaaaa = $("#fitem_id_profile_field_company label").html();
        console.log("aaaaaa- ", aaaaaa);
        var bbbb = aaaaaa.replace("Company", "Search for company");
        console.log("aaaaaa- ", bbbb);
        $("#fitem_id_profile_field_company label").html(bbbb);


        // var capsulepasscode='<div id="casuleagenypasscode" style="display: none;"><p class="custregcomp"> The company you have selected is registered with Boehringer Ingelheim as a Capsule Agent so you will need to enter the passcode above to continue. </p></div>';


        $("#fitem_id_userroles").insertAfter("#fitem_id_profile_field_company");

        var list_companydata = "";
        list_companydata += "<div id='countrybrowsers'></div><p class='custregcomp'>Can’t find your company? <span class='companymodelview' style='text-decoration: underline;'>Click here</span></p>";


        list_companydata += '<input maxlength="2048" size="30" name="other_company" type="text" value="" placeholder="Please enter your company name" id="id_other_company" style="display:none;">';


        $("#fitem_id_profile_field_company .felement").append(list_companydata);
        console.log(list_companydata);
        $("#fitem_id_profile_field_accesscode .felement").append("<p class='accesscode_help'>Need help with the Access Code? Call Customer Care on <a href='tel:1800 808 691'>1800 808 691</a></p>");

$("#fitem_id_profile_field_company .felement #id_profile_field_company").change(function(){
    var companyname = $(this).val();
    console.log("change- ", companyname);
    check_companyname(companyname,true);
});
$("#id_profile_field_company").focusout(function(){
    var companyname = $(this).val();
    console.log("focusout- ", companyname);
    check_companyname(companyname);
});
check_companyname($("#fitem_id_profile_field_company .felement #id_profile_field_company").val(),false);



$(document).on( "click", "#countrybrowsers ul li", function(){
    var selectedcountry = $(this).text();
    console.log("selectedcountry- ", selectedcountry);
    $("#fitem_id_profile_field_company .felement #id_profile_field_company").val(selectedcountry);
    $("#countrybrowsers").hide();
    check_companyname(selectedcountry,true);
});
$("#id_profile_field_company").focusin(function(){
    $("#countrybrowsers").show();
});
$("#fitem_id_profile_field_company").focusout(function(){
    // console.log("checkfocus- ", $("#countrybrowsers").is(":focus"));
    if($("#countrybrowsers").is(":focus")){
        // console.log("countrybrowsers focused");

    } else {
        setTimeout(function(){ $("#countrybrowsers").hide(); }, 1000);
    }
    // 
});

var seltxt = "";
$("#id_profile_field_company").keydown(function(event){
    if(event.which === 13){
        event.preventDefault();
        return false;
    }

});
$("#id_profile_field_company").keyup(function(event){
	$('input[name="profile_field_companyid"]').val("");
    var currenttext = $(this).val();
    console.log("e.which- ", event.which);
    console.log("e.keyCode- ", event.keyCode);
    currenttext = currenttext.trim();
    currenttext = currenttext.toLowerCase();
    var newdata = "";
    console.log("seltxt- ", seltxt);
    console.log("selectedCompanyText- ", selectedCompanyText);
    if(event.which === 13 && seltxt){
        console.log("enter- ", seltxt);
        $("#fitem_id_profile_field_company .felement #id_profile_field_company").val(seltxt);
        $("#countrybrowsers").hide();
        check_companyname($("#fitem_id_profile_field_company .felement #id_profile_field_company").val(),true);
        $("#countrybrowsers").hide();
        event.preventDefault();
        return false;
    }
    if(event.which === 40 || event.which === 38){
        seltxt = movefocusinfilters(event);
        event.preventDefault();
        return false;
    }
    liSelected = null;
    $("#countrybrowsers").html("");
    if(currenttext.length >= 2 ){
        var filterdata = prev_companydata.filter(function checkAdult(comp) {
              return comp.toLowerCase().search(currenttext) >= 0;
            }
        );
        if(filterdata.length > 30){
            filterdata.length = 30
        };
        for (var i = 0; i < filterdata.length; i++) { 
            var filterdstr = filterdata[i].replace(/\&amp;/g,'&');
            newdata += "<li>"+filterdstr+"</li>";
        }
        if(newdata == ""){
            $("#countrybrowsers").hide();
        } else {
            $("#countrybrowsers").show();
        }
        var fData =  "<ul>" + newdata + "</ul>";
        $("#countrybrowsers").html(fData);
    }
});

        $(".companymodelview").click(function(){
            $(".countrymissingmodel").toggleClass("active");
        });
    });
    function movefocusinfilters(e){
        var li = $('#countrybrowsers > ul > li');
        console.log("e.which- ", e.which);
        if(e.which === 40){
            if(liSelected){
                liSelected.removeClass('background');
                next = liSelected.next();
                if(next.length > 0){
                    liSelected = next.addClass('background');
                    selectedCompanyText = next.text();

                }else{
                    liSelected = li.eq(0).addClass('background');
                    selectedCompanyText = li.eq(0).text();
                }
            }else{
                liSelected = li.eq(0).addClass('background');
                    selectedCompanyText = li.eq(0).text();
            }
        }else if(e.which === 38){
            if(liSelected){
                liSelected.removeClass('background');
                next = liSelected.prev();
                if(next.length > 0){
                    liSelected = next.addClass('background');
                    selectedCompanyText = next.text();

                }else{

                    liSelected = li.last().addClass('background');
                    selectedCompanyText = li.last().text()
                }
            }else{

                liSelected = li.last().addClass('background');
                selectedCompanyText = li.last().text()
            }
        }
        console.log(selectedCompanyText);
        return selectedCompanyText;
    }

    function check_companyname(companyname,popup){
        console.log("Checking- ", companyname);
        if(companyname && companyname.toLowerCase() == "other - company"){
            $("#id_other_company").show();
        } else {
            $("#id_other_company").val("");
            $("#id_other_company").hide();
        }
        checkcapsuleCompany();
    }

    $(document).ready(function(){
        $(".custommodel .model_close .btn, .custommodel").click(function(){
            $(this).closest(".custommodel").removeClass("active");
        });
    });
    var XHR = null;
    function checkcapsuleCompany(){
        var companyname = $("#fitem_id_profile_field_company .felement #id_profile_field_company").val();
        var userroles = $("#id_userroles").val();
        console.log("userroles- ", userroles);
        if(XHR){
            XHR.abort();
        }
        XHR = $.ajax({url: "ajexcapsule.php?company="+encodeURIComponent(companyname), success: function(result){
            console.log(result);
            companyvalidation = JSON.parse(result);
            if(companyvalidation.agency == 1){
                $('#id_userroles').val(12);
                $('#id_userroles').attr("readonly","readonly");
                $('#fitem_id_userroles').addClass("hidefrmuser");
                $("#id_userroles").trigger("change");
            } else {
                $('#fitem_id_userroles').removeClass("hidefrmuser");
                $('#id_userroles').removeAttr("readonly");
            }
            console.log("companyvalidation-", companyvalidation)
            if(companyvalidation.id) {
                $('input[name="profile_field_companyid"]').val(companyvalidation.id);
            } else {
                $('input[name="profile_field_companyid"]').val("");
            } 
            if(companyvalidation.company_group){
                $('#company_group_containar').show();
                $('#company_group_text').html(companyvalidation.company_group.consent_text);
                if(companyvalidation.company_group.req_consent == 1){
                    $("#id_companygroup").attr("required", "required");
                    $("#company_group_containar .req").show();
                } else {
                    $("#company_group_containar .req").hide();
                    $("#id_companygroup").removeAttr("required");
                }
            } else {
                $("#id_companygroup").removeAttr("required");
                $('#id_companygroup').prop('checked', false);
                $('#company_group_containar').hide();
                $('#company_group_text').html("");
            }
        }});
    }

    
</script>

<?php
if ($mform_signup instanceof renderable) {
    // Try and use the renderer from the auth plugin if it exists.
    try {
        $renderer = $PAGE->get_renderer('auth_' . $authplugin->authtype);
    } catch (coding_exception $ce) {
        // Fall back on the general renderer.
        $renderer = $OUTPUT;
    }
    echo $renderer->render($mform_signup);
} else {
    // Fall back for auth plugins not using renderables.
    $mform_signup->display();
}
echo $OUTPUT->footer();
?>

<!-- 30  09  2020 -->

<style>
    #page-login-signup .card-title.text-xs-center {
        display: none;
    }

    #page-login-signup .clearfix.collapsible>legend.ftoggler {
        display: none;
    }

    img.icon {  
        margin-left: 4px;
    }
</style>

