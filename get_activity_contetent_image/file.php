<?php

/* 
 * This file is use to retrive files from hidden module directories
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// disable moodle specific debug messages and any errors in output
// define('NO_DEBUG_DISPLAY', true);

require('../../config.php');

require_once($CFG->dirroot."/lib/filelib.php");

//require_login();
if (isguestuser()) {
    print_error('noguest');
}

//Add custom code to get relativepath by this "file_rewrite_pluginfile_urls"

    if (empty($relativepath)) {
        $relativepath = get_file_argument();
    }
    $fileinfo = explode("/", $relativepath);

//end

// $contenthash = optional_param('id', 0, PARAM_TEXT);
$forcedownload = optional_param('forcedownload', 0, PARAM_BOOL);
$preview = optional_param('preview', null, PARAM_ALPHANUM);
if( $forcedownload ){
    $file_download = true;
} else{
    $file_download = false;
}

//custom start

    $fileinfo = [
        'contextid' => $fileinfo[1],   // ID of context.
        'component' => $fileinfo[2], // Your component name.
        'filearea'  => $fileinfo[3],       // Usually = table name.
        'itemid'  => $fileinfo[4],       // Usually = table name.
        'filepath'  => '/',            // Any path beginning and ending in /.
        'filename'  => $fileinfo[5],   // Any filename.
      ];
    // $file1 = $DB->get_record("files", $fileinfo); // file hash get here
    // $file = $fs->get_file_by_hash( $contenthash ); // 1st. if you can pass file(img) hash than get image 

    //2nd to get image by pass this param

    $fs = get_file_storage();
    $file = $fs->get_file(
        $fileinfo['contextid'],
        $fileinfo['component'],
        $fileinfo['filearea'],
        $fileinfo['itemid'],
        $fileinfo['filepath'],
        $fileinfo['filename']
    );

//custom end



if( $file ){
    
// ========================================
// finally send the file
// ========================================
//\core\session\manager::write_close(); // Unlock session during file serving.
//send_stored_file($file, null, $CFG->filteruploadedfiles, $forcedownload);

   \core\session\manager::write_close(); // Unlock session during file serving.
send_stored_file($file,  0, false, $file_download , array('preview' => $preview) );

//\core\session\manager::write_close(); // Unlock session during file serving.
//send_stored_file($file, 0, false, true, array('preview' => $preview)); // force download - security first!


}
else{
    send_file_not_found();
}