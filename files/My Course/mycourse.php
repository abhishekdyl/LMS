<?php
    require_once("../../config.php");
    global $DB,$CFG;
    use core_completion\progress;
        function coursecolor($courseid) {
            $basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894', '#0984e3', '#b2bec3', '#fdcb6e', '#fd79a8', '#6c5ce7'];
            $color = $basecolors[$courseid % 10];
            return $color;
        }
        function getcourse_image($courseid) {
            global $DB, $CFG;
            require_once($CFG->dirroot. '/course/classes/list_element.php');
            $course = $DB->get_record('course', array('id' => $courseid));
            $course = new core_course_list_element($course);
            foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                if($isimage){
                    $imageurl = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'. $file->get_contextid(). '/'. $file->get_component(). '/'. $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                    return $imageurl;
                }
            }
            if(empty($imageurl)){
                $color = coursecolor($course->id);
                $pattern = new \core_geopattern();
                $pattern->setColor($color);
                $pattern->patternbyid($courseid);
                $classes = 'coursepattern';
                $imageurl = $pattern->datauri();
            }
            return $imageurl;
        }
        // require_once($CFG->dirroot."/course/externallib.php");

        require_once($CFG->dirroot . '/lib/enrollib.php');
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
                    border-radius: 8px;
                }

                .img-frame img:hover{
                    background-color:
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

                #myProgresss { 
                    background : #f2f2f2;
                    width : 100%;
                    height : 3px;
                }

                #myBarr{
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
                .h-progresss{
                    min-height: 50px;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }
                .custom-border{
                    padding: 0px;
                }
                .course-name {
                    display: -webkit-box;
                    -webkit-box-orient: vertical;
                    -webkit-line-clamp: 2;
                    overflow: hidden;
                    max-height: 50px;
                    min-height: 50px;
                    color : #0a0a0a;
                    font-family : arial !important;
                    font-weight : 700 !important;
                }

                #myenrolledcourse .card{
                    padding-left : 15px;
                    padding-right : 15px;
                    border: 1px solid gray;
                }
                .card-body .side-details {
                    font-size: 14px;
                    color: steelblue;
                }
                .pro_side{
                    justify-content : space-between;
                }

                #myenrolledcourse input:hover,
                #sorting:hover,
                #filterd:hover{
                    background-color: #efefef;
                }

                #myenrolledcourse input,
                #sorting,
                #filterd{
                    width: 100%;
                    padding: 7px;
                    border-radius: 5px;
                    border: 1px solid gray;
                    background-color: white;
                }
                @media (min-width: 992px){
                    .custom-course-frame{
                        flex: 0 0 20% !important;
                        max-width: 20% !important; 
                    }
                }
                </style> 

                <div class="container">
                <div class="row" id="myenrolledcourse" >
                <div class="col-12 my-3">
                    <div class="row" id="filters">
                        <div class="col-7">
                            <input type="text" id="searchin" placeholder="Search" name="search" value="" autocomplete="off">
                        </div>
                        <div class="col-2">
                            <select name="filter" class="dropdown" id="filterd">
                                <option value="all">all</option>
                                <option value="inprogress">Inprogress</option>
                                <option value="future">Future</option>
                                <option value="past">Past</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <select name="sort" class="dropdown" id="sorting">
                                <option value="fullname asc">Sort by course name</option>
                                <option value="timeaccess desc">Sort by last access</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="loaddata" data-page="1"></div>
                </div>
                '.'<div class="d-flex align-items-center justify-content-center" id="pagination"></div>'.'
                    </div>

                <script type="text/javascript" src="js/jQuery.js"></script>
                <script type="text/javascript" src="css/slick.js"></script>
                <script type="text/javascript">
                function ajax_call(param="all") {
                    var data  = {
                        page: parseInt($(".loaddata").attr("data-page")),
                        sort: $("#sorting").val(),
                        filter: $("#filterd").val(),
                        search: $("#searchin").val(),
                        param: param,
                    };
                    console.log("mmmmmmmmm",data);
                    var that = this;
                    $.ajax({
                        type:"GET",
                        url:"'.$CFG->wwwroot.'/blocks/utrains_enroll_courselist/ajax.php",
                        data: data,
                        success:function(response){
                            response = JSON.parse(response);
                            $(".loaddata").html(response.html);
                            $("#pagination").html(response.pagination);
                            console.log("response- ", response);
                        }
                    });
                    
                }
                  $(function(){ 

                        console.log("aaaaaaaaaa");
                        $("body").on("click",".loadmoredata",function(){
                            if($(this).hasClass("disable")){
                                return;
                            }
                            var datato = $(this).attr("data-to");
                            var currentpage = parseInt($(".loaddata").attr("data-page"));
                            console.log("currentpage-", currentpage);
                            if(datato == "prev"){
                                currentpage = currentpage-1;
                            } else if(datato == "next"){
                                currentpage = currentpage+1;
                            }
                            console.log("datato-", datato);
                            console.log("currentpage-", currentpage);
                            $(".loaddata").attr("data-page",currentpage);
                            ajax_call();
                        });
                        $("body").on("change",".dropdown",function(){
                            console.log("dddddddddrop");
                            $(".loaddata").attr("data-page", 1);
                            $("#searchin").val("");

                            ajax_call();
                              
                        });
                        $("body").on("keyup","#searchin",function(){
                            $(".loaddata").attr("data-page", 1);
                            console.log("ssssssssss");
                            ajax_call("search");
                              
                        });
                    });

                    ajax_call();

                </script>
                ';

     echo $OUTPUT->header();
     echo $html;   
     echo $totalcount;
     echo $OUTPUT->footer();          