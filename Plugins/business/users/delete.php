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
 * Page to allow the administrator to delete networked hosts, with a confirm message
 *
 * @package    core
 * @subpackage mnet
 * @copyright  2007 Donal McMullan
 * @copyright  2007 Martin Langhoff
 * @copyright  2010 Penny Leach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');

// echo "<pre>";
// print_r($USER);
// die;
//require_once($CFG->libdir . '/adminlib.php');

$step   = optional_param('step', 'verify', PARAM_ALPHA);
$userid = required_param('userid',PARAM_INT);
$companyname = required_param('companyname', PARAM_ALPHA);
// print_r ($userid);

//$mnet = get_mnet_environment();

$PAGE->set_url('/local/business/users/delete.php');
//admin_externalpage_setup('mnetpeer' . $hostid);

require_sesskey();
$userdata=$DB->get_record("user",array('id'=>$userid));
$username=$userdata->username;
//$mnet_peer = new mnet_peer();
//$mnet_peer->set_id($hostid);

if(!empty($userid) && !empty($companyname)){
    
    if ('verify' == $step) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading("Delete User");
        if ($groupdata) {
            //echo $OUTPUT->notification("testttttttt");
        }
        $yesurl = new moodle_url('/local/business/users/delete.php', array('userid' => $userid, 'step' => 'delete'));
        $nourl = new moodle_url('/local/business/users/index.php');
        echo $OUTPUT->confirm('If you proceed, this person will no longer have access to your private eLearning portal : ' . , $yesurl, $nourl);
        echo $OUTPUT->footer();
    } elseif ('delete' == $step) {
        
        $sql="SELECT cbu.* FROM {user} u JOIN {custom_branding_users} cbu ON u.id=cbu.userid WHERE cbu.userid=? ";
        $data=$DB->get_record_sql($sql,array($userid));
        // echo "<pre>fffffffffff";
        // print_r($data);
        if($data){
            $data->status=0;
            $data->modifiedby=$USER->id;
            $data->modifieddate=time();
            $DB->update_record('custom_branding_users',$data);
            redirect(new moodle_url('/local/business/users/index.php'), 'This User '.$username .' Inactive from custom branding list successfully', 5);
        }else{
            redirect(new moodle_url('/local/business/users/index.php'), $username .' not avaliable in branding', 5);
        }
        // echo $groupdata->id;
        // print_r($coursedata);
        // echo "<br>";
        // print_r($userdata);
        // // die;
        // $groupdata->deleted=1;
        // $groupdata->updatedby=$USER->id;
        // $groupdata->updateddate=time();
        //$DB->update_record('utrains_groups',$groupdata);
    
        
    }

}else

