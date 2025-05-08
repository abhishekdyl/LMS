<?php
require('../../config.php');
   require "$CFG->libdir/tablelib.php";

   $context = context_system::instance();
   $PAGE->set_context($context);
   $PAGE->set_url('/local/business/test.php');
   
   $download = optional_param('download', '', PARAM_ALPHA);
   
   $table = new table_sql('uniqueid');
   $table->is_downloading($download, 'test', 'testing123');
   
   if (!$table->is_downloading()) {
    
       $PAGE->set_title('Testing');
       $PAGE->set_heading('Testing table class');

       echo $OUTPUT->header();
   }
   $table->set_sql('*', "{lightboxgallery_image_vote}", '1=1');
   $table->define_baseurl("$CFG->wwwroot/local/business/test.php"); 
   $table->out(10, true);
   


   if (!$table->is_downloading()) {
       echo $OUTPUT->footer();
   }
   
   // function get_course_image($courseid)
   // {
   //    global $CFG, $COURSE;
   //    $url = '';
   //    require_once( $CFG->libdir . '/filelib.php' );
   //    $context = context_course::instance( $courseid );
   //    $fs = get_file_storage();
   //    $files = $fs->get_area_files( $context->id, 'course', 'overviewfiles', 0 );
   //    foreach ( $files as $f )
   //    {
   //      if ( $f->is_valid_image() )
   //      {
   //         $url = moodle_url::make_pluginfile_url( $f->get_contextid(), $f->get_component(), $f->get_filearea(), null, $f->get_filepath(), $f->get_filename(), false );
   //      }
   //    }
   //    return $url;
   // }
   // echo get_course_image('383');

   // function print_pattern($num)
   // {  
   // for ($i = 0; $i < $num; $i++)
   // {
   // for($k = $num; $k > $i+1; $k-- )
   // {
   // echo " ";
   // }
   // for($j = 0; $j <= $i; $j++ )
   // {
   // echo "* ";
   // }
   // echo "<br/>";
   // }
   // 
   // echo $OUTPUT->footer();
   ?>