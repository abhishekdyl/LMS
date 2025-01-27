<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['newLog'])) {
        try {
            $newLog = json_decode($_POST['newLog']);
            $oldlog = file_get_contents('access.log');
            $data = json_decode($oldlog);
        } catch (Exception $e) {
            $data = array();
        }
        if(empty($newLog)){
            echo 0;
        }
        if(empty($data)){
            $data = array();
        }
        $data = array_merge($data, $newLog);
        file_put_contents('access.log', json_encode($data));
    }
} 
