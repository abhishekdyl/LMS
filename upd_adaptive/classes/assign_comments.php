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
 * This file contains the moodle hooks for the comments feedback plugin
 *
 * @package   assignfeedback_adaptive
 * @copyright 2018 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */ 
namespace assignfeedback_adaptive;

class assign_comments {

	public function insert_assign_feedback($params) { 
		global $DB, $USER;	
		$feedback = $params['feedback'];
		$type = $params['type'];	
		$asgid = $params['asgid'];	
		
		$rec->feedback_type = $type;
		$rec->userid = $USER->id;	
		$rec->feedback = $feedback;	
		$rec->assignment_id = $asgid;	

		$insert = $DB->insert_record('feedback_type',$rec);
		
		if ($insert) {
                $savesuccess = true;
                $savemsg = 'Feedback has been inserted successfully!';
                $htmltext = '<li class="thrfeedback" data-id="'.$insert.'"><span class="fbname">'.$feedback.'</span><span class="actionbuttons"><i class="icon fa fa-cog fa-fw edit_feedback" title="Edit" aria-label="Edit" data-id="'.$insert.'" ></i><i class="icon fa fa-trash fa-fw delete_feedback " title="Delete" aria-label="Delete" data-id="'.$insert.'"></i></span></li>';
            } 
		$data = array(
			'result' => $savesuccess,
            'message' => $savemsg,
            'htmltext' => $htmltext,
        );
        return $data;
	}
	public function delete_assign_feedback($params) {
		global $DB;
		$id = $params['id'];

		$delete = $DB->delete_records('feedback_type', array('id'=>$id));
		
		if ($delete) {
                $dltsuccess = true;
                $deletemsg = 'Feedback has been deleted successfully!';
            }
		$data = array(
			'result' => $dltsuccess,
            'message' => $deletemsg
        );
        return $data;
	}

	public function edit_assign_feedback($params) {
		global $DB;
		$id = $params['id'];
		$feedback = $params['feedback'];
		
		$records = $DB->get_records_sql("SELECT id from {feedback_type} WHERE id = $id");
        if($records) {
            foreach ($records as $record) {
                $rec->id = $record->id;
                $rec->feedback = $feedback;
                $updt = $DB->update_record('feedback_type', $rec);
				if ($updt) {
		            $updsuccess = true;
		            $updmsg = 'Feedback has been updated successfully!';
		        }
				$data = array(
					'result' => $updsuccess,
		            'message' => $updmsg
		        );
		        return $data;
	        }
        }
    }    
}	

