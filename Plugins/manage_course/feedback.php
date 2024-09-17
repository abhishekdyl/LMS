<?php
require_once('../../config.php');
global $DB, $CFG ,$PAGE ,$USER;
require_login();
$id = optional_param('id', 0, PARAM_INT); 

if($_POST['massage']){
    $massageobj = $_POST;
    $aa = $DB->insert_record('course_feedback',$massageobj,$returnid = true,$bulk = false);
}

$chats = $DB->get_records('course_feedback',array('courseid'=>$id));
$course = $DB->get_record('course',array('id'=>$id));
echo $OUTPUT->header();
echo ' <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<div class="main" id="course_chat" >
    <div class="container">
        <div class="row">
            <div class="col-12 border">
                <div class="row">
                    <div class="col-9 coursenam">
                        <h2 class="course-title p-3" >'.$course->fullname.'</h2>
                    </div>
                    
                    <a class="col-3 course-btn" href="/course/view.php?id='.$id.'">View</a>
                    
                </div>
                <hr>
                <div class="mg-body">
                    <ul>';
                foreach ($chats as $value) {
                    $uname = $DB->get_record('user',array('id'=>$value->userid));
                    if(date('d/m/y',time()) == date('d/m/y',$value->date)){$dtime = date('h:i A',$value->date); }else{$dtime = date('F, j Y h:i A',$value->date); }
                    if($value->userid == $USER->id){
                        echo '<li style="text-align: right;"><span>'.$uname->firstname.', '.$dtime.'</span><p>'.$value->massage.'</p></li>';   
                    }else{
                        echo '<li><span>'.$uname->firstname.', '.$dtime.'</span><p>'.$value->massage.'</p></li>';   
                    }
                }
            echo '  </ul>
                </div>
                <form class="row btn-form" method="POST">
                    <textarea name="massage" class="col-9"></textarea>
                    <input type="hidden" name="courseid" value="'.$id.'" >
                    <input type="hidden" name="userid" value="'.$USER->id.'" >
                    <input type="hidden" name="date" value="'.time().'" >
                  
                    <button class="col-3 send" type="submit">Send</button>
                   
                </form>
            </div>
        </div>
    </div>

</div>';
echo $OUTPUT->footer();
?>
<style>
#course_chat li{
    list-style: none;
}
#course_chat p{
    font-size: 19px;
}
#course_chat hr{
    margin-top: 0px;
}
#course_chat .coursenam .course-title{
    margin-bottom: 0px !important;
}
#course_chat .btn-form .send{
    background: aliceblue;
    text-align: center;
    padding: 19px;
    color : #4caf50;
}
#course_chat .course-btn{
    text-align: center;
    padding: 19px;
    background: aliceblue;
}
#course_chat .course-btn:hover{
    background-color: #4caf50 !important;
    color: #fff !important;
    transition: all 0.3s ease 0.1s;
    text-decoration: none;
}
</style>