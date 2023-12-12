<?php
https://galgano.academy/user/preferences.php  //For email notification
?>


if(){
            $obj = new stdClass();
            $obj->student_id = $data->userid;
            $obj->submission_id = $data->id;
            $obj->filename = $data->filename;
            $obj->onlinetext = $data->onlinetext;
        //    $aa = $DB->insert_record('review_assign_block',$obj);
            echo "<pre>";
            print_r($obj);
            echo "</pre>";
        }