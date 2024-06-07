
<?php
require('../../config.php');
require_once($CFG->dirroot."/course/externallib.php");

$COURSEAPI = new core_course_external();
$data =  $COURSEAPI->get_enrolled_courses_by_timeline_classification("all");
echo $OUTPUT->header();
$html ='
 <style>
          .img-frame {
    position: relative;
    display: block;
    width: 100%;
    padding: 0;
    overflow: hidden;
}
.img-frame::before {
    display: block;
    content: "";
}
.img-frame-21by9::before {
    padding-top: 42.8571428571%;
}
.img-frame-16by9::before {
    padding-top: 56.25%;
}
.img-frame-4by3::before {
    padding-top: 75%;
}
.img-frame-1by1::before {
    padding-top: 100%;
}
.img-frame img {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
    -o-object-fit: cover;
    object-fit: cover;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

.head-lime-clamp{
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
    max-height : 50px;
    min-height : 50px;
}

.txt-lime-clamp{
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    overflow: hidden;
}

#myProgress { 
    background : #f2f2f2;
    width : 100%;
    height : 3px;
}

#myBar{
    background : #3986b6;
    height : 100%;
}

.theme-btn,
.theme-btn:hover,
.theme-btn:focus{
    background: #3986b6;
    border: 2px solid #3986b6;
    color: #fff;
    padding: 3px 10px;
    border-radius: 3px;
}
.h-progress{
    min-height: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.custom-border{
    border: 1px solid #e3e3e3;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}
</style> 
<div class="row">';
foreach ($data['courses'] as $course) { 
    $html .= '
        <div class="col-12 col-lg-3 col-md-4 my-3">
            <div class="card">
                <div class="img-frame img-frame-1by1">
                    <img src="'.$course->courseimage.'">
                </div>
                <div class="card-body custom-border">
                    <h4 class="card-title head-lime-clamp">'.$course->fullname.'</h4>
                    <div class="h-progress">
                        <div id="myProgress">
                            <div id="myBar" style="width : '.$course->progress.'%;"></div>
                        </div>
                        <p>'.$course->progress.'%</p>
                    </div>
                    <a href="'.$course->viewurl.'" class="theme-btn">Visualizza</a>
                </div>
            </div>
        </div>
        ';
}
$html .= '

    <script type="text/javascript" src="js/jQuery.js"></script>
    <script type="text/javascript" src="css/slick.js"></script>
    <script type="text/javascript">
    </script>
';

return $html;
echo $OUTPUT->footer(); 
?>