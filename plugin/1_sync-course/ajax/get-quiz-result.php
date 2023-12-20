<?php
ob_start();
session_start();
require_once('../../../../wp-config.php');
$ans_data=$_POST;
$quest_id_arr=array_keys($_POST);
$quiz_questions=$_SESSION['one_planet']['question_data'];
$total_frac=0;
$total_max=0;
foreach($quiz_questions->questions as $question){
	$total_max =$total_max+$question->maxmark;
	if(in_array($question->id, $quest_id_arr)){
		$ans_keys=$ans_data[$question->id];
		$max_mark=$question->maxmark;
		if(!is_array($ans_keys)){
			$ans_keys=array($ans_keys);
		}
		$frac=0;
			$selected_ans_key_data = array_filter($question->question_answer_text, function($obj) use ($ans_keys) {
			if (in_array($obj->id,$ans_keys)) {
			        return true;
			    }
			});
			foreach($selected_ans_key_data as $user_ans){
				$frac =($frac+$user_ans->fraction);
			}
			
		
		$total_frac =($total_frac+($frac*$max_mark));
	}
}
$passing_student_data=(($total_frac/$quiz_questions->sumgrades)*$quiz_questions->grade);
if($passing_student_data>=$quiz_questions->passing_grade->gradepass){
	$msg['status']=true;
	$msg['msg']= 'Congratulations you have passed the technical knowledge requirement to enroll to one planet  online College. Please continue to the registration process and view your Courses.';
	$_SESSION['one_planet']['quiz_status']=true;
}else{
	$msg['status']=false;
	$msg['msg']= 'You must be need to pass the digital literacy test in order to join the online program';
}
$inserted_id=$wpdb->insert("{$wpdb->prefix}question_status", 
	array('question_data' => serialize($quiz_questions->questions), 
		'user_data' => serialize($ans_data),
		'passing_grade' => $quiz_questions->passing_grade->gradepass,
		'gotgrade' => $passing_student_data,
		'grade' => $quiz_questions->grade,
		'passing_status' => $msg['status'],
		'createddate' => time()
) );
$_SESSION['one_planet']['question_status_id']=$wpdb->insert_id;
$msg['session']=$_SESSION['one_planet'];
$msg['inserted_id']=$wpdb->insert_id;

echo json_encode($msg);