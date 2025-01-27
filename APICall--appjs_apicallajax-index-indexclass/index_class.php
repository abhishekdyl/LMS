<?php
class APIManager {
    private $fixedlangauge = "fr"; 
    private $courselangauge = "fr"; 
    public $userlangauge = "fr"; 
    private $token = ""; 
    private $currentYear = null; 
    public $assigndcomponents = array();
    public $status = 0; 
    public $message = "Error";
    public $msg = "";
    public $data = null;
    public $INSTITUTION = null;
    public $code = 404;
    public $currentschoolyear = 0;
    public $premiumAccount = false;
    public $premiumAccountExpired = false;
    public $currenttime = 0;
    public $premiumAccountExpiry = 0;
    public $remainingDays = 0;
    public $internal = false;
    public $functioncalled = array();
    public $parentstatus = array();
    public $examMode = true;
    private $args = array();
    private $publicflowcourse = array(62);
    private $publicflowdiagnostic = array("62"=>array(5468, 5683));
    private $courseshortnames = array(
        "fr"=>array(
            "math"=>"MATHÉMATIQUES",
            "arabic"=>"ARABE",
            "french"=>"FRANÇAIS",
            "mise"=>"Français - Mathématiques",
            "tarl"=>"Mathématiques"
        ),
        "ar"=> array(
            "math"=>"الرياضيات",
            "arabic"=>"اللغة العربية",
            "french"=>"اللغة الفرنسية",
            "mise"=>"اللغة الفرنسية - الرياضيات",
            "tarl"=>"الرياضيات",
       )
    );
    private $withouttarlusers = array(
        242481, // f3ap1
        242492, // m3ap1
        242395, // ssanae1
        242394, // ssoukaina1
        242372, // ttaoufik1

        242515, //ataghi3  
        242516, //ataghi4
        242520, //ataghi5
        242499, //falami1   
        242502, //gamani1 
        242500, //hakki1 
        242594, //ma1 
        242523, //namarti1 
        242521, //samir1 

        240958, //achahbi1  
        240957, //btaghnaouti1  
        240959, //fyaacoubi1 
        240956, //hrakhoui1 
        240955, //imalaki1 
        241735, //mmrhari8 
        240960, //msalamat1 
        240954, //nfalaki1 
        240962, //nayouch1  
        240963, //oahfir1 
        240961, //stouahri1     


        241128, //ajaytest123353104824 
        242577, //ea1 
        242578, //ea2 
        242579, //ea3 
        242580, //ea4   
        242399, //hhayat1 
        242398, //hhind1 
        242374, //iibtissam1 
        242376, //iisrae2 
        242397, //jjihane1 
        242391, //llaila1   
        242400, //llili2 
        241433, //t@774189545 
        241458, //te_873285436 
        241346, //yyum.comyum.com1  
        242545, //s3ap2  

    );
    // private $overviewvideo='https://portal.fivestudents.com/tokenpluginfile.php/ce744436ea51e435901e6f82d5d2e266/5978/question/questiontext/376372/1/55822/testvider.mp4';
    private $overviewvideo='https://fivestudents.s3.eu-central-1.amazonaws.com/TARL/Videotraillerfinal-V5.mp4';
    public $error = array(
        "code"=> 404,
        "title"=> "Server Error.",
        "message"=> "server under maintenance"
    );
    private $chooseoptions = array("fr"=>"Choisir", "ar"=>"اختر");
    private $skippedcategory = array(1,28);
    function __construct() {
        global $CATGRADE, $INSTITUTION;
        $CATGRADE = 10;
        $this->currenttime = time();
        $this->currentschoolyear = self::get_current_schoolyear();
        $this->code = 400;
        $this->error = array(
            "code"=> 400,
            "title"=> "Server Error..",
            "message"=> "Missing functionality"
        );

    }
    public function set_args($args)
    {
        $this->args=$args;
    }
    public function login($args){
        global $DB, $CFG, $PARENTUSER, $USER;
        $logintype =  $args['logintype'];     
        $username =  $args['username'];     
        $password =  $args['password'];     
        $token =  $args['token'];
        if($logintype == "normal"){
            if(empty($username) || empty($password)){
                $this->sendError("Login Failed", "Username and password is reqired");
            } else {
                if($user = $DB->get_record_sql("SELECT * from {user} u where u.deleted=0 AND u.confirmed = 1 AND u.suspended=0 AND (u.email=? or u.username=?)", array($args['username'], $args['username']))){
                    $DB->delete_records('external_tokens', array('userid' => $user->id));
                    $postDATA = [
                        'username' => $args['username'],
                        'password' => $args['password'],
                        'service'   => 'moodle_mobile_app',
                    ];
                    $ch = curl_init($CFG->wwwroot."/login/token.php");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postDATA);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    $gettoken = curl_exec($ch);
                    if($gettoken = json_decode($gettoken)){
                        if($gettoken->token){
                            self::validatetoken($gettoken->token);
                            $responsedata = new stdClass();
                            $responsedata->token = $gettoken->token;
                            $responsedata->userDetails = $PARENTUSER;
                            $this->sendResponse($responsedata);
                        } else {
                            $this->sendError("Login Failed", "Invalid Credentials");
                        }
                    } else {
                        $this->sendError("Login Failed", "Login Failed");       
                    }
                } else {
                    $this->sendError("Login Failed", "Login Failed");       
                }
            }
        } else if($logintype == "google") {
            if(empty($token)){
                $this->sendError("Login Failed", "Googel Token is required"); 
            } else {
                if($googledata = self::parseJwt($token)){
                    if($user = $DB->get_record('user', array('email' => $googledata->email, "deleted"=>0))){

                    } else {
                        $password = md5(bin2hex(random_bytes(10)));
                        $name = explode(" ", $googleuser->name);
                        $lastname = array_pop($name);
                        $firstname = !empty(implode(" ", $name))?implode(" ", $name):$lastname;

                        $user = new stdClass();
                        $user->username = $googledata->email;
                        $user->firstname = $firstname;
                        $user->lastname = $lastname;
                        $user->email = $googledata->email;
                        $user->confirmed = 1;
                        $user->mnethostid = 1;
                        $user->password = $password;
                        $user->timecreated = time();
                        $user->id = $DB->insert_record('user',$user);
                    }
                    $this->checkuseronWP($token);
                    if(!empty($user->id)){
                        if($token = self::get_usertoken($user->id)){
                            self::validatetoken($token);
                            $responsedata = new stdClass();
                            $responsedata->token = $token;
                            $responsedata->userDetails = $PARENTUSER;
                            $this->sendResponse($responsedata);
                        } else {
                            $this->sendError("Loggin Failed", "Invalid Request"); 
                        }
                    } else {
                        $this->sendError("Unable to get user", "Unable to get user"); 
                    }
                } else {
                    $this->sendError("Loggin Failed", "Invalid Request"); 
                }
            }
        } else {
            $this->sendError("Login Failed", "Invalid login type"); 
        }
    }
    private function checkuseronWP($token){
        global $CFG;
        $data = new stdClass();
        $data->token = $token;
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $CFG->wproot.'/googleaccount.php',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>json_encode($data),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cookie: PHPSESSID=8p1sv8d11h2e3k3oujo2vltb6k'
          ),
        ));
        $response = curl_exec($curl);
    }
    public function getquizsummaryold($args) {
        global $DB, $USER, $CFG;
        $coursemoduleid = $args['wscoursemoduleid'];
        // print_r($args);
        $userid = $USER->id;
        if(empty($coursemoduleid)){
            $this->sendError("Invalid  moduleid", "Invalid  moduleid");
        } else {
            require_once($CFG->dirroot.'/mod/quiz/locallib.php');
            if (!$cm = get_coursemodule_from_id('quiz', $coursemoduleid)) {
                $this->sendError("Invalid  moduleid", "Invalid  moduleid");
                return;
            }
            $query_course_papers = $DB->get_record_sql("select cm.id,q.id as qid, q.name, q.course,intro, q.grademethod from {course_modules} as cm left join {quiz} as q on cm.instance = q.id where cm.id=?", array($coursemoduleid));
            if($query_course_papers) {
                $status = 1;
                $message="Quiz details found.";
                $query_quizgrade = $DB->get_records_sql("select g.* from {quiz_grades} as g where g.quiz=".$query_course_papers->qid." and userid=". $USER->id);
                $allquizgrade = array();
                
                $arr_gradingmethod=array("", "Highest grade", "Average grade","First attempt","Last attempt");
                $final_grade=0;
                $count_grade=0;
                $sum_grade=0;
                $gradecheck=0;
                $got_grade="";
                $gradingmethod=$query_course_papers->grademethod;

                foreach ($query_quizgrade as $key => $value) {
                    $allquizgrade[]=$value;
                    $count_grade++;
                    $gradecheck=1;
                    if($gradingmethod == 1){
                        if($value->grade > $final_grade){
                                $final_grade= $value->grade;
                        }
                    } else if($gradingmethod == 2){
                        $sum_grade += $value->grade;
                        $final_grade = $sum_grade / $count_grade;
                    } else if($gradingmethod == 3){
                        $final_grade = $value->grade;
                        return false; 
                    } else if($gradingmethod == 4){
                        $final_grade = $value->grade;
                    } 
                }
                if(!$gradecheck){
                    $final_grade="Not yet graded";
                }
                $query_course_papers->final_grade=$final_grade;
                $gradingmethodname="Grading method: ".$arr_gradingmethod[$gradingmethod];
                $query_course_papers->gradingmethodname=$gradingmethodname;
                $query_course_papers->intro=strip_tags($query_course_papers->intro);

                $qry_quizsummery="select qa.id as 'attemptid', q.id as 'quizid', qa.attempt as 'attemptno', qa.state as 'quizstate', DATE_FORMAT(FROM_UNIXTIME(qa.timefinish), '%W, %d %M %Y %h:%k %p') as 'finishedtime', qa.sumgrades as 'grade', qg.grade as 'totalgarde', q.name as 'papername',q.sumgrades as cgrade, q.grade as dgrade from {course_modules} as cm LEFT JOIN {quiz_attempts} as qa on cm.instance = qa.quiz left join {quiz} as q on cm.instance = q.id LEFT JOIN {quiz_grades} as qg ON qa.quiz =qg.quiz where qa.userid=$userid and cm.id=$coursemoduleid order by qa.id";
                $query_quiz_summery = $DB->get_records_sql($qry_quizsummery);
                
                $quiz_summery_data = array();
                $btn_startquiz="Attempt Quiz";
                foreach($query_quiz_summery as $rs_quiz_summery)
                {
                    $btn_startquiz="Re-Attempt Quiz";
                    $rs_quiz_summery->user=$userid;
                    $quiz_summery_data[] =  $rs_quiz_summery;
                    if(!strcmp($rs_quiz_summery->quizstate,"inprogress")){
                        $btn_startquiz="Continue the last attempt";
                    }
                }
                $query_course_papers->btn_startquiz=$btn_startquiz;
                $query_course_papers->summary=$quiz_summery_data;
                $query_course_papers->allgrade=$allquizgrade;
                $this->sendResponse($query_course_papers);
            } else {
                $this->sendError("Invalid  moduleid", "Invalid  moduleid");
            }         
            // print_r($query_course_papers);
        }
    } 
    public function quizattempt($args) {
        global $DB, $USER, $CFG;
        // print_r($args);
        require_once($CFG->dirroot . '/mod/quiz/classes/external.php');
        $quizEx = new mod_quiz_external();
        $coursemoduleid = $args['wscoursemoduleid'];
        if(empty($coursemoduleid)){
            $this->sendError("Invalid  moduleid", "Invalid  moduleid");
        } else {
            require_once($CFG->dirroot.'/mod/quiz/locallib.php');
            if (!$cm = get_coursemodule_from_id('quiz', $coursemoduleid)) {
                $this->sendError("Invalid  moduleid", "Invalid  moduleid");
                return;
            }
            $attempts = quiz_get_user_attempts($cm->instance, $USER->id, 'unfinished', true);
            if(sizeof($attempts) > 0){
                $this->sendResponse(array_pop($attempts));
            } else {
                $newattempt = $quizEx->start_attempt($cm->instance);
                $this->sendResponse($newattempt);
            }
        }
    }
    public function fetchquizquestion($args) {
        global $DB, $USER, $CFG, $wstoken;
        $baseurl = "/webservice/pluginfile.php";
        require_once($CFG->dirroot.'/repository/lib.php'); 
        $uniqueid = $args['uniqueid'];
        if(empty($uniqueid)){
            $this->sendError("Invalid  uniqueid", "Invalid  uniqueid");
        } else {
            $qry="select q.id as 'qid',qa.id as 'id',q.name as 'name', q.questiontext as 'questiontext', q.qtype as 'qtype', qa.slot as 'slot' from {question_attempts} as qa left join {question} as q on qa.questionid = q.id where qa.questionusageid=?";
            $query_fetch_question = $DB->get_records_sql($qry, array($uniqueid));
            // print_r($query_fetch_question);
            $fetch_question_data = array();
            $qtype=""; 
            $questioncheck=0;
            foreach($query_fetch_question as $rs_fetch_question)
            {
                $questioncheck = 1;
                $qtype=$rs_fetch_question->qtype;
                $question_id=$rs_fetch_question->qid;
                $slot=$rs_fetch_question->slot;
                if($qtype=="truefalse")
                {
                    $query_fetch_options = $DB->get_records_sql("select an.id , an.answer as 'option' , an.answerformat , an.fraction as 'answerfraction' ,CASE WHEN an.fraction =0 THEN 'incorrect' WHEN an.fraction =1 THEN 'correct' END AS 'answer' from {question_attempts} as qa left join {question} as q on qa.questionid = q.id left join {question_answers} as an on q.id = an.question where qa.questionusageid=$uniqueid and qa.slot=$slot");
                    $allanswer=array();
                    $optionValue_Arr=array("False","True");
                    $optionValue = 0;
                     foreach($query_fetch_options as $rs_fetch_options)
                     {
                        $rs_fetch_options->value = array_search($rs_fetch_options->option,$optionValue_Arr);
                        $allanswer[]=$rs_fetch_options;
                     }
                    $rs_fetch_question->allanswer=$allanswer;
                } elseif($qtype=="multichoice")
                {
                    $query_fetch_optstep=$DB->get_records_sql("SELECT qasd.value FROM `{question_attempts}`as qa LEFT JOIN {question_attempt_steps} as qas on qa.id = qas.questionattemptid LEFT JOIN {question_attempt_step_data} as qasd on qasd.attemptstepid = qas.id WHERE qa.questionusageid = $uniqueid and `slot` = $slot and qasd.name='_order'");
                    $answerseq="";
                    foreach($query_fetch_optstep as $rs_fetch_optstep)
                     {
                        $answerseq=$rs_fetch_optstep->value;
                     }
                     $answerseqdata=explode(",",$answerseq);
                     $answerseqdataArr=array();
                     $optionValue = 0;
                     foreach ($answerseqdata as $key => $value) {
                        $query_fetch_options = $DB->get_record_sql("select an.id , an.answer as 'option' , an.answerformat , an.fraction as 'answerfraction' ,CASE WHEN an.fraction =0 THEN 'incorrect' WHEN an.fraction =1 THEN 'correct' END AS 'answer' from {question_answers} as an  where an.id=".$value);
                        $query_fetch_options->value=$optionValue;
                        $answerseqdataArr[] = $query_fetch_options;
                        $optionValue++;
                     }
                    // $rs_fetch_question->answerseq=explode(",",$answerseq);
                    $rs_fetch_question->allanswer=$answerseqdataArr;
                } else if($qtype=="match")
                {
                    unset($rs_fetch_question->allanswer);
                    $query_fetch_choiceorder=$DB->get_records_sql("SELECT qasd.value FROM `{question_attempts}`as qa LEFT JOIN {question_attempt_steps} as qas on qa.id = qas.questionattemptid LEFT JOIN {question_attempt_step_data} as qasd on qasd.attemptstepid = qas.id  WHERE qa.questionusageid = $uniqueid and `slot` = $slot and qasd.name='_choiceorder'");
                    $choiceorder="";
                    foreach($query_fetch_choiceorder as $rs_fetch_choiceorder)
                     {
                        $choiceorder=$rs_fetch_choiceorder->value;
                     }
                    $query_fetch_stemorder=$DB->get_records_sql("SELECT qasd.value FROM `{question_attempts}`as qa LEFT JOIN {question_attempt_steps} as qas on qa.id = qas.questionattemptid LEFT JOIN {question_attempt_step_data} as qasd on qasd.attemptstepid = qas.id WHERE qa.questionusageid = $uniqueid and `slot` = $slot and qasd.name='_stemorder'");
                    $stemorder="";
                    foreach($query_fetch_stemorder as $rs_fetch_stemorder)
                     {
                        $stemorder=$rs_fetch_stemorder->value;
                     }
                     $choiceorderData = explode(",",$choiceorder);
                     // print_r($choiceorderData);
                     $choiceorderDataArr= array();
                     $ans_data = new stdClass();
                     $optionValue = 0;
                        $ans_data->id="0";
                        $ans_data->answertext="Choisir…";
                        $ans_data->value=$optionValue;
                        $choiceorderDataArr[] = $ans_data;
                     foreach ($choiceorderData as $key => $value) {
                        $optionValue ++;
                        $qry_matchoption = $DB->get_record_sql("SELECT * from  {qtype_match_subquestions} as qms where qms.id=".$value);
                        $ans_data = new stdClass();
                        $ans_data->id=$qry_matchoption->id;
                        $ans_data->answertext=$qry_matchoption->answertext;
                        $ans_data->value=$optionValue;
                        $choiceorderDataArr[] = $ans_data;
                     }

                     $stemorderData = explode(",",$stemorder);
                     // print_r($stemorderData);
                     $stemorderDataArr= array();
                     $match_o_value=0;
                     foreach ($stemorderData as $key => $value) {
                        $qry_matchoption = $DB->get_records_sql("SELECT  qmsq.id as qmsqid, qa.questionusageid as quesattemptid,qc.contextid as contextid,qmsq.id as id, qmsq.questionid as questionid, qmsq.questiontext as questiontext, qmsq.questiontextformat as questiontextformat, qmsq.answertext as answertext  FROM {qtype_match_subquestions} as qmsq  INNER JOIN {question} as q ON qmsq.questionid=q.id  INNER JOIN {question_categories} as qc ON qc.id=q.category  INNER JOIN {question_attempts} as qa ON qa.questionid=q.id where qmsq.id=".$value);
                        $stemoption = "";
                        foreach ($qry_matchoption as $key => $value) {
                            $questions=$value->questiontext; //@@PUGLINFILE@@
                            $quesattemptid = $value->quesattemptid;
                            $qtypecontext = $value->contextid;
                            $qmsqid = $value->qmsqid;
                            $questionfile=$DB->get_records_sql("SELECT * FROM  {files}  WHERE contextid=$qtypecontext ");
                            foreach($questionfile as $image){
                                $file='pluginfile.php';
                                $cntxid=$image->contextid;
                                $component=$image->component;
                                $filearea=$image->filearea;
                                $itemid=$image->itemid;
                                $filepath=$image->filepath;
                                $filename=$image->filename;
                            }
                            // $value->questiontext1=file_rewrite_pluginfile_urls($questions,$file,$cntxid,$component,$filearea,$quesattemptid.'/1/'.$qmsqid);
                            // $fileurl = $CFG->wwwroot."/".$file."/".$cntxid."/".$component."/".$filearea."/".$quesattemptid.'/'.$slot.'/'.$qmsqid;
                            // $fileurl = htmlcontenturl_login($token, $fileurl);
                            // $decodedqtext=replacepluginURL($questions,$fileurl);
                            // $value->questiontext=$decodedqtext;
                            $value->value = "sub".$match_o_value;
                            $match_o_value++;
                            $stemoption=$value;
                        }


                        
                        $stemorderDataArr[] = $stemoption;
                     }

                    $rs_fetch_question->choiceorder=$choiceorderDataArr;
                    $rs_fetch_question->stemorder=$stemorderDataArr;
                } else if($qtype=="ddimageortext"){
                    $alldrops = $DB->get_records("qtype_ddimageortext_drops", array("questionid"=>$rs_fetch_question->qid));
                    $rs_fetch_question->alldrops = array_values($alldrops);


                    // $choiceorder=$DB->get_field_sql("SELECT qasd.value FROM `{question_attempts}`as qa LEFT JOIN {question_attempt_steps} as qas on qa.id = qas.questionattemptid LEFT JOIN {question_attempt_step_data} as qasd on qasd.attemptstepid = qas.id  WHERE qa.questionusageid = $uniqueid and `slot` = $slot and qasd.name='_choiceorder1' and qa.id=?", array($rs_fetch_question->id));
                    // if($choiceorder){
                    //     foreach (explode(",", $choiceorder) as $value) {
                    //         $found_key = array_search($value, array_column($people, 'fav_color'));
                    //     }
                    // }
                    // $rs_fetch_question->alldrops = array_values($alldrops);


                    
                    $alldrags = $DB->get_records("qtype_ddimageortext_drags", array("questionid"=>$rs_fetch_question->qid));
                    foreach ($alldrags as $key => $drag) {
                        if($file = $DB->get_record_sql("select * from {files} where component = ? and filearea = ? and itemid = ? and filename != '.'", array("qtype_ddimageortext", "dragimage", $drag->id))){
                            $fileurl = file_encode_url($CFG->wwwroot . $baseurl, '/'.$file->contextid.'/'.$file->component.'/'.$file->filearea.'/'.$uniqueid.'/'.$rs_fetch_question->slot.'/'.$file->itemid.'/'.$file->filepath.$file->filename, false)."?token=".$wstoken;
                            $file->fileurl = $fileurl;
                            unset($file->contenthash);
                            unset($file->pathnamehash);
                            $alldrags[$key]->file = $file;
                        }
                    }
                    $rs_fetch_question->alldrags = array_values($alldrags);
                } else if($qtype=="multianswer"){
                    echo "string1";
                    $qry_subquestion="select q.id as 'id', q.name as 'name', q.questiontext as 'questiontext', q.qtype as 'qtype' from {question} as q where q.parent=?";
                    $rs_subquestion = $DB->get_records_sql($qry_subquestion, array($rs_fetch_question->qid));
                    $allsubquestions = array();
                    foreach ($rs_subquestion as $key => $subquestion) {
                        array_push($allsubquestions, $subquestion);
                    }
                    $rs_fetch_question->subquestion = $allsubquestions;
                }
                $fetch_question_data[] =  $rs_fetch_question;

            }
            if($questioncheck){
                $this->sendResponse($fetch_question_data);
            } else {
                $this->sendError("Question Not Found", "Question Not Found");
            }

        }
    }
    private function getfileurl($file){
        global $DB, $USER, $CFG, $wstoken;
        $baseurl = "/webservice/pluginfile.php";
        if(is_object($file)){
            return $file;
        }
        return null;
    }
    public function attemptquestion($args){
        global $DB, $USER, $CFG;
        $wsquestionatmpid = $args['wsquestionatmpid'];
        $wsanswer_data = $args['wsanswer_data'];
        $skipanswercheck = $args['skipanswercheck'];
        $this->skipanswercheck = $skipanswercheck;
        if((empty($wsquestionatmpid) || $wsanswer_data == "") && $skipanswercheck != 1){
            $this->sendError("Invalid  moduleid", "Invalid  moduleid");
        } else {
            $time=time();
            $qry="select max(id) as id,max(sequencenumber) as 'seq' FROM {question_attempt_steps} where questionattemptid=$wsquestionatmpid";
            $query_fetch_seq = $DB->get_records_sql($qry);
            $qas_seq=0;
            $qas_id="";
            foreach($query_fetch_seq as $rs_fetch_seq)
            {
                $qas_seq=$rs_fetch_seq->seq;
                $qas_id=$rs_fetch_seq->id;
            }
            $get_qtype = $DB->get_record_sql("SELECT q.* FROM  {question_attempts} AS qsa INNER JOIN {question} AS q ON qsa.`questionid` = q.id WHERE qsa.id =".$wsquestionatmpid);
            if(!empty($get_qtype))
            {
                $question_type = $get_qtype->qtype;
                if($question_type == "match"){
                    if(is_array($wsanswer_data) && sizeof($wsanswer_data) > 0){
                        $qasd_val=array();
                        if($qas_seq !="")
                        {
                            $query_fetch_seq_val = $DB->get_records_sql("SELECT * FROM {question_attempt_step_data} WHERE attemptstepid=$qas_id order by id");
                            foreach($query_fetch_seq_val as $rs_fetch_seq_val)
                            {
                                $qasd_val[]=$rs_fetch_seq_val->value;
                            }
                            $ans_optioncheck = 0;
                            $atmp_state = "complete";
                            foreach ($wsanswer_data as $key => $answer_data) {
                                if($answer_data['value'] == 0){
                                    $atmp_state="invalid";
                                }
                            }
                            if(sizeof($qasd_val) != sizeof($wsanswer_data)){
                                    $atmp_state="invalid";
                            }
                            $qas_seq++;
                            $rec_insert= new stdClass();
                            $rec_insert->questionattemptid= $wsquestionatmpid;
                            $rec_insert->sequencenumber= $qas_seq;
                            $rec_insert->state= $atmp_state;
                            $rec_insert->timecreated= $time;
                            $rec_insert->userid= $userid;
                            $q_a_steps = $DB->insert_record('question_attempt_steps', $rec_insert, true);
                            foreach ($wsanswer_data as $key => $answer_data) {
                                if($answer_data['key'] == "" || $answer_data['value'] == ""): continue; endif;
                                $rec_insert1= new stdClass();
                                $rec_insert1->attemptstepid= $q_a_steps;
                                $rec_insert1->name= $answer_data['key'];
                                $rec_insert1->value= $answer_data['value'];
                                $q_a_s_data = $DB->insert_record('question_attempt_step_data', $rec_insert1, true);
                            }
                            $status = 1;
                            $message = 'Answer updated';
                        }
                    } else {
                        $status = 0;
                        $message = "Not sufficient answerdata";
                    }
                } else if($question_type == "truefalse" || $question_type == "multichoice" || $question_type == "shortanswer"){
                    $qasd_val="";
                    if($qas_seq !="")
                    {
                        $query_fetch_seq_val = $DB->get_records_sql("SELECT value FROM {question_attempt_step_data} WHERE attemptstepid=$qas_id and name='answer'");
                        foreach($query_fetch_seq_val as $rs_fetch_seq_val)
                        {
                            $qasd_val=$rs_fetch_seq_val->value;
                        }
                    }                   
                    if($qasd_val != $wsanswer_data)
                    {
                        $qas_seq++;
                        $rec_insert= new stdClass();
                        $rec_insert->questionattemptid= $wsquestionatmpid;
                        $rec_insert->sequencenumber= $qas_seq;
                        $rec_insert->state= 'complete';
                        $rec_insert->timecreated= $time;
                        $rec_insert->userid= $userid;
                        $q_a_steps = $DB->insert_record('question_attempt_steps', $rec_insert, true);
                        $rec_insert1= new stdClass();
                        $rec_insert1->attemptstepid= $q_a_steps;
                        $rec_insert1->name= 'answer';
                        $rec_insert1->value= $wsanswer_data;
                        $q_a_s_data = $DB->insert_record('question_attempt_step_data', $rec_insert1, true);
                        $status = 1;
                        $message = 'Answer updated';
                    } else {
                        $status = 1;
                        $message = 'Answer not updated';
                    }
                }
                if($status){
                    $this->sendResponse($message);
                } else {
                    $this->sendError($message, $message);
                }
            } else {
                $message = 'Invalid Question type';
                $this->sendError($message, $message);
            }
        }
    }
    private function sendResponse($data) {
        $this->status = 1;
        $this->message = "Success";
        $this->data = $data;
        $this->code = 200;
        $this->error = null;
    }
    public function sendError($title, $message, $code=400, $data=null) {
        $this->status = 0;
        $this->message = "Error";
        $this->data = null;
        $this->code = $code;
        $this->error = array(
            "code"=> $code,
            "title"=> $title,
            "message"=> $message,
            "data"=> $data
        );
    }
    public function validatetoken($token){
        global $DB, $USER, $PARENTUSER;
        if(empty($token)){
            $this->sendError("Missing User Token", "Missing User Token", 100);
        } else if($query = $DB->get_record('external_tokens', array('token' => $token))){
            $this->token = $token;
            $user = $DB->get_record("user", array("id"=>$query->userid));
            unset($user->password);
            if($user->deleted || $user->suspanded || !$user->confirmed){
                $this->sendError("Invalid Token", "Invalid Token", 100);
            } else {
                if(in_array($user->id, $this->withouttarlusers)){
                    $this->publicflowcourse = array();
                    $this->publicflowdiagnostic = array();
                }
                $USER = $user;
                $USER->ignoresesskey = true;
                self::fetchparentdetails($user->id);
                return true;
            }
        } else {
            $this->sendError("Invalid Token", "Invalid Token", 100);
        }
        return false;
    }
    public function fetchparentdetails($userid = 0){
        global $DB, $USER, $PARENTUSER, $PAGE, $INSTITUTION;
        $child = null;
        if(empty($userid)){
            $userid = $USER->id;
        }

        $PARENTUSER->imageUrl = "";
        if($child = $DB->get_record("childusers", array("userid"=>$userid))){
            if($this->isSchoolPayMember($userid)){
                $this->premiumAccount = true;   
                $this->premiumAccountExpiry = 0;
                $this->remainingDays = 0;
                // $subscriptions =  $DB->get_record_sql("SELECT ps.* FROM mdl_plussubscription ps INNER JOIN mdl_childusers cu on cu.userid = ps.userid and cu.grade = ps.grade WHERE ps.userid = :userid AND ps.schoolyear = :schoolyear AND ps.paidamount", array("userid"=>$userid, "schoolyear"=>$this->currentschoolyear, "status"=>1));
                // if($subscriptions->enddate < time()){
                //     $this->premiumAccount = false;   
                //     $this->premiumAccountExpiry = $subscriptions->enddate;
                //     $diff=date_diff(date_create(date("Y-m-d", $this->premiumAccountExpiry)),date_create(date("Y-m-d")));
                //     $this->remainingDays = $diff->format("%a");
                // }
            } else {
                $subscriptions =  $DB->get_record_sql("SELECT ps.* FROM mdl_plussubscription ps INNER JOIN mdl_childusers cu on cu.userid = ps.userid and cu.grade = ps.grade WHERE ps.userid = :userid AND ps.schoolyear = :schoolyear AND ps.paidamount", array("userid"=>$userid, "schoolyear"=>$this->currentschoolyear, "status"=>1));
                if($child->usertype == 3){
                    $this->premiumAccount = true;   
                    $this->premiumAccountExpiry = 0;
                    $this->remainingDays = 0;
                } else if($subscriptions){
                    $this->premiumAccount = true;   
                    $this->premiumAccountExpiry = 0;
                    $this->remainingDays = 0;
                    if($subscriptions->totalamount != $subscriptions->paidamount){
                        if($subscriptions->enddate < time()){
                            $this->premiumAccount = false;
                            $this->premiumAccountExpired = true;   
                        }
                        $this->premiumAccountExpiry = $subscriptions->enddate;
                        $diff=date_diff(date_create(date("Y-m-d", $this->premiumAccountExpiry)),date_create(date("Y-m-d")));
                        $this->remainingDays = $diff->format("%a");
                    }
                }
            }
            // $this->premiumAccount = $DB->record_exists_sql("SELECT igm.* from mdl_institution_group_member igm INNER JOIN mdl_institution_group ig on ig.id= igm.groupid and ig.categoryid=:grade WHERE igm.schoolyear=:schoolyear AND igm.userid = :userid and igm.status=:status", array("grade"=>$child->grade ,"schoolyear"=>$this->currentschoolyear, "userid"=>$userid, "status"=>1));
            // if($this->premiumAccount){
            //     $this->premiumAccountExpiry = strtotime("+20 days");
            //     $this->remainingDays = 20;
            // }

            $parent = $DB->get_record("user", array("id"=>$child->parentid));
            if($parent){
                $userpicture = new user_picture(core_user::get_user($child->parentid));
                $userpicture->size = 150; // Size f1.
                $PARENTUSER->imageUrl = $userpicture->get_url($PAGE)->out(false);
            }
            $PARENTUSER->id = $parent->id;
            $PARENTUSER->parentFirstName = $parent->firstname;
            $PARENTUSER->parentLastName = $parent->lastname;
            $PARENTUSER->emailId = $parent->email;
        } else {
            $userpicture = new user_picture(core_user::get_user($USER->id));
            $userpicture->size = 150; // Size f1.
            $PARENTUSER->id = $USER->id;
            $PARENTUSER->parentFirstName = $USER->firstname;
            $PARENTUSER->parentLastName = $USER->lastname;
            $PARENTUSER->emailId = $USER->email;
            $PARENTUSER->imageUrl = $userpicture->get_url($PAGE)->out(false);
        }
        $PARENTUSER->subscriptions = self::get_subscriptions();
        $PARENTUSER->children = self::get_children();
        $PARENTUSER->currentChild = null;
        if(!empty($child)){
            $childid = $child->userid;
            $currentChild = array_filter($PARENTUSER->children, function($v, $k)  use ($childid) {
                // global $childid;
                return $v->id == $childid;
            }, ARRAY_FILTER_USE_BOTH);
            if(sizeof($currentChild)){
                $PARENTUSER->currentChild = array_pop($currentChild);
            }
        }
        if($PARENTUSER->currentChild){
            if($institution = $DB->get_record_sql("SELECT i.* FROM mdl_institution_group_member igm INNER JOIN mdl_institutions i on i.id=igm.institutionid INNER JOIN mdl_institution_group ig on ig.id=igm.groupid WHERE ig.categoryid=:grade AND igm.schoolyear=:currentschoolyear AND igm.userid=:userid ", array("grade"=>$PARENTUSER->currentChild->grade, "currentschoolyear"=>$this->currentschoolyear, "userid"=>$PARENTUSER->currentChild->id))){
                if(!empty($institution->disablecourses)){
                    $institution->disablecourses = (array)unserialize($institution->disablecourses);
                } else {
                    $institution->disablecourses = new stdClass();
                }
                $INSTITUTION = $institution;
                $this->INSTITUTION = $institution;
            }

            $this->missionMode = $PARENTUSER->currentChild->missionMode;
            $this->examMode = true;
        }
        return $PARENTUSER;
    }
    public function get_subscriptions($userid = 0){
        global $DB, $USER, $PARENTUSER;
        $allsubscription = array();
        $allsubscriptions = $DB->get_recordset_sql("select c.*, s.aname as subsname, cc.name as catname, cc.id as categoryid from {custom_enrol_details} c inner join mdl_setting s on c.subscription_id=s.id inner join {course_categories} cc on cc.id = c.course_cat_id where c.user_id=? and c.enrolment_date_to > ? and c.free = 0", array($PARENTUSER->id, time()));
        foreach ($allsubscriptions as $subscription) {
            $subscriptiondata = new stdClass();
            $subscriptiondata->id = intval($subscription->id);
            $subscriptiondata->categoryId = intval($subscription->categoryid);
            $subscriptiondata->categoryName = $subscription->catname;
            $subscriptiondata->subscriptionName = $subscription->subsname;
            $subscriptiondata->startDate = intval($subscription->enrolment_date_from);
            $subscriptiondata->endDate = intval($subscription->enrolment_date_to);
            $subscriptiondata->assignedTo = intval($subscription->assignedto);
            $subscriptiondata->assignedDate = intval($subscription->assigneddate);
            $subscriptiondata->free = intval($subscription->free);
            array_push($allsubscription, $subscriptiondata);

        }
        return $allsubscription;
    }
    public function get_children($userid = 0){
        global $DB, $USER, $PARENTUSER, $PAGE, $childid;
        $children = array();
        $allchild = $DB->get_recordset_sql("SELECT u.*, c.parentid, c.subscriptionid, c.grade, c.id as childid, c.image, c.portraitimage, c.region, c.provinces, c.referralcode, xp.referralcoin, c.usertype FROM {childusers} c INNER JOIN {user} u on u.id = c.userid and u.deleted=0 left join {xpsetting} xp on xp.gradeid = c.grade  where c.parentid = ? AND c.deleted=0", array($PARENTUSER->id));
        foreach ($allchild as $child) {
            $childdata = new stdClass();
            $childid = intval($child->id);
            $childdata->id = intval($child->id);
            $childdata->childId = intval($child->childid);
            $childdata->username = $child->username;
            $childdata->firstName = $child->firstname;
            $childdata->lastName = $child->lastname;
            $childdata->emailId = $child->email;
            $childdata->region = $child->region;
            $childdata->provinces = $child->provinces;
            $childdata->usertype = $child->usertype;
            $childdata->premiumAccount = false;
            $childdata->premiumAccountExpiry = 0;
            $childdata->remainingDays = 0;
            // if($subs = $this->get_curreentsubscription($child->id, $child->grade)){
            //     $childdata->premiumAccount = true;
            //     $childdata->premiumAccountExpiry = $subs->enddate;
            //     $childdata->remainingDays = $this->date_diff($subs->enddate, time());
            // }
            // $childdata->premiumAccount = $DB->record_exists("institution_group_member", array("schoolyear"=>$this->currentschoolyear, "userid"=>$child->id, "status"=>1));
            // if($childdata->premiumAccount){
            //     $childdata->remainingDays = 20;
            //     $childdata->premiumAccountExpiry = strtotime("+20 days");
            // }
            $childdata->disablecourses = new stdClass();
            $childdata->institutionid = 0;
            if($institution = $DB->get_record_sql("SELECT i.* FROM mdl_institution_group_member igm inner join mdl_institutions i on i.id=igm.institutionid where igm.schoolyear=? and igm.status=1 and igm.userid=?", array($this->currentschoolyear, $child->id))){
                if(!empty($institution->disablecourses)){
                    $childdata->disablecourses = (object)unserialize($institution->disablecourses);
                }
                $updatechild = new stdClass();
                $updatechild->id = $child->childid;
                $childdata->institutionid = $institution->id;
                $requpdatechild = false;
                if(empty($childdata->region)){
                    $requpdatechild = true;
                    $updatechild->region = $institution->region;
                    $childdata->region = $institution->region;
                }
                if(empty($childdata->provinces)){
                    $requpdatechild = true;
                    $updatechild->provinces = $institution->provinces;
                    $childdata->provinces = $institution->provinces;
                }
                if($requpdatechild){
                    $DB->update_record("childusers",$updatechild);
                }
            }
            if($this->isSchoolPayMember($child->id)){
                $childdata->premiumAccount = true;   
                $childdata->premiumAccountExpiry = 0;
                $childdata->remainingDays = 0;
                // $subscriptions =  $DB->get_record_sql("SELECT ps.* FROM mdl_plussubscription ps INNER JOIN mdl_childusers cu on cu.userid = ps.userid and cu.grade = ps.grade WHERE ps.userid = :userid AND ps.schoolyear = :schoolyear AND ps.paidamount > 0", array("userid"=>$child->id, "schoolyear"=>$this->currentschoolyear, "status"=>1));
                // if($subscriptions->enddate < time()){
                //     $childdata->premiumAccount = false;   
                //     $childdata->premiumAccountExpiry = $subscriptions->enddate;
                //     $diff=date_diff(date_create(date("Y-m-d", $this->premiumAccountExpiry)),date_create(date("Y-m-d")));
                //     $childdata->remainingDays = $diff->format("%a");
                // }
            } else {
                $subscriptions =  $DB->get_record_sql("SELECT ps.* FROM mdl_plussubscription ps INNER JOIN mdl_childusers cu on cu.userid = ps.userid and cu.grade = ps.grade WHERE ps.userid = :userid AND ps.schoolyear = :schoolyear AND ps.paidamount > 0", array("userid"=>$child->id, "schoolyear"=>$this->currentschoolyear, "status"=>1));
                if($child->usertype == 3){
                    $childdata->premiumAccount = true;   
                    $childdata->premiumAccountExpiry = 0;
                    $childdata->remainingDays = 0;
                } else if($subscriptions){
                    $childdata->premiumAccount = true;   
                    $childdata->premiumAccountExpiry = 0;
                    $childdata->remainingDays = 0;
                    if($subscriptions->totalamount != $subscriptions->paidamount){
                        if($subscriptions->enddate < time()){
                            $childdata->premiumAccount = false;
                            $childdata->premiumAccountExpired = true;   
                        }
                        $childdata->premiumAccountExpiry = $subscriptions->enddate;
                        $diff=date_diff(date_create(date("Y-m-d", $childdata->premiumAccountExpiry)),date_create(date("Y-m-d")));
                        $childdata->remainingDays = $diff->format("%a");
                    }
                }
            }
            

            $childdata->referralBonus = (!empty($child->referralcoin)?intval($child->referralcoin):100);
            $childdata->referralConsumed = true;
            $childdata->referedUser = "";
            // if($referedfrom = $DB->get_record_sql("select wh.*, u.alternatename from mdl_userwallethistory wh inner join mdl_user u on u.id = wh.fromuser where wh.type='referedfrom' and wh.userid = ? ", array($child->id))){
            //     $childdata->referralConsumed = true;
            //     $childdata->referedUser = $referedfrom->alternatename;
            // }
            $childdata->charName = $child->alternatename;
            $childdata->grade = intval($child->grade);    
            $childdata->internal = $DB->record_exists_sql("SELECT ue.*, e.enrol, c.category from mdl_user_enrolments ue INNER JOIN mdl_enrol e on e.id=ue.enrolid INNER JOIN mdl_course c on c.id=e.courseid WHERE ue.userid = ? and e.enrol != 'self' and c.category = ?", array($childdata->id, $childdata->grade));    
            $childdata->charImage = $child->image;
            $childdata->referralCode = $child->referralcode;
            $childdata->portraitImage = self::getmyportraitimages($child);
             $userpicture = new user_picture(core_user::get_user($child->id));
            $userpicture->size = 150; // Size f1.
            $childdata->characterImageUrl = $child->image;
            $childdata->currentSubscription = null;
            $childdata->examMode = true;
            $childdata->missionMode = $DB->record_exists("institution_group_member", array("status"=>1, "userid"=>$child->id, "schoolyear"=>$this->currentschoolyear));
            // $mypacks = array_filter($PARENTUSER->subscriptions, function($v, $k) {
            //     global $childid;
            //     return $v->assignedTo == $childid;
            // }, ARRAY_FILTER_USE_BOTH);
            // if(!empty($mypacks)){
            //     $childdata->currentSubscription = array_pop($mypacks);
            // }
            array_push($children, $childdata);
        }
        return $children;
    }
    public function getmyportraitimages($child){
        global $DB, $CFG;
        // $sql="SELECT p.id,p.name,pg.name AS potraitgroupname,p.ratio,up.completed,pg.gender,pg.background_color FROM {potrait} p JOIN {userpotrait} up ON p.id=up.potraitid JOIN {potraitgroup} pg ON pg.id=p.group_id WHERE up.userid=? and p.id = ?";
        // if($list=$DB->get_record_sql($sql,array($child->id, $child->portraitimage))){
        $sql="SELECT p.id,p.name,pg.name AS potraitgroupname,p.ratio,'1' as completed,pg.gender,pg.background_color 

        FROM mdl_potrait p 
        LEFT JOIN mdl_potraitgroup pg ON pg.id=p.group_id WHERE p.id = ?";
        // echo $sql;
        // print_r(array($child->id, $child->portraitimage));
        if($list=$DB->get_record_sql($sql,array($child->portraitimage))){
            $list->backgroundimage=self::get_deginersfiles("potrait", "backgroundimage", $list->id);
            $list->backgroundimagesmall=$list->backgroundimage;
            $list->backgroundimagemedium=$list->backgroundimage;
            $list->backgroundimagelarge=$list->backgroundimage;
            // $list->backgroundimagesmall=self::get_deginersfiles("potrait", "backgroundimagesmall", $list->id);
            // $list->backgroundimagemedium=self::get_deginersfiles("potrait", "backgroundimagemedium", $list->id);
            // $list->backgroundimagelarge=self::get_deginersfiles("potrait", "backgroundimagelarge", $list->id);
        } else {
            $list = null;
        }
        return $list;
    }
    public function validatelanguage($language){
        global $lang;
        $alllangs = get_string_manager()->get_list_of_translations();
        if(array_key_exists($language, $alllangs)){
            $lang = $language;
        }
    }
    public function get_My_Subscription(){
        global $DB, $CFG, $USER;
        $allsubscriptions = $DB->get_record_sql(" select c.*, s.aname as subsname, cc.name as catname, cc.id as categoryid from {custom_enrol_details} c inner join mdl_setting s on c.subscription_id=s.id inner join {course_categories} cc on cc.id = c.course_cat_id where c.assignedto=?", array($USER->id));
        if($allsubscriptions){
            $subscriptiondata = new stdClass();
            $subscriptiondata->id = $subscription->id;
            $subscriptiondata->categoryId = $subscription->categoryid;
            $subscriptiondata->categoryName = $subscription->catname;
            $subscriptiondata->subscriptionName = $subscription->subsname;
            $subscriptiondata->startDate = $subscription->enrolment_date_from;
            $subscriptiondata->endDate = $subscription->enrolment_date_to;
            $subscriptiondata->assignedTo = $subscription->assignedto;
            $subscriptiondata->assignedDate = $subscription->assigneddate;
            $subscriptiondata->free = $subscription->free;
        }
        return $subscriptiondata;
    }
    public function getGradeData($args){
        global $DB, $CFG, $USER, $PARENTUSER, $XPSETTING, $ISINTERNAL;
        $this->PARENTUSER = $PARENTUSER;
        if(!empty($PARENTUSER->currentChild)){
            $currentChild = $PARENTUSER->currentChild;
            $gradeid = $currentChild->grade;
            $message = array();
            $userlang = $args['lang'];
            $alllangs = get_string_manager()->get_list_of_translations();
            if(!empty($userlang) && array_key_exists($userlang, $alllangs)){
                if(in_array($gradeid, array(25,26,4,5,8,9,23,24))){
                    $this->userlangauge = $userlang;
                } else {
                    $this->userlangauge = 'fr';
                }
            }
            $subscriptionstatus = $currentChild->currentSubscription;
            // if($subscriptionstatus){
            //     if($subscriptionstatus->endDate < time()){
            //         array_push($message, "Subscription Expired");
            //     }
            //     $gradeid = $subscriptionstatus->categoryId;

            // } else {
            // }
            $currentyeadending = strtotime("30 June ".date("Y"));
            if($currentyeadending < time()){
                $currentyeadending = strtotime("30 June ".date("Y", strtotime("+1 year")));
            }
            $this->internal = $DB->record_exists_sql("SELECT ue.*, e.enrol, c.category from mdl_user_enrolments ue INNER JOIN mdl_enrol e on e.id=ue.enrolid INNER JOIN mdl_course c on c.id=e.courseid WHERE ue.userid = ? and e.enrol != 'self' and c.category = ?", array($USER->id, $gradeid));
            self::enrolluserincourses($currentChild->id, $currentChild->grade, "self", $currentyeadending);
            $XPSETTING = $DB->get_record("xpsetting", array("gradeid"=>$gradeid));
            if(empty($XPSETTING)){
                $XPSETTING=new stdClass();
                $XPSETTING->scoremultiplier = 100;
                $XPSETTING->roundon = 10;
                $XPSETTING->bonus_score = 10;
            }
            // $assessmentdata = self::get_grade($gradeid, "assessment");
            $questdata = self::get_grade($gradeid, "quest");
            // $challengedata = self::get_grade($gradeid, "challenge");
            if(!empty($assessmentdata) || !empty($questdata) || !empty($challengedata)){
                $data = new stdClass();
                $data->grade = null;
                $data->quest = $questdata;
                if($this->internal){
                    $data->challenge = null;
                } else {
                    $data->challenge = null;
                }
                $data->message = implode(", ", $message);
                $data->message = implode(", ", $message);
                $data->buyLink = "https://fivestudents.com/pricing_najah/";
                $data->inviteDescription = "Invite your firends to this APP";
                $data->lastUpdated = self::get_lastupdated($gradeid, 1);
                $data->examMode = true;
                $data->missionMode = false;
                if($this->missionMode){
                    $data->missionMode = true;
                }                
                $this->sendResponse($data);
            } else {
                $this->sendError("Invalid Grade", "Grade not Found");
            }
        } else {
            $this->sendError("Failed", "Please try through Child Account");
        }
    }
    private function get_lastupdated($gradeid, $update = false){
        global $DB, $USER;
        $lastupdated = $DB->get_record_sql("SELECT * FROM {childdataupdate} WHERE gradeid = :gradeid AND userid = :userid", array("gradeid"=>$gradeid, "userid"=>$USER->id));
        if(!empty($lastupdated)){
            $lastupdated->lastupdated = time();
            $lastupdated->lastfetched = time();
            $lastupdated->updatedby = $USER->id;
            if($update){
                $DB->update_record("childdataupdate", $lastupdated);
            }
        } else {
            $lastupdated = new stdClass();
            $lastupdated->gradeid = $gradeid;
            $lastupdated->userid = $USER->id;
            $lastupdated->lastupdated = time();
            $lastupdated->lastfetched = time();
            $lastupdated->updatedby = $USER->id;
            if($update){
                $DB->insert_record("childdataupdate", $lastupdated);
            }
        }
        return $lastupdated->lastupdated;
    }
    public function get_grade($gradeid, $topictype){
        global $DB;
        if($category = $DB->get_record("course_categories", array("visible"=>1, "id"=>$gradeid))){
            $gradesetting = $DB->get_record("regionssetting", array("category"=>$gradeid, "course"=>0, "topic"=>0, "module"=>0));
            $grade = array(
                "gradeId"=>$gradeid,
                "gradeLevel"=>"",
                "maxScore"=>self::get_categorygrade($gradeid),
                "backdropImage"=>array(
                    "small"=>"",
                    "medium"=>"",
                    "large"=>"",
                    "extraLarge"=>""
                ),
                "dialogue"=>array(),
                "characterImages"=>array(
                    "brightness"=>0,
                    "mainCharacter"=>array(
                        "mainCharacter"=>0,
                        "imageUrl"=>"",
                        "region"=>"",
                        "provinces"=>"",
                        "xPos"=>0,
                        "yPos"=>0,
                        "narrationSettings" => array(
                            "language"=>"",
                            "voiceStyle"=>"",
                            "speechRate"=>0,
                            "pitch"=>0
                        )
                    ),
                    "secondaryCharacter"=>array(
                        "secondaryCharacter"=>0,
                        "imageUrl"=>"",
                        "region"=>"",
                        "provinces"=>"",
                        "xPos"=>0,
                        "yPos"=>0,
                        "narrationSettings" => array(
                            "language"=>"",
                            "voiceStyle"=>"",
                            "speechRate"=>0,
                            "pitch"=>0
                        )
                    )
                ),
                "backgroundMusicUrl"=>"",
                "ctaColor"=>"",
                "courses"=>array()
            );


            // if(!empty($gradesetting)){
                $designerdata = self::get_designer_narration("category", $gradeid, $this->userlangauge); 
                $grade['gradeLevel']=$category->name;
                $grade['backdropImage']['extraLarge'] = self::get_deginersfiles("category", "backgroundimage", $gradeid);
                $grade['backdropImage']['small'] = $grade['backdropImage']['extraLarge'];
                $grade['backdropImage']['medium'] = $grade['backdropImage']['extraLarge'];
                $grade['backdropImage']['large'] = $grade['backdropImage']['extraLarge'];
                $mcharacterdata = self::get_character($gradesetting->maincharacter);
                $scharacterdata = self::get_character($gradesetting->secondarycharacter);
                $grade['backgroundMusicUrl']=self::get_deginersfiles("category", "backgroundmusicurl", $gradeid);
                $grade['dialogue']=self::get_dialogs("category", $gradeid);
                $grade['characterImages']['brightness']=$gradesetting->brightness;
                $grade['characterImages']['mainCharacter']['imageUrl']=self::get_childImage();
                $grade['characterImages']['mainCharacter']['region']=$mcharacterdata->region;
                $grade['characterImages']['mainCharacter']['provinces']=$mcharacterdata->provinces;
                $grade['characterImages']['mainCharacter']['narrationSettings']=self::get_natrationsetting($gradesetting->maincharacter);
                $grade['characterImages']['mainCharacter']['mainCharacter']=$gradesetting->maincharacter;

                $grade['characterImages']['mainCharacter']['xPos']=floatval($gradesetting->mainxpos);
                $grade['characterImages']['mainCharacter']['yPos']=floatval($gradesetting->mainypos);
                $grade['characterImages']['secondaryCharacter']['imageUrl']=self::get_deginersfiles("character", "imageurl", $gradesetting->secondarycharacter);
                $grade['characterImages']['secondaryCharacter']['region']=$scharacterdata->region;
                $grade['characterImages']['secondaryCharacter']['provinces']=$scharacterdata->provinces;
                $grade['characterImages']['secondaryCharacter']['narrationSettings']=self::get_natrationsetting($gradesetting->secondarycharacter);
                $grade['characterImages']['secondaryCharacter']['secondaryCharacter']=$gradesetting->secondarycharacter;
                $grade['characterImages']['secondaryCharacter']['xPos']=floatval($gradesetting->secondaryxpos);
                $grade['characterImages']['secondaryCharacter']['yPos']=floatval($gradesetting->secondaryypos);
                $grade['ctaColor']=$gradesetting->ctacolor;
                
            // }
            // print_r(self::get_gradecourses($gradeid));
            $grade['courses']=self::get_gradecourses($gradeid, $topictype);
        }
        if(sizeof($grade['courses'])){
           return $grade;
        } else {
            return null;
        }
    }
    public function get_character($characterid){
        global $DB;
        if($character = $DB->get_record("charactersetting", array("id"=>$characterid))){
            $character->region=(!empty($character->region)?$character->region:"");
            $character->provinces=(!empty($character->provinces)?$character->provinces:"");
            return $character;
        } else {
            $character = new stdClass();
            $character->name="";
            $character->imageurl="";
            $character->region="";
            $character->provinces="";
            return $character;
        }
    }
    public function get_childImage(){
        global $PARENTUSER;
        $imageurl = "";
        if(isset($PARENTUSER->currentChild->charImage)){
            return $PARENTUSER->currentChild->charImage;
        }
        return $imageurl;
    }
    public function get_gradecourses($gradeid, $topictype){
        global $DB, $CFG, $INSTITUTION, $USER;
        $allcourse = array();
        $allcoursedata = $DB->get_records_sql("SELECT c.*, cd.value as coursetype FROM {course} c LEFT JOIN {customfield_data} cd on cd.instanceid = c.id LEFT JOIN {customfield_field} cf on cf.id=cd.fieldid AND cf.shortname = 'coursetype' WHERE c.category=:category AND c.visible=:visible ", array("category"=>$gradeid, "visible"=>1));
        $gradeforadditional = array(4,5,25,26,23,24);
        if(in_array($gradeid, $gradeforadditional)){
            if($additionalcourse = $DB->get_record_sql("SELECT c.*, cd.value as coursetype FROM {course} c LEFT JOIN {customfield_data} cd on cd.instanceid = c.id LEFT JOIN {customfield_field} cf on cf.id=cd.fieldid AND cf.shortname = 'coursetype' WHERE c.id=:id AND c.visible=:visible", array("id"=>55, "visible"=>1))){
                $allcoursedata[$additionalcourse->id] = $additionalcourse;       
            }
            if($this->INSTITUTION->ispublic){
                if($additionalcourse = $DB->get_record_sql("SELECT c.*, cd.value as coursetype FROM {view_course} c LEFT JOIN {customfield_data} cd on cd.instanceid = c.id LEFT JOIN {customfield_field} cf on cf.id=cd.fieldid AND cf.shortname = 'coursetype' WHERE c.id=:id AND c.visible=:visible", array("id"=>62, "visible"=>1))){
                    $allcoursedata[$additionalcourse->id] = $additionalcourse;       
                }
            }
        }

        $enabledcourse = array();
        if($this->INSTITUTION){
            $enabledcourse = $DB->get_fieldset_sql("SELECT courseid FROM {institution_enabledcourse} iec WHERE institutionid=:institutionid AND gradeid=:gradeid", array("institutionid"=>$this->INSTITUTION->id, "gradeid"=>$gradeid));
        }

        $this->disablecourse = array();
        foreach ($allcoursedata as $key => $course) {
            if(isset($this->args['courseid']) && !empty($this->args['courseid'])){
                if($this->args['courseid'] != $course->id){
                    continue;
                }
            }
            if(!empty($enabledcourse) && !in_array($course->id, $enabledcourse)){continue;} else
            if(isset($this->INSTITUTION->id) && !empty($this->INSTITUTION->id) && $DB->record_exists_sql("SELECT idc.id FROM {institution_disabledcourse} idc WHERE idc.gradeid=:gradeid AND idc.courseid=:courseid AND idc.institutionid=:institutionid", array("gradeid"=>$gradeid, "courseid"=>$course->id, "institutionid"=>$this->INSTITUTION->id))) {
                continue;
            }
            if(!empty($course->lang)){
                $this->courselangauge = $course->lang;
            }
            if($component = $DB->get_record_sql("SELECT cs.* FROM {course_sections} cs INNER JOIN {tarldiagnostic} as td ON td.assignedcomponent = cs.id WHERE td.userid=:userid AND td.courseid=:courseid AND cs.lang=:lang order by cs.section desc", array("userid"=>$USER->id, "lang"=>$this->userlangauge, "courseid"=>$course->id))){
                $this->assigndcomponents[$course->id] = $component->id;
                // $this->assignedcomponent = $component->id;
                // $this->assignedcomponent1 = $component;
            }
            // $this->assignedcomponent2 = array("SELECT cs.* FROM {course_sections} cs INNER JOIN {tarldiagnostic} as td ON td.assignedcomponent = cs.id WHERE td.userid=:userid AND td.courseid=:courseid AND cs.lang=:lang order by cs.section desc", array("userid"=>$USER->id, "lang"=>$this->userlangauge, "courseid"=>$course->id));
            $coursedata = self::get_gradecourse($course, $topictype);
            // print_r($coursedata);
            if(!empty($coursedata)){
                array_push($allcourse, $coursedata);
            }
        }
        // print_r($allcourse);
        return $allcourse;
    }
    public function get_gradecourse($course, $topictype){
        global $DB, $CFG, $USER;
        self::checkEnrollInCourses($course->id);
        $coursesetting = $DB->get_record("regionssetting", array("course"=>$course->id, "topic"=>0, "module"=>0));
        // if(!empty($coursesetting)){
        $mcharacterdata = self::get_character($coursesetting->maincharacter);
        $scharacterdata = self::get_character($coursesetting->secondarycharacter);

            $coursedata = array(
                "id"=>$course->id,
                "title"=>$course->fullname,
                "shortName"=>$course->shortname,
                "narativeName"=>$course->fullname,
                "type"=>"course",
                "subtype"=>"course",
                "shortDesc"=>$coursesetting->shortdescription,
                "longDesc"=>$coursesetting->longdescription,
                "longDescNaration"=>self::get_deginersfiles("course_".$this->userlangauge, "longdecriptionarration", $course->id),
                "duration"=>$coursesetting->duration,
                "ctaColor"=>$coursesetting->ctacolor,
                "backdropImage"=>array(
                    "small"=>"",
                    "medium"=>"",
                    "large"=>"",
                    "extraLarge"=>""
                ),
                "backgroundMusicUrl"=>self::get_deginersfiles("category", "backgroundmusicurl", $course->id),
                "totalTopics"=>$DB->get_field_sql("SELECT count(cs.id) as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE course = ? and (cfo.value IS NULL OR cfo.value=0)", array("parent", $course->id)),
                "tags"=>explode(",", $coursesetting->tags),
                "xPos"=>$coursesetting->myxpos,
                "yPos"=>$coursesetting->myypos,
                "dialogue"=>self::get_dialogs("course", $course->id),
                "characterImages"=>array(
                    "brightness"=>$coursesetting->brightness,
                    "mainCharacter"=>array(
                        "mainCharacter"=>$coursesetting->maincharacter,
                        "imageUrl"=>self::get_childImage(),
                        "region"=>$mcharacterdata->region,
                        "provinces"=>$mcharacterdata->provinces,
                        "xPos"=>floatval($coursesetting->mainxpos),
                        "yPos"=>floatval($coursesetting->mainypos),
                        "narrationSettings" => self::get_natrationsetting($coursesetting->maincharacter)
                    ),
                    "secondaryCharacter"=>array(
                        "secondaryCharacter"=>$coursesetting->secondarycharacter,
                        "imageUrl"=>self::get_deginersfiles("character", "imageurl", $coursesetting->secondarycharacter),
                        "region"=>$scharacterdata->region,
                        "provinces"=>$scharacterdata->provinces,
                        "xPos"=>floatval($coursesetting->secondaryxpos),
                        "yPos"=>floatval($coursesetting->secondaryypos),
                        "narrationSettings" => self::get_natrationsetting($coursesetting->secondarycharacter)
                    )
                ),
                "topics"=>self::get_coursetopics($course, $topictype),
                // "topicstree"=>self::get_coursetopicstree($course, $topictype),
                "completedTopics"=>0,
                "currentTopicId"=>0,
            );
            $coursedata['backdropImage']['extraLarge'] = self::get_deginersfiles("course", "backgroundimage", $course->id);
            $coursedata['backdropImage']['small'] = $coursedata['backdropImage']['extraLarge'];
            $coursedata['backdropImage']['medium'] = $coursedata['backdropImage']['extraLarge'];
            $coursedata['backdropImage']['large'] = $coursedata['backdropImage']['extraLarge'];

            if($designerdata = self::get_designer_narration("course", $course->id, $this->userlangauge)){
                $coursedata["narativeName"] = !empty($designerdata->name)?$designerdata->name:"";
                $coursedata["shortDesc"] = !empty($designerdata->shortdescription)?$designerdata->shortdescription:"";
                $coursedata["longDesc"] = !empty($designerdata->longdescription)?$designerdata->longdescription:"";
            } 
            if($subjectdata = $DB->get_record_sql("SELECT cf.shortname, cd.instanceid as courseid, cd.value, cf.configdata FROM  mdl_customfield_field cf LEFT JOIN  mdl_customfield_data cd on cf.id=cd.fieldid and cd.instanceid = ? WHERE cf.shortname=?", array($course->id, "coursetype"))){
                $subjectdata->configdata = json_decode($subjectdata->configdata);
                $subjectdata->configdata->optionlist = explode("\r\n", $subjectdata->configdata->options);
                $subject = !empty($subjectdata->value)?$subjectdata->configdata->optionlist[$subjectdata->value-1]:$subjectdata->configdata->defaultvalue;
                $coursedata["subject"] = $subject;
                if(isset($this->courseshortnames[$this->userlangauge][$subject]) && !empty($this->courseshortnames[$this->userlangauge][$subject])){
                    $coursedata['shortName'] = $this->courseshortnames[$this->userlangauge][$subject];
                }
            }
            $coursedata["requireDiagnostic"] = false;
            $coursedata["overviewVideo"] = "";
            $coursedata["diagnosticQuiz"] = 0;
            $coursedata["diagnosticViewed"] = false;
            $coursedata["directIn"] = false;
            $coursedata["directInTopic"] = null;
            $coursedata["directInTopicData"] = null;
            $coursedata["directInTopicParents"] = array();
            if(in_array($course->id, $this->publicflowcourse)){
                if($this->INSTITUTION && $this->INSTITUTION->ispublic){
                    $coursedata["requireDiagnostic"] = true;
                    $coursedata["overviewVideo"] = $this->overviewvideo;
                    $coursedata["diagnosticQuiz"] = $this->get_diagnosticQuiz($course->id);
                    if(empty($coursedata["diagnosticQuiz"])){
                        if($openquiz = $DB->get_record_sql("SELECT * FROM {tarldiagnostic} WHERE courseid=:courseid AND userid=:userid AND timefinish IS NULL order by id desc limit 0, 1", array("courseid"=>$course->id, "userid"=>$USER->id))){
                            $coursedata["openquiz"] = $openquiz;
                            if($directInTopic = $DB->get_record_sql("SELECT cs.id, cs.name, cs.section, cs.course, cs.lang, 
                            cs1.id as parentid, cs1.section as parentsection, cs1.name as parentname, cs1.lang as parentlang, cs1.isdiagnostic as parentisdiagnostic, 
                            cs2.id as grandparentid, cs2.section as grandparentsection, cs2.name as grandparentname, cs2.lang as grandparentlang, cs2.isdiagnostic as grandparentisdiagnostic 

                            FROM mdl_course_sections cs 
                            INNER JOIN mdl_course_format_options cfo ON cfo.courseid = cs.course AND cfo.sectionid =cs.id AND cfo.name = 'parent' 
                            INNER JOIN mdl_course_sections cs1 ON cs1.course = cfo.courseid AND cs1.section = cfo.value 
                            INNER JOIN mdl_course_format_options cfo1 ON cfo1.courseid = cs1.course AND cfo1.sectionid =cs1.id AND cfo1.name = 'parent' 
                            INNER JOIN mdl_course_sections cs2 ON cs2.course = cfo1.courseid AND cs2.section = cfo1.value 
                            WHERE cs.id=:sectionid", array("sectionid"=>$openquiz->maintopic))){
                                $coursedata["directIn"] = true;
                                if($directInTopic->grandparentlang != $this->userlangauge && $othertopic = $this->getOtherTopic($directInTopic->grandparentid)){
                                    $coursedata["directInTopic"] = $othertopic->id;
                                    $coursedata["directInTopicName"] = $othertopic->name;
                                    $coursedata["directInTopicData"] = $othertopic;
                                    $coursedata["directInTopicParents"] = $this->get_parenttopics($othertopic->id);
                                } else {
                                    $coursedata["directInTopic"] = $directInTopic->grandparentid;
                                    $coursedata["directInTopicName"] = $directInTopic->grandparentname;
                                    $coursedata["directInTopicData"] = $directInTopic;
                                    $coursedata["directInTopicParents"] = $this->get_parenttopics($directInTopic->grandparentid);
                                }
                            }


                        }
                    }
                    $coursedata["diagnosticViewed"] = $DB->record_exists("tarldiagnosticviewed", array("courseid"=>$course->id, "userid"=>$USER->id));
                } else {
                    return null;
                }
            }
            $coursedata["totalTopics"] = sizeof($coursedata['topics']);
            $coursedata["locked"] = false;
            $coursedata["completed"] = self::isCompleted($coursedata["topics"]);
            $coursedata["started"] = self::isStarted($coursedata["topics"]);
            $coursedata["completedModule"] = self::totalCompleted($coursedata["topics"]);
            $coursedata["totalModule"] = sizeof($coursedata["topics"]);
            $coursedata["status"] = self::getMyStatus($coursedata["locked"], $coursedata["completed"], $coursedata["started"]);

        // }
        if(sizeof($coursedata["topics"])>0){
           return $coursedata;
        } else {
            return null;
        }
    }
    private function lockalltopics($course)
    {
        global $DB, $CFG, $TOPICTYPE, $PARENTUSER, $USER;
        $sections = $DB->get_records("course_sections", array("course"=>$course->id));
        $sectionstoinsert = array();
        foreach ($sections as $key => $section) {
            $insdata = new stdClass();
            $insdata->userid = $USER->id;
            $insdata->courseid = $course->id;
            $insdata->topicid = $section->id;
            $insdata->status = 3;
            $insdata->updatedtime = time();
            array_push($sectionstoinsert, $insdata);
        }
        $DB->delete_records("tarltopics_completion",array("userid"=>$USER->id, "courseid"=>$course->id));
        $DB->insert_records("tarltopics_completion",$sectionstoinsert);
    }
    private function get_diagnosticQuiz($courseid)
    {
        global $DB, $CFG, $TOPICTYPE, $PARENTUSER, $USER;
        $currentChild = $PARENTUSER->currentChild;
        if(!isset($this->publicflowdiagnostic[$courseid])){
            return 0;
        }
        $tarldiagtopics = $this->publicflowdiagnostic[$courseid];
        if(is_array($tarldiagtopics)){
            $tarldiagtopics = implode(",", $tarldiagtopics);
        }
        $gradeid = $currentChild->grade;
        $this->setstatesfortopics($courseid);
        $existing = $DB->get_record_sql("SELECT * FROM {tarldiagnostic} WHERE courseid=:courseid and userid=:userid AND preview=0 ORDER BY id desc limit 0, 1", array("courseid"=>$courseid, "userid"=>$USER->id));
        $topic = 0;
        if($existing){
            if($existing->diagnostic && empty($existing->timefinish)){
                $topic = $existing->topic;
            }
        } else {
            $query1 = "SELECT cs.id FROM mdl_course_sections cs INNER JOIN mdl_course_format_options cfo ON cfo.courseid = cs.course AND cfo.sectionid = cs.id AND cfo.name = 'parent' INNER JOIN mdl_course_sections cs1 ON cs1.course = cfo.courseid AND cs1.section = cfo.value INNER JOIN mdl_course_format_options cfo1 ON cfo1.courseid = cs1.course AND cfo1.sectionid =cs1.id AND cfo1.name = 'parent' INNER JOIN mdl_course_sections cs2 ON cs2.course = cfo1.courseid AND cs2.section = cfo1.value INNER JOIN mdl_course_modules cm on FIND_IN_SET(cm.id,cs.sequence) AND cm.deletioninprogress=0 INNER JOIN mdl_modules as m on m.id=cm.module AND m.name='quiz' INNER JOIN mdl_quiz q on q.id=cm.instance AND q.ismainquiz=1 WHERE cs.course=:course AND (FIND_IN_SET(:sectionforgrades,cs.forgrades)) AND (FIND_IN_SET(:quizforgrades,q.forgrades)) AND (cs.lang IS NULL OR cs.lang='' OR cs.lang=:userlang) AND cs.isdiagnostic=1 AND cs1.id in({$tarldiagtopics}) GROUP BY cs.id ORDER BY cs.name limit 0,1";
            $query2 = "SELECT cs.id FROM mdl_course_sections cs INNER JOIN mdl_course_format_options cfo ON cfo.courseid = cs.course AND cfo.sectionid = cs.id AND cfo.name = 'parent'  INNER JOIN mdl_course_sections cs1 ON cs1.course = cfo.courseid AND cs1.section = cfo.value INNER JOIN mdl_course_format_options cfo1 ON cfo1.courseid = cs1.course AND cfo1.sectionid =cs1.id AND cfo1.name = 'parent' INNER JOIN mdl_course_sections cs2 ON cs2.course = cfo1.courseid AND cs2.section = cfo1.value INNER JOIN mdl_course_modules cm on FIND_IN_SET(cm.id,cs.sequence) AND cm.deletioninprogress=0 INNER JOIN mdl_modules as m on m.id=cm.module AND m.name='quiz' INNER JOIN mdl_quiz q on q.id=cm.instance AND q.ismainquiz=1 WHERE cs.course=:course AND (FIND_IN_SET(:sectionforgrades,cs.forgrades)) AND (q.forgrades=0 OR q.forgrades IS NULL OR FIND_IN_SET(:quizforgrades,q.forgrades)) AND (cs.lang IS NULL OR cs.lang='' OR cs.lang=:userlang) AND cs.isdiagnostic=1 AND cs1.id in({$tarldiagtopics}) GROUP BY cs.id ORDER BY cs.name limit 0,1";
            $query3 = "SELECT cs.id FROM mdl_course_sections cs INNER JOIN mdl_course_format_options cfo ON cfo.courseid = cs.course AND cfo.sectionid = cs.id AND cfo.name = 'parent' INNER JOIN mdl_course_sections cs1 ON cs1.course = cfo.courseid AND cs1.section = cfo.value INNER JOIN mdl_course_format_options cfo1 ON cfo1.courseid = cs1.course AND cfo1.sectionid =cs1.id AND cfo1.name = 'parent' INNER JOIN mdl_course_sections cs2 ON cs2.course = cfo1.courseid AND cs2.section = cfo1.value INNER JOIN mdl_course_modules cm on FIND_IN_SET(cm.id,cs.sequence) AND cm.deletioninprogress=0 INNER JOIN mdl_modules as m on m.id=cm.module AND m.name='quiz' INNER JOIN mdl_quiz q on q.id=cm.instance AND q.ismainquiz=1 WHERE cs.course=:course AND (cs.forgrades=0 OR cs.forgrades IS NULL OR FIND_IN_SET(:sectionforgrades,cs.forgrades)) AND (q.forgrades=0 OR q.forgrades IS NULL OR FIND_IN_SET(:quizforgrades,q.forgrades)) AND (cs.lang IS NULL OR cs.lang='' OR cs.lang=:userlang) AND cs.isdiagnostic=1 AND cs1.id in({$tarldiagtopics}) GROUP BY cs.id ORDER BY cs.name limit 0,1";
            $topic= 0;
            if($topicid = $DB->get_field_sql($query1, array("course"=>$courseid, "sectionforgrades"=>$gradeid, "quizforgrades"=>$gradeid, "userlang"=>$this->userlangauge))){
                // print_r(array($query1, array("course"=>$courseid, "sectionforgrades"=>$gradeid, "quizforgrades"=>$gradeid)));
                $topic = $topicid;
            } else if($topicid = $DB->get_field_sql($query2, array("course"=>$courseid, "sectionforgrades"=>$gradeid, "quizforgrades"=>$gradeid, "userlang"=>$this->userlangauge))){
                // print_r(array($query2, array("course"=>$courseid, "sectionforgrades"=>$gradeid, "quizforgrades"=>$gradeid)));
                $topic = $topicid;
            } else if($topicid = $DB->get_field_sql($query3, array("course"=>$courseid, "sectionforgrades"=>$gradeid, "quizforgrades"=>$gradeid, "userlang"=>$this->userlangauge))){
                // print_r(array($query3, array("course"=>$courseid, "sectionforgrades"=>$gradeid, "quizforgrades"=>$gradeid)));
                $topic = $topicid;
            }
            if($topic && $topicdata=$DB->get_record_sql("SELECT cs.id, cs.name, cs.section, cs.course, cs.isdiagnostic, cs1.id as parentid, cs1.section as parentsection, cs1.name as parentname, cs1.isdiagnostic as parentisdiagnostic FROM mdl_course_sections cs INNER JOIN mdl_course_format_options cfo ON cfo.courseid = cs.course AND cfo.sectionid =cs.id AND cfo.name = 'parent' INNER JOIN mdl_course_sections cs1 ON cs1.course = cfo.courseid AND cs1.section = cfo.value WHERE cs.id=:id", array("id"=>$topic))){
                $modules = $DB->get_fieldset_sql("SELECT cm.id FROM {course_modules} cm INNER JOIN {modules} m on m.id=cm.module AND m.name='quiz' INNER JOIN {quiz} q on q.id=cm.instance and q.ismainquiz = 1 where cm.section=:sectionid AND cm.deletioninprogress=0 AND cm.visible=1 AND (q.forgrades=0 OR q.forgrades IS NULL OR FIND_IN_SET(:quizforgrades,q.forgrades))", array("sectionid"=> $topic, "quizforgrades"=>$gradeid));
                if(empty($modules)){
                    $modules = $DB->get_fieldset_sql("SELECT cm.id FROM {course_modules} cm INNER JOIN {modules} m on m.id=cm.module AND m.name='quiz' INNER JOIN {quiz} q on q.id=cm.instance and q.ismainquiz = 1  where cm.section=:sectionid AND cm.deletioninprogress=0 AND cm.visible=1", array("sectionid"=> $topic));
                }
                $d = new stdClass();
                $d->userid = $USER->id;
                $d->schoolyear = $this->currentschoolyear;
                $d->courseid = $courseid;
                $d->topic = $topic;
                $d->maintopic = $topic;
                $d->maintopicparent = $topicdata->parentid;
                if(sizeof($modules) > 0){
                    $key = array_rand($modules);
                    $quizid = $modules[$key];
                    $d->quiz = $quizid;
                }
                $d->diagnostic = $topicdata->isdiagnostic;
                $d->createdby = $USER->id;
                $d->createddate = time();
                $DB->insert_record("tarldiagnostic",$d);
            }
        }
        return $topic?$topic:0;
    }
    public function get_coursetopics($course, $topictype, $parent = null){
        global $DB, $CFG, $TOPICTYPE, $PARENTUSER;
        $gradeid=0;
        if($currentChild = $PARENTUSER->currentChild){
            $gradeid = $currentChild->grade;
        }
        require_once($CFG->dirroot."/course/lib.php");
        $alltopicdata = array();
        $TOPICTYPE = $topictype;

        $langfilter = "";
        if($course->coursetype != 6){
            $langfilter = "and (cs.lang IS NULL or cs.lang=? or cs.lang=?)";
        }
        if(empty($parent)){
            $alltopics = $DB->get_recordset_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE course = ? and (cfo.value IS NULL OR cfo.value=0) and cs.isattempt = 0 and cs.type = ? {$langfilter} order by section asc", array("parent", $course->id, $topictype, "", $this->userlangauge));
        } else if(!empty($parent->section)) {
            $alltopics = array();
            if(isset($this->args['completetree']) && !empty($this->args['completetree'])){
                $alltopics = $DB->get_recordset_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE course = ? and cfo.value=? AND (cs.forgrades = 0 OR FIND_IN_SET(?,cs.forgrades)) AND (cs.type = NULL OR cs.type = '' OR  cs.type = ?) and cs.isattempt = 0 {$langfilter} order by section asc", array("parent", $course->id, $parent->section, $gradeid, $topictype, "", $this->userlangauge));
            }            
        } else {
            $alltopics = array();
        }
        foreach ($alltopics as $topic) {
            if(in_array($course->id, $this->publicflowcourse) && !empty($topic->sequence)){
                continue;
            }

            $restrictiontype = "topic";
            if(!empty($parent)){
                $restrictiontype = "subtopic";
                $topic->type = $parent->type;
            }
            $sectioninfo = get_fast_modinfo($course, $USER->id)->get_section_info($topic->section);
            if(empty($topic->name)){
                $topic->name=get_section_name($course, $sectioninfo);
            }
            $topic->uservisible = !$sectioninfo->uservisible;
            $topic->restricted = self::isrestricted($restrictiontype, $topic->id);

            $topicdata = self::get_topic($course, $topic, $topictype);
            if(!empty($topicdata)){
                $topicdata['parent'] = (isset($parent->id)?$parent->id:0);
                $topicdata['position'] = sizeof($alltopicdata)+1;
                $topicdata['TOPICTYPE'] = $TOPICTYPE;
                array_push($alltopicdata, $topicdata); 
            }
        }
        return $alltopicdata;
    }
    public function isrestricted($level, $itemid){
        global $DB, $CFG, $USER;
        $restricted = false;
        switch ($level) {
            case 'topic':
                $restricted = $DB->record_exists_sql("SELECT mr.* FROM mdl_modules_restriction mr INNER JOIN mdl_institution_group ig ON ig.institutionid = mr.institutionid AND ig.id = mr.groupid INNER JOIN mdl_institution_group_member igm ON igm.institutionid = ig.institutionid AND igm.schoolyear= mr.schoolyear AND igm.status=1 AND igm.groupid = mr.groupid WHERE mr.schoolyear = :schoolyear AND igm.userid = :userid AND mr.status = :status AND mr.topic=:topic AND mr.subtopic=:subtopic AND mr.quiz=:quiz ", array("schoolyear"=>$this->currentschoolyear, "userid"=>$USER->id, "status"=>1, "topic"=>$itemid, "subtopic"=>0, "quiz"=>0));
                break;
            case 'subtopic':
                $restricted = $DB->record_exists_sql("SELECT mr.* FROM mdl_modules_restriction mr INNER JOIN mdl_institution_group ig ON ig.institutionid = mr.institutionid AND ig.id = mr.groupid INNER JOIN mdl_institution_group_member igm ON igm.institutionid = ig.institutionid AND igm.schoolyear= mr.schoolyear AND igm.status=1 AND igm.groupid = mr.groupid WHERE mr.schoolyear = :schoolyear AND igm.userid = :userid AND mr.status = :status AND mr.subtopic=:subtopic AND mr.quiz=:quiz ", array("schoolyear"=>$this->currentschoolyear, "userid"=>$USER->id, "status"=>1, "subtopic"=>$itemid, "quiz"=>0));
                break;
            case 'quiz':
                $restricted = $DB->record_exists_sql("SELECT mr.* FROM mdl_modules_restriction mr INNER JOIN mdl_institution_group ig ON ig.institutionid = mr.institutionid AND ig.id = mr.groupid INNER JOIN mdl_institution_group_member igm ON igm.institutionid = ig.institutionid AND igm.schoolyear= mr.schoolyear AND igm.status=1 AND igm.groupid = mr.groupid WHERE mr.schoolyear = :schoolyear AND igm.userid = :userid AND mr.status = :status AND mr.quiz=:quiz ", array("schoolyear"=>$this->currentschoolyear, "userid"=>$USER->id, "status"=>1, "quiz"=>$itemid));
                break;
            default:
                break;
        }
        return $restricted;
    }


    public function get_topic($course, $topic, $topictype){
        global $DB, $CFG, $USER, $PARENTUSER;
        $gradeid=0;
        if($currentChild = $PARENTUSER->currentChild){
            $gradeid = $currentChild->grade;
        }

        $topicsetting = $DB->get_record("regionssetting", array("course"=>$topic->course, "topic"=>$topic->id, "module"=>0));
        $mcharacterdata = self::get_character($topicsetting->maincharacter);
        $scharacterdata = self::get_character($topicsetting->secondarycharacter);
        // if(!empty($topicsetting)){
            $topicdata = array(
                "id"=>$topic->id,
                "section"=>$topic->section,
                "courseId"=>$topic->course,
                "title"=>$topic->name,
                "shortName"=>$topic->shortname,
                "lang"=>$topic->lang,
                "narativeName"=>$topic->name,
                "type"=>($topic->parent?"subtopic":"topic"),
                "subtype"=>$topic->type,
                "shortDesc"=>$topicsetting->shortdescription,
                "longDesc"=>$topicsetting->longdescription,
                "longDescNaration"=>self::get_deginersfiles("topic_".$this->userlangauge, "longdecriptionarration", $topic->id),
                "duration"=>$topicsetting->duration,
                "ctaColor"=>$topicsetting->ctacolor,
                "backdropImage"=>array(
                    "small"=>"",
                    "medium"=>"",
                    "large"=>"",
                    "extraLarge"=>""
                ),
                "backgroundMusicUrl"=>self::get_deginersfiles("topic", "backgroundmusicurl", $topic->id),
                "tags"=>explode(",", $topicsetting->tags),
                "xPos"=>$topicsetting->myxpos,
                "yPos"=>$topicsetting->myypos,
                "dialogue"=>self::get_dialogs("topic", $topic->id),
                "characterImages"=>array(
                    "brightness"=>$topicsetting->brightness,
                    "mainCharacter"=>array(
                        "mainCharacter"=>$topicsetting->maincharacter,
                        "imageUrl"=>self::get_childImage(),
                        "region"=>$mcharacterdata->region,
                        "provinces"=>$mcharacterdata->provinces,
                        "xPos"=>floatval($topicsetting->mainxpos),
                        "yPos"=>floatval($topicsetting->mainypos),
                        "narrationSettings" => self::get_natrationsetting($topicsetting->maincharacter)
                    ),
                    "secondaryCharacter"=>array(
                        "secondaryCharacter"=>$topicsetting->secondarycharacter,
                        "imageUrl"=>self::get_deginersfiles("character", "imageurl", $topicsetting->secondarycharacter),
                        "region"=>$scharacterdata->region,
                        "provinces"=>$scharacterdata->provinces,
                        "xPos"=>floatval($topicsetting->secondaryxpos),
                        "yPos"=>floatval($topicsetting->secondaryypos),
                        "narrationSettings" => self::get_natrationsetting($topicsetting->secondarycharacter)
                    )
                ),
                "totalSubTopics"=>0,
                "subtopics"=>array(),
                "completedSubTopics"=>0,
                "currentSubTopicId"=>0,
                "parentData" => $DB->get_record_sql("SELECT cs.id, cs.name, cfo.value as parent, cs1.id as parentid, cs.section, cs.course, cs.forgrades from {course_sections} cs INNER JOIN {course_format_options} cfo ON cfo.courseid = cs.course AND cfo.sectionid =cs.id AND cfo.name = 'parent' LEFT JOIN {course_sections} cs1 on cs1.course = cfo.courseid AND cs1.section = cfo.value where cs.id=:sectionid", array("sectionid"=>$topic->id)),
                "rewards"=> array(),
                "quizzes"=> array(),
            );
            if($topicdata['parentData']){
                if($topicdata['parentData']->parent == 0){
                    $topicdata['parentData'] = null;
                    $topicdata['parent'] = 0;
                } else {
                    $topicdata['parent'] = $topicdata['parentData']->parentid;
                }
            } else {
                $topicdata['parent'] = 0;
            }
            $topicdata['backdropImage']['extraLarge'] = self::get_deginersfiles("topic", "backgroundimage", $topic->id);
            $topicdata['backdropImage']['small'] = $topicdata['backdropImage']['extraLarge'];
            $topicdata['backdropImage']['medium'] = $topicdata['backdropImage']['extraLarge'];
            $topicdata['backdropImage']['large'] = $topicdata['backdropImage']['extraLarge'];

            $topicdata["isTarl"] = false;
            $topicdata["isTarlQuiz"] = false;
            if($designerdata = self::get_designer_narration("topic", $topic->id, $this->userlangauge)){
                $topicdata["narativeName"] = !empty($designerdata->name)?$designerdata->name:"";
                $topicdata["shortDesc"] = !empty($designerdata->shortdescription)?$designerdata->shortdescription:"";
                $topicdata["longDesc"] = !empty($designerdata->longdescription)?$designerdata->longdescription:"";
            } 
            // $topicdata['moreSubTopics'] = $DB->record_exists_sql("SELECT cs.* FROM {view_course_sections} cs WHERE cs.course = ? and cs.parent=? and (cs.lang=? or cs.lang=?) AND cs.type=? order by section asc", array($course->id, $topic->section, "", $this->userlangauge, "quest"));
            $topicdata['moreSubTopics'] = $DB->record_exists_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE course = ? and cfo.value=? AND (cs.forgrades = 0 OR FIND_IN_SET(?,cs.forgrades)) AND (cs.type = NULL OR cs.type = '' OR  cs.type = ?) and cs.isattempt = 0 {$langfilter} order by section asc", array("parent", $course->id, $topic->section, $gradeid, $topictype, "", $this->userlangauge)); 
            $topicdata["currentQuizId"] = 0;
            $topicdata["locked"] = $topic->uservisible;
            $topicdata["restricted"] = $topic->restricted;
            if($topic->restricted){
                $topicdata["locked"] = $topic->restricted;
            }

            if(!in_array($course->id, $this->publicflowcourse)){
                $topicdata["subtopics"] = self::get_coursetopics($course, $topictype, $topic);
                $topicdata["totalSubTopics"] = sizeof($topicdata["subtopics"]);
                if(!$topicdata['moreSubTopics']){
                    $topicdata['quizzes'] = self::get_quizes($course, $topic);
                }
                if($topicdata["totalSubTopics"]){
                    $topicdata["completed"] = self::isCompleted($topicdata["subtopics"]);
                    $topicdata["started"] = self::isStarted($topicdata["subtopics"]);
                    $topicdata["completedModule"] = self::totalCompleted($topicdata["subtopics"]);
                    $topicdata["totalModule"] = sizeof($topicdata["subtopics"]);
                } else {
                    $topicdata["completed"] = self::isCompleted($topicdata["quizzes"]);
                    $topicdata["started"] = self::isStarted($topicdata["quizzes"]);
                    $topicdata["completedModule"] = self::totalCompleted($topicdata["quizzes"]);
                    $topicdata["totalModule"] = sizeof($topicdata["quizzes"]);
                }
            } else {
                $topicdata["isTarl"] = true;
                $topicdata["isTarlQuiz"] = false;
                $topicdata["locked"] = true;
                $topicdata["restricted"] = false;
                $modules = $DB->get_fieldset_sql("SELECT cm.id FROM {course_modules} cm INNER JOIN {modules} m on m.id=cm.module AND m.name='quiz' where cm.section=:sectionid AND cm.visible=1", array("sectionid"=> $topic->id));
                if(sizeof($modules) > 0){
                    $topicdata["isTarlQuiz"] = true;
                }
                $topicstatus = $this->getTarlTopicStatus($topic);
                if($topicstatus != 3){
                    $topicdata["subtopics"] = self::get_coursetopics($course, $topictype, $topic);
                    $topicdata["totalSubTopics"] = sizeof($topicdata["subtopics"]);
                } else {
                    $topicdata["subtopics"] = array();
                    $topicdata["totalSubTopics"] = 0;
                }
                if($topicstatus){
                    $topicdata["locked"] = ($topicstatus == 3)?true:false;
                    $topicdata["completed"] = ($topicstatus == 2)?true:false;
                    $topicdata["started"] = ($topicstatus <= 2)?true:false;
                } else {
                    $topicdata["locked"] = true;
                    $topicdata["completed"] = false;
                    $topicdata["started"] = false;
                }
                if($topicdata["locked"]){
                    $topicdata["restricted"] = true;
                }
                $topicdata["isTarlTopic"] = false;
                $topicdata["isTarlQuizId"] = 0;
                $topicdata["completedModule"] = 0;
                $topicdata["totalModule"] = 0;
                $topicdata["secondDiag"] = 0;
                $topicdata["secondDiagBtn"] = false;
                // if($topicstatus <= 3){
                    if($diagnostic = $DB->get_record_sql("SELECT td.*, cfo.value as parent FROM mdl_course_sections cs INNER JOIN mdl_tarldiagnostic td ON td.assignedcomponent=cs.id INNER JOIN mdl_course_format_options cfo on cs.id = cfo.sectionid and cfo.name='parent' WHERE cs.course = :course AND cs.isdiagnostic=1 AND td.preview=1 AND td.userid=:userid AND cfo.value=:parent AND td.timefinish IS NULL order by cs.section asc", array("course"=>$topic->course, "parent" => $topic->section, "userid" => $USER->id))){
                        $topicdata["secondDiag"] = $diagnostic->topic;
                        $topicdata["secondDiagBtn"] = true;
                        $topicdata["completed"] = false;
                        $topicdata["started"] = true;
                    } else {
                        $othertopic = null;
                        if($topic->clonetopics){
                            $othertopic = $DB->get_record_sql("SELECT * FROM {view_course_sections} WHERE id=:id", array("id"=>$topic->clonetopics));
                            array_push($ids, $othertopic->id);
                        } else {
                            $othertopic = $DB->get_record_sql("SELECT * FROM {view_course_sections} WHERE clonetopics=:clonetopics", array("clonetopics"=>$topic->id));
                            array_push($ids, $othertopic->id);
                        }
                        // $topicdata["othertopic"] = $othertopic;
                        if($othertopic){
                            if($diagnostic = $DB->get_record_sql("SELECT td.*, cfo.value as parent FROM mdl_course_sections cs INNER JOIN mdl_tarldiagnostic td ON td.assignedcomponent=cs.id INNER JOIN mdl_course_format_options cfo on cs.id = cfo.sectionid and cfo.name='parent' WHERE cs.course = :course AND cs.isdiagnostic=1 AND td.preview=1 AND td.userid=:userid AND cfo.value=:parent AND td.timefinish IS NULL order by cs.section asc", array("course"=>$othertopic->course, "parent" => $othertopic->section, "userid" => $USER->id))){
                                $topicdata["secondDiag"] = $diagnostic->topic;
                                $topicdata["secondDiagBtn"] = true;
                                $topicdata["completed"] = false;
                                $topicdata["started"] = true;
                            }
                        }
                    }
                // }
                $topicdata["childs"] = array_values($DB->get_records_sql("SELECT cs1.*, cfo1.value as parent, tc.status FROM {course_sections} cs1 left join {course_format_options} cfo1 on cfo1.name='parent' AND cfo1.courseid=cs1.course AND cs1.id = cfo1.sectionid LEFT JOIN {tarltopics_completion} tc on cs1.course=tc.courseid AND cs1.id=tc.topicid AND tc.userid=:userid WHERE cs1.course = :course and cfo1.value=:section", array("course"=>$course->id, "section"=>$topic->section, "userid"=>$USER->id)));

                $seqs = array_filter(array_column($topicdata["childs"], 'sequence'));
                if(!empty($seqs)){
                    $topicdata["isTarlTopic"] = true;
                    $topicdata["totalModule"] = sizeof($topicdata["childs"]);
                    $totalstatus = array_column($topicdata["childs"], 'status');
                    $statuscount = array_count_values($totalstatus);
                    if(in_array(2, $totalstatus)){$topicdata["completedModule"]=$statuscount[2];}
                    if($topicdata["started"]){
                        $topicdata["isTarlQuizId"] = $this->get_tarlquizid($topicdata);
                    }
                }
            }
            if($currentQuiz = $DB->get_record_sql("select * from {quizaccessed} where courseid = ? and topicid = ? and userid=? order by id desc limit 0, 1", array($topic->course,$topic->id, $USER->id))){
                $topicdata["currentQuizId"] = $currentQuiz->moduleid;
            }
            $topicdata["status"] = self::getMyStatus($topicdata["locked"], $topicdata["completed"], $topicdata["started"]);
            if(empty($topicdata["status"]) && $topicdata["isTarl"]){
                 $topicdata["status"] = 3;
            }
        // }
            unset($topicdata['childs']);
        return $topicdata;
    }
    public function getMyStatus($locked=false, $completed=false, $started = false, $grade=""){
        $returnstatus = 0;
        if($locked){
            $returnstatus = 3;
        } else if($started){
            if(!$completed){
                if($grade == ""){
                    $returnstatus = 1;
                } else {
                    $returnstatus = 4;
                }
            } else {
                $returnstatus = 2;
            }
        }
        return $returnstatus;
    }
    public function isCompleted($allmodules){
        $completed = true;
        if(!empty($allmodules)){
            foreach ($allmodules as $key => $modules) {
                if(!$modules["completed"]){
                    $completed = false;
                    break;
                }
            }
        } else {
            $completed = false;
        }
        return $completed;
    }
    public function isLocked($allmodules){
        $locked = true;
        if(!empty($allmodules)){
            foreach ($allmodules as $key => $modules) {
                if(!$modules["locked"]){
                    $locked = false;
                    break;
                }
            }
        } else {
            $locked = true;
        }
        return $locked;
    }
    public function isStarted($allmodules){
        $started = false;
        foreach ($allmodules as $key => $modules) {
            if($modules["started"]){
                $started = true;
                break;
            }
        }
        return $started;
    }
    public function totalCompleted($allmodules){
        $completed = 0;
        foreach ($allmodules as $key => $modules) {
            if($modules["completed"]){
                $completed++;
            }
        }
        return $completed;
    }
    public function get_quizes($course, $topic){
        global $DB, $USER;
        $allquizzes = array();
        $allids = explode(",",$topic->sequence);
        if(empty($allids)){
            $allmodules = array();
        } else {
            [$insql, $inparams] = $DB->get_in_or_equal($allids);
            $sql = "SELECT cm.id as cm, cm.instance as id, q.name as name, m.name as 'mod',q.remedyquiz, q.istutoring FROM mdl_course_modules cm  INNER JOIN mdl_quiz q on cm.instance = q.id INNER JOIN mdl_modules as m on m.id = cm.module and m.name ='quiz' WHERE cm.deletioninprogress = 0 AND cm.id $insql order by q.name ";
            $allmodules = $DB->get_recordset_sql($sql, $inparams);
        }
        foreach ($allmodules as $module) {
            if(
                ($module->remedyquiz && !self::isInternalUser($course->id, $USER->id)) ||
                ($module->istutoring == 1)
            ){ continue;}
            $quizdata = self::get_quiz($course, $topic, $module);
            if(!empty($quizdata)){
                $quizdata['quizno'] = "Quiz n<sup><small>o</small></sup>".(sizeof($allquizzes)+1);
                array_push($allquizzes, $quizdata);
            }
        }

        return $allquizzes;
    }
    private function quiz_get_best_grade($quizid, $userid){
        global $DB; 
        return $DB->get_field_sql("select max(qa.sumgrades) from {quiz_attempts} qa INNER JOIN {quizattempts} qas on qas.attemptid=qa.id where qa.quiz=? and qa.userid=?", array($quizid, $userid));
    }
    public function get_quiz($course, $topic, $module){
        global $DB, $CFG, $USER, $CATGRADE, $XPSETTING, $TOPICTYPE, $PARENTUSER;
        $quiz = $DB->get_record("quiz", array("id"=>$module->id));
        $subjectdata = $DB->get_record_sql("SELECT cf.shortname, cd.instanceid as courseid, cd.value, cf.configdata FROM  mdl_customfield_field cf LEFT JOIN  mdl_customfield_data cd on cf.id=cd.fieldid and cd.instanceid = ? WHERE cf.shortname=?", array($course->id, "coursetype"));
        if (($this->premiumAccount && !$quiz->accessibility)
         ||  (!empty($quiz->lang) && $quiz->lang != $this->userlangauge && $subjectdata->value != 2)) {
            return;
        }
        if(!empty($quiz->forgrades)){
            $forgrades = explode(",", $quiz->forgrades);
            $currentChild = $PARENTUSER->currentChild;
            $gradeid = $currentChild->grade;
            if(!in_array($gradeid, $forgrades)){
                return;
            }
        }
        // if (
        //     ($this->premiumAccount && !$quiz->accessibility) ||  
        //     ($quiz->lang != "" && $quiz->lang != $this->userlangauge)
        //     ) {
        //     return;
        // }
        $this->get_categorygrade();
        require_once($CFG->dirroot."/mod/quiz/lib.php");
        // $bestgrade = quiz_get_best_grade($module, $USER->id);
        $bestgrade = self::quiz_get_best_grade($quiz->id, $USER->id);
        $iscompleted = $DB->record_exists_sql("select * from {course_modules_completion} where coursemoduleid = ? and userid=? and completionstate != ?", array($module->cm, $USER->id,0));
        $xp = 0;
        if($xp_userData=$DB->get_record_sql("SELECT * FROM {xphistory} WHERE userid=? AND moduleid=? order by gotgrade desc limit 0, 1",array($USER->id,$module->cm))){
            $xp=$xp_userData->gotgrade;
            $maxxp=$xp_userData->gotgrademax;
        } else {
            $maxxp= intval($XPSETTING->roundon)*$XPSETTING->scoremultiplier;
        }
        // if($modulesetting = $DB->get_record("regionssetting", array("course"=>$topic->course, "topic"=>$topic->id, "module"=>$module->cm))){
            $moduledata = array(
                "id"=>$module->id,
                "cmid"=>$module->cm,
                "topicId"=>$topic->id,
                "forgrades"=>explode(",", $quiz->forgrades),
                "courseId"=>$topic->course,
                "title"=>$module->name,
                "shortName"=>$module->name,
                "narativeName"=>$module->name,
                "lang"=>(!empty($quiz->lang)?$quiz->lang:$topic->lang),
                "type"=>$module->mod,
                "istutoring"=>$quiz->istutoring,
                "remedyquiz"=>$quiz->remedyquiz,
                "groupaquiz"=>$quiz->groupaquiz,
                "groupbquiz"=>$quiz->groupbquiz,
                "groupcquiz"=>$quiz->groupcquiz,
                "subtype"=>$topic->type,
                "xp"=>$xp,
                "maxXp"=>$maxxp,
                "longDescNaration"=>self::get_deginersfiles("module_".$this->userlangauge, "longdecriptionarration", $module->id),
                // "shortDesc"=>$modulesetting->shortdescription,
                // "longDesc"=>$modulesetting->longdescription,
                // "duration"=>$modulesetting->duration,
                // "ctaColor"=>$modulesetting->ctacolor,
                "backdropImage"=>array(
                    "small"=>"",
                    "medium"=>"",
                    "large"=>"",
                    "extraLarge"=>""
                ),
                "backgroundMusicUrl"=>"",
                "tags"=>explode(",", $modulesetting->tags),
                "xPos"=>$modulesetting->myxpos,
                "yPos"=>$modulesetting->myypos,
                "dialogue"=>self::get_dialogs("module", $module->id),
                "characterImages"=>array(
                    "brightness"=>$modulesetting->brightness,
                    "mainCharacter"=>array(
                        "mainCharacter"=>$modulesetting->maincharacter,
                        "imageUrl"=>self::get_childImage(),
                        "region"=>"",
                        "provinces"=>"",
                        "xPos"=>floatval($modulesetting->mainxpos),
                        "yPos"=>floatval($modulesetting->mainypos),
                        "narrationSettings" => self::get_natrationsetting($modulesetting->maincharacter)
                    ),
                    "secondaryCharacter"=>array(
                        "secondaryCharacter"=>$modulesetting->secondarycharacter,
                        "imageUrl"=>self::get_deginersfiles("character", "imageurl", $modulesetting->secondarycharacter),
                        "region"=>"",
                        "provinces"=>"",
                        "xPos"=>floatval($modulesetting->secondaryxpos),
                        "yPos"=>floatval($modulesetting->secondaryypos),
                        "narrationSettings" => self::get_natrationsetting($modulesetting->secondarycharacter)
                    )
                ),
                "totalQuestions"=>0,
                "completedQuestions"=>0,
                "bestGrade"=>(empty($bestgrade)?"":number_format($bestgrade, 2)),
                "summary"=>array(),
                "rewards"=>array(),
                "materials"=>array(),
                "questions"=>array(),
                "completed"=>$iscompleted,
                "timed"=>!empty($quiz->timelimit),
                "totalTime"=>intval($quiz->timelimit),
            );
            $allexistingattempt = array();
            $lastgrade = "";
            $quiz_attempt_last = "";
            // $attempts = quiz_get_user_attempts($quiz->id, $USER->id, 'all', true);
            $attemptedAfterExpiry = false;
            $homework = $DB->get_record_sql("SELECT h.* FROM mdl_homework h INNER JOIN mdl_institution_group_member igm on h.groupid = igm.groupid AND h.schoolyear = igm.schoolyear WHERE igm.userid=? and h.quiz=? and h.status = 1 and igm.status=1 and igm.schoolyear=?", array($USER->id, $module->cm, $this->currentschoolyear));
            $moduledata['isTarl'] = false;
            if(in_array($quiz->course, $this->publicflowcourse)){
                $moduledata['isTarl'] = true;
            }

            $moduledata['isHomework'] = false;
            $moduledata['homeworkType'] = 0;
            $moduledata['homeworkId'] = 0;
            $moduledata['disableHints'] = false;
            $moduledata['disableTimer'] = false;
            $moduledata['blockedQuestions'] = false;
            $moduledata['disableExplanation'] = false;
            $moduledata['disableExplanation'] = false;
            $moduledata['immediateFeedback'] = false;
            $moduledata['homeworkFilterArea'] = 0;
            $moduledata['homeworkStudents'] = array();
            if($subjectdata = $DB->get_record_sql("SELECT cf.shortname, cd.instanceid as courseid, cd.value, cf.configdata FROM  mdl_customfield_field cf LEFT JOIN  mdl_customfield_data cd on cf.id=cd.fieldid and cd.instanceid = ? WHERE cf.shortname=?", array($course->id, "coursetype"))){
                $subjectdata->configdata = json_decode($subjectdata->configdata);
                $subjectdata->configdata->optionlist = explode("\r\n", $subjectdata->configdata->options);
                $moduledata["subject"] = !empty($subjectdata->value)?$subjectdata->configdata->optionlist[$subjectdata->value-1]:$subjectdata->configdata->defaultvalue;
            }

            if($homework){
                $moduledata['isHomework'] = true;
                $moduledata['homeworkType'] = $homework->type;
                $moduledata['homeworkId'] = $homework->id;
                $moduledata['blockedQuestions'] = $this->get_blockedquestions($homework->id);
                $moduledata['disableTimer'] = $homework->disabletimer?true:false;
                $moduledata['disableHints'] = $homework->disablehints?true:false;
                $moduledata['disableExplanation'] = $homework->disableexplanation?true:false;
                $moduledata['disableTranslation'] = $homework->disabletranslation?true:false;
                $moduledata['immediateFeedback'] = $homework->immediatefeedback?true:false;
                $moduledata['homeworkFilterArea'] = $homework->filterarea;
                $moduledata['homeworkStudents'] = explode(",", $homework->students);
            }
            /*foreach ($attempts as $key => $attempt) {
                if(!$DB->record_exists("quizattempts", array("schoolyear"=>$this->currentschoolyear,"courseid"=>$course->id, "topicid"=>$topic->id, "moduleid"=>$module->cm, "instance"=>$quiz->id, "attemptid"=>$attempt->id))){
                    continue;
                }
                // $attempt->questions = self::get_questions($attempt);
                $attempt->questions = array();
                $attempt->currentPage = $attempt->currentpage;
                $attempt->timeStart = $attempt->timestart;
                $attempt->timeFinish = $attempt->timefinish;
                $attempt->timeModified = $attempt->timemodified;
                $attempt->timeModifiedOffline = $attempt->timemodifiedoffline;
                $attempt->timeCheckState = $attempt->timecheckstate;
                $attempt->maxScore = $CATGRADE;
                $attempt->isHomework = $moduledata['isHomework'];
                $attempt->homeworkType = $moduledata['homeworkType'];
                $attempt->disableHints = $moduledata['disableHints'];
                $attempt->disableExplanation = $moduledata['disableExplanation'];
                $attempt->disableTranslation = $moduledata['disableTranslation'];
                $attempt->quizGrade = number_format($quiz->grade, 2);
                $attempt->quizSumGrades = number_format($quiz->sumgrades, 2);
                $attempt->quizGotGrades = number_format($attempt->sumgrades, 2);
                $attempt->attemptedAfterExpiry = $DB->record_exists_sql("SELECT qa.*, qa1.timefinish, h.duedate, IF(h.duedate<qa1.timefinish, 1, 0) as withintime 
                    FROM mdl_quizattempts qa 
                    INNER JOIN mdl_quiz_attempts qa1 on qa.attemptid = qa1.id 
                    INNER JOIN mdl_homework h on h.quiz=qa.moduleid
                    INNER JOIN mdl_institution_group_member igm on h.groupid = igm.groupid 
                    AND igm.userid = qa.userid AND igm.status=1 

                    WHERE h.duedate<qa1.timefinish and qa.attemptid=?", array($attempt->id));

                // $attempt->sumGrades = $attempt->sumgrades;
                $attempt->sumGrades = number_format(round((floatval($attempt->sumgrades)/floatval($quiz->sumgrades))*floatval($quiz->grade), 4),2);
                unset($attempt->currentpage);
                unset($attempt->timestart);
                unset($attempt->timefinish);
                unset($attempt->timemodified);
                unset($attempt->timemodifiedoffline);
                unset($attempt->timecheckstate);
                unset($attempt->sumgrades);
                array_push($allexistingattempt, $attempt);
                if($attempt->state == "finished"){
                    $lastgrade = $attempt->sumGrades;
                    $attemptedAfterExpiry = $attempt->attemptedAfterExpiry;
                }
            }*/
            $sqqq = "SELECT * FROM {quiz_attempts} WHERE userid='$USER->id' AND quiz='$quiz->id' AND state='finished' ORDER BY id DESC LIMIT 1";
            $quiz_attempt_last = $DB->get_record_sql($sqqq, array());
            if($quiz_attempt_last){
                $lastgrade = number_format(round((floatval($quiz_attempt_last->sumgrades)/floatval($quiz->sumgrades))*floatval($quiz->grade), 4),2);
            }

            $moduledata['attemptedAfterExpiry'] = $attemptedAfterExpiry;
            $moduledata['lastGrade'] = $lastgrade;;
            // $moduledata['bbbbbbb'] = $sqqq;
            $moduledata['summary'] = $allexistingattempt;
            // $moduledata['summary'] = array();
            $moduledata['started'] = (!empty($quiz_attempt_last));
            $cm = get_fast_modinfo($course, $USER->id)->get_cm($module->cm);

            $moduledata['locked'] = !$cm->uservisible;
            $moduledata['restricted'] = self::isrestricted('quiz', $module->cm);
            // if($module->cm == 5996){
            //     print_r($moduledata);
            //     die;
            // }
            $moduledata['restrictedcheck'] = $TOPICTYPE;
            if($moduledata['restricted']){
                $moduledata['locked'] = $moduledata['restricted'];
            }
            if($TOPICTYPE == "quest" && $this->missionMode == false){
                $moduledata['locked'] = true;
            }
            if(!self::isInternalUser($course->id, $USER->id) && !$this->premiumAccount && $cm->uservisible && $quiz->accessibility && $TOPICTYPE == "quest"){
                $moduledata['locked'] = true;
                $moduledata['locked1'] = true;
            }
            $moduledata["status"] = self::getMyStatus($moduledata["locked"], $moduledata["completed"], $moduledata["started"], $moduledata["bestGrade"]);

        // }
        return $moduledata;
    }
    public function get_natrationsetting($characterid){
        global $DB, $CFG;
        $naration = array(
            "language"=>"",
            "voiceStyle"=>"",
            "speechRate"=>0,
            "pitch"=>0,
        );
        if($character = $DB->get_record_sql("SELECT n.* from mdl_narationsetting n INNER JOIN mdl_charactersetting ch on ch.naration = n.id where ch.id-? ", array($characterid))){
            $naration['language'] = $character->language;
            $naration['voiceStyle'] = $character->voicestyle;
            $naration['speechRate'] = intval($character->speechrate);
            $naration['pitch'] = intval($character->pitch);
        }
        return $naration;
    }
    public function get_deginersfiles($component, $filearea, $itemid){
        global $DB, $CFG;
        $imageurl = "";
        // $staticimage = "https://app.fivestudents.com/local/designer/file.php?id=cff7064adc796307ea809df30c6471a71fc8641f";
        // if(in_array($filearea, array("backgroundimagesmall","backgroundimagemedium", "backgroundimagelarge", "backgroundimage")) ){
        //     $imageurl = $staticimage;
        // } else {
        $nullablecomponent = array("potrait", "question");
            $filedata  = $DB->get_record_sql("select f.filename as image, f.pathnamehash from {files} as f where f.itemid = ? and f.component = ? and f.filearea = ? and filename !='.'", array($itemid, $component, $filearea));
            if(!empty($filedata)){
                $imageurl = $CFG->wwwroot."/local/designer/file.php?id=".$filedata->pathnamehash."&filename=".$filedata->image;
            } else if(in_array($component, $nullablecomponent)) {
                $imageurl = null;
            } else if(strpos($filearea, "dimage")) {
                $imageurl = $CFG->wwwroot."/local/designer/images/default-background.jpg";
            } else if(strpos($filearea, "music")) {
                $fileurl = $CFG->wwwroot."/local/designer/images/default-backgroundmusic.mp3";
            }
        // }
        return $imageurl;
    }
    public function get_dialogs($level, $itemid){
        global $DB, $CFG;
        $alldialogs = array();
        $categoryid = 0;
        $courseid = 0;
        $topicid = 0;
        $moduleid = 0;
        $alldialogdata = array();
        // switch ($level) {
        //     case 'module':
        //         $alldialogdata = $DB->get_records("dialogsetting", array("moduleid"=>$itemid, "lang"=>$this->userlangauge));
        //         break;
        //     case 'topic':
        //         $alldialogdata = $DB->get_records("dialogsetting", array("topic"=>$itemid, "moduleid"=>0, "lang"=>$this->userlangauge));
        //         break;
        //     case 'course':
        //         $alldialogdata = $DB->get_records("dialogsetting", array("course"=>$itemid, "topic"=>0, "moduleid"=>0, "lang"=>$this->userlangauge));
        //         break;
        //     case 'category':
        //         $alldialogdata = $DB->get_records("dialogsetting", array("category"=>$itemid, "course"=>0, "topic"=>0, "moduleid"=>0, "lang"=>$this->userlangauge));
        //         break;
        //     default:
        //         break;
        // }
        foreach ($alldialogdata as $key => $data) {
            $dataarr = array();
            $dataarr['dialogueId']= $data->id;
            $dataarr['dialogueAudioFile']= self::get_deginersfiles("dialog", "audiofile", $data->id);
            $dataarr['dialogType']= $data->dialogtype;
            $dataarr['message']= $data->message;
            $dataarr['sender']= $data->sender;
            array_push($alldialogs, $dataarr);
        }
        return $alldialogs;
    }
    public function get_designer_narration($level, $itemid, $lang = null){
        global $DB, $CFG;
        if(empty($lang)){
            $lang = $this->userlangauge;
        }
        $alldialogs = array();
        $categoryid = 0;
        $courseid = 0;
        $topicid = 0;
        $moduleid = 0;
        $designerdata = null;
        switch ($level) {
            case 'module':
                $designerdata = $DB->get_record("designer_narration", array("moduleid"=>$itemid, "lang"=>$lang));
                if(empty($designerdata) && $this->userlangauge != 'fr'){
                    $designerdata = $DB->get_record("designer_narration", array("moduleid"=>$itemid, "lang"=>"fr"));
                }
                break;
            case 'topic':
                $designerdata = $DB->get_record("designer_narration", array("topic"=>$itemid, "moduleid"=>0, "lang"=>$lang));
                if(empty($designerdata) && $this->userlangauge != 'fr'){
                    $designerdata = $DB->get_record("designer_narration", array("topic"=>$itemid, "moduleid"=>0, "lang"=>"fr"));
                }
                break;
            case 'course':
                $designerdata = $DB->get_record("designer_narration", array("course"=>$itemid, "topic"=>0, "moduleid"=>0, "lang"=>$lang));
                if(empty($designerdata) && $this->userlangauge != 'fr'){
                    $designerdata = $DB->get_record("designer_narration", array("course"=>$itemid, "topic"=>0, "moduleid"=>0, "lang"=>"fr"));
                }
                break;
            case 'category':
                $designerdata = $DB->get_record("designer_narration", array("category"=>$itemid, "course"=>0, "topic"=>0, "moduleid"=>0, "lang"=>$lang));
                if(empty($designerdata) && $this->userlangauge != 'fr'){
                    $designerdata = $DB->get_record("designer_narration", array("category"=>$itemid, "course"=>0, "topic"=>0, "moduleid"=>0, "lang"=>"fr"));
                }
                break;
            default:
                echo "string";
                break;
        }
        return $designerdata;
    }
    public function getTarlQuizData($args){
        global $DB, $CFG, $USER, $PARENTUSER, $CATGRADE, $Qposition;
        $currentChild = $PARENTUSER->currentChild;
        $gradeid = $currentChild->grade;

        $moduleid = $args['moduleid'];
        if(empty($moduleid)){
            $this->sendError("Invalid Module", "Module not Found");
        } else {
            $section = $DB->get_record_sql("SELECT cs.*, tc.status FROM {course_sections} cs LEFT JOIN {tarltopics_completion} tc on tc.courseid=cs.course AND tc.topicid=cs.id AND tc.userid=:userid WHERE cs.id=:id and cs.visible=:visible", array("id"=>$moduleid, "visible"=>1, "userid"=>$USER->id));
            if(empty($section)){
                $this->sendError("Invalid Module", "Module not Found");
            } else if(!in_array($section->course, $this->publicflowcourse)){
                $this->sendError("Invalid Module", "Invalid module ID");
            } else {
                if(!$DB->record_exists("tarldiagnosticviewed", array("courseid"=>$section->course, "userid"=>$USER->id))){
                    $data = new stdClass();
                    $data->userid = $USER->id;
                    $data->courseid = $section->course;
                    $data->viewtime = time();
                    $DB->insert_record("tarldiagnosticviewed", $data);
                }
                $existing = $DB->get_record_sql("SELECT * FROM {tarldiagnostic} WHERE courseid=:courseid and userid=:userid and maintopic=:topicid AND timefinish IS NULL ORDER BY id desc limit 0, 1", array("courseid"=>$section->course, "userid"=>$USER->id, "topicid"=>$section->id));
                if(empty($existing)){
                    $existing = $DB->get_record_sql("SELECT * FROM {tarldiagnostic} WHERE courseid=:courseid and userid=:userid and topic=:topicid AND timefinish IS NULL ORDER BY id desc limit 0, 1", array("courseid"=>$section->course, "userid"=>$USER->id, "topicid"=>$section->id));
                }
                if($existing){
                    if($existing->quiz){
                        $quizid = $existing->quiz;
                    } else {
                        if($existing->diagnostic == 1){
                            $modules = $DB->get_fieldset_sql("SELECT cm.id FROM {course_modules} cm INNER JOIN {modules} m on m.id=cm.module AND m.name='quiz' INNER JOIN {quiz} q on q.id=cm.instance where cm.section=:sectionid AND cm.deletioninprogress=0 AND cm.visible=1 AND (q.forgrades=0 OR q.forgrades IS NULL OR FIND_IN_SET(:quizforgrades,q.forgrades))", array("sectionid"=> $existing->topic, "quizforgrades"=>$gradeid));
                            if(empty($modules)){
                                $modules = $DB->get_fieldset_sql("SELECT cm.id FROM {course_modules} cm INNER JOIN {modules} m on m.id=cm.module AND m.name='quiz' INNER JOIN {quiz} q on q.id=cm.instance  where cm.section=:sectionid AND cm.deletioninprogress=0 AND cm.visible=1", array("sectionid"=> $existing->topic));
                            }
                        } else {
                            $modules = $DB->get_fieldset_sql("SELECT cm.id FROM {course_modules} cm INNER JOIN {modules} m on m.id=cm.module AND m.name='quiz' INNER JOIN {quiz} q on q.id=cm.instance where cm.section=:sectionid AND cm.deletioninprogress=0 AND cm.visible=1 AND (q.forgrades=0 OR q.forgrades IS NULL OR FIND_IN_SET(:quizforgrades,q.forgrades))", array("sectionid"=> $existing->topic, "quizforgrades"=>$gradeid));
                            if(empty($modules)){
                                $modules = $DB->get_fieldset_sql("SELECT cm.id FROM {course_modules} cm INNER JOIN {modules} m on m.id=cm.module AND m.name='quiz' INNER JOIN {quiz} q on q.id=cm.instance  where cm.section=:sectionid AND cm.deletioninprogress=0 AND cm.visible=1", array("sectionid"=> $existing->topic));
                            }
                        }
                        if(empty($modules)){
                            $this->sendError("Invalid Module", "Module Not yet available");
                        } else {
                            $key = array_rand($modules);
                            $quizid = $modules[$key];
                            $existing->quiz = $quizid;
                            $existing->timestart = time();
                            $DB->update_record("tarldiagnostic", $existing);
                        }
                    }
                } else {
                    if($section->status == 1){
                        $modules = $DB->get_fieldset_sql("SELECT cm.id FROM {course_modules} cm INNER JOIN {modules} m on m.id=cm.module AND m.name='quiz' INNER JOIN {quiz} q on q.id=cm.instance where cm.section=:sectionid AND cm.deletioninprogress=0 AND cm.visible=1", array("sectionid"=> $section->id));
                        if(empty($modules)){
                            $modules = $DB->get_fieldset_sql("SELECT cm.id FROM {course_modules} cm INNER JOIN {modules} m on m.id=cm.module AND m.name='quiz' INNER JOIN {quiz} q on q.id=cm.instance  where cm.section=:sectionid AND cm.deletioninprogress=0 AND cm.visible=1", array("sectionid"=> $section->id));
                        }
                        $topicdata= $DB->get_record_sql("SELECT cs.id, cs.name, cs.section, cs.course, cs.isdiagnostic, cs1.id as parentid, cs1.section as parentsection, cs1.name as parentname, cs1.isdiagnostic as parentisdiagnostic FROM mdl_course_sections cs INNER JOIN mdl_course_format_options cfo ON cfo.courseid = cs.course AND cfo.sectionid =cs.id AND cfo.name = 'parent' INNER JOIN mdl_course_sections cs1 ON cs1.course = cfo.courseid AND cs1.section = cfo.value WHERE cs.id=:id", array("id"=>$section->id));
                        if(sizeof($modules)){
                            $d = new stdClass();
                            $d->userid = $USER->id;
                            $d->schoolyear = $this->currentschoolyear;
                            $d->courseid = $section->course;
                            $d->topic = $section->id;
                            $d->maintopic = $section->id;
                            $d->maintopicparent = $topicdata->parentid;
                            $d->diagnostic = $section->isdiagnostic;
                            $d->createdby = $USER->id;
                            $d->createddate = time();
                            $key = array_rand($modules);
                            $quizid = $modules[$key];
                            $d->quiz = $quizid;
                            $d->timestart = time();
                            $d->id = $DB->insert_record("tarldiagnostic",$d);
                            $existing = $d;
                        }
                    }
                }
                if($quizid){
                    $args['moduleid']=$quizid;
                    $args['tarldiagnostic']=$existing;
                    $this->isTarl = true;
                    $this->marktarlstatus($existing->topic, 1);
                    $this->getQuizData($args);
                } else {
                    $this->sendError("Invalid Module", "Module Not found.");
                }
            }
        }
    }
    public function getQuizData($args){
        global $DB, $CFG, $USER, $PARENTUSER, $CATGRADE, $Qposition;
        $maxScore = $this->get_categorygrade();
        if(!empty($PARENTUSER->currentChild)){
            $currentChild = $PARENTUSER->currentChild;

            require_once($CFG->dirroot.'/mod/quiz/locallib.php');
            require_once($CFG->dirroot.'/mod/quiz/lib.php');
            // require_once($CFG->libdir . '/completionlib.php');
            // require_once($CFG->dirroot . '/course/format/lib.php');
            $moduleid = $args['moduleid'];
            if (!$cm = get_coursemodule_from_id('quiz', $moduleid)) {
                $this->sendError("Invalid Module", "Module not Found");
            } else if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
                $this->sendError("Invalid access to course", "Invalid access to course");
            } else if (!$section = $DB->get_record('course_sections', array('id' => $cm->section))) {
                $this->sendError("Invalid access to component", "Invalid access to component");
            } else {
                $tarldiagnostic_maintopic = 0;
                $tarlattempt = 0;
                $istarlattempt = 0;
                $tarlpreview = 0;
                if(isset($args['tarldiagnostic']) && !empty($args['tarldiagnostic'])){
                    $tarldiagnostic = $args['tarldiagnostic'];
                    $istarlattempt = true;
                    $tarldiagnostic_maintopic = $tarldiagnostic->maintopic;
                    $tarlpreview = $tarldiagnostic->preview;
                    if (!$section = $DB->get_record('course_sections', array('id' => $tarldiagnostic->maintopic))){
                        $this->sendError("Invalid access to component", "Invalid access to component");
                        return;
                    }
                    $tarlattempt = $DB->get_field_sql("SELECT COUNT(id) FROM {tarldiagnostic} where userid=:userid and schoolyear=:schoolyear AND maintopic=:maintopic", array("userid"=>$USER->id, "schoolyear"=>$this->currentschoolyear, "maintopic"=>$tarldiagnostic->maintopic));
                }

                if(!empty($course->lang)){
                    $this->courselangauge = $course->lang;
                }
                // require_once($CFG->libdir . "/modinfolib.php");
                // $modinfo = get_fast_modinfo($course);
                // $modinfocm = $modinfo->get_cm($moduleid);
                $module = new stdClass();
                $module->id = $cm->instance;
                $module->cm = $cm->id;
                $module->name = $cm->name;
                $module->mod = $cm->modname;
                // print_r($cm);
                $topic = $DB->get_record("course_sections", array("id"=>$cm->section));
                $quizdata = self::get_quiz($course, $topic, $module);
                $quizaccessed = new stdClass();
                $quizaccessed->userid = $USER->id;
                $quizaccessed->courseid = $cm->course;
                $quizaccessed->topicid = $cm->section;
                $quizaccessed->moduleid = $cm->id;
                $quizaccessed->instance = $cm->instance;
                $quizaccessed->schoolyear = $this->currentschoolyear;
                $quizaccessed->accessedtime = time();
                // $DB->insert_record("quizaccessed", $quizaccessed);
                //         if(empty($quizdata)){
                            // $this->sendError("Quiz not yet available", "Quiz not yet available");
                //         } else {

                    $quizobj = quiz::create($cm->instance, $USER->id);
                    $quiz = $quizobj->get_quiz();
                    $context = context_module::instance($cm->id);
                    quiz_view($quiz, $course, $cm, $context);
                    if (!$quizobj->has_questions()) {
                        $this->sendError("Questions not found", "Questions not found");
                    } else {
                        $allexistingattempt = array();
                        // $attempts = quiz_get_user_attempts($quiz->id, $USER->id, 'all', true);
                        $lastgrade = "";

                        $timenow = time();
                        // $quizdata['summary'] = $allexistingattempt;
                        $quizdata['summary'] = array();
                        $accessmanager = $quizobj->get_access_manager($timenow);
                        list($currentattemptid, $attemptnumber, $lastattempt, $messages, $page) = quiz_validate_new_attempt($quizobj, $accessmanager, $forcenew, -1, false);
                        if (!$quizobj->is_preview_user() && $messages) {
                            $questions = [];
                        } else {
                            if ($currentattemptid) {
                                $localattempt = $DB->get_record("quiz_attempts", array("id"=>$currentattemptid));
                            } else {
                                $localattempt = quiz_prepare_and_start_new_attempt($quizobj, $attemptnumber, $lastattempt);
                            }
                            $localattempt->userId = $localattempt->userid;
                            $localattempt->uniqueid = $localattempt->uniqueid;
                            $localattempt->tarldiagnosticMaintopic = $tarldiagnostic_maintopic;
                            $localattempt->tarlattempt = $tarlattempt;
                            $localattempt->istarlattempt = $istarlattempt;
                            $localattempt->tarlpreview = $tarlpreview;
                            $localattempt->currentPage = $localattempt->currentpage;
                            $localattempt->sumGrades = $localattempt->sumgrades;
                            $localattempt->timeStart = intval($localattempt->timestart);
                            $localattempt->timeFinish = intval($localattempt->timefinish);
                            $localattempt->timeModified = intval($localattempt->timemodified);
                            $localattempt->timeCheckState = intval($localattempt->timecheckstate);
                            $localattempt->timeModifiedOffline = intval($localattempt->timemodifiedoffline);
                            $localattempt->isHomework = $quizdata['isHomework'];
                            $localattempt->lang = $quizdata['lang'];
                            $localattempt->homeworkType = $quizdata['homeworkType'];
                            $localattempt->disableHints = $quizdata['disableHints'];
                            $localattempt->disableExplanation = $quizdata['disableExplanation'];
                            // $localattempt->attemptedAfterExpiry = $DB->record_exists_sql("SELECT qa.id  FROM mdl_quizattempts qa INNER JOIN mdl_quiz_attempts qa1 on qa.attemptid = qa1.id INNER JOIN mdl_homework h on h.quiz=qa.moduleid INNER JOIN mdl_institution_group_member igm on h.groupid = igm.groupid AND igm.userid = qa.userid AND igm.status=1  WHERE h.duedate < qa1.timestart and qa.attemptid=?", array($localattempt->id));
                            $localattempt->attemptedAfterExpiry = false;
                            $quizdata['attemptedAfterExpiry'] = $localattempt->attemptedAfterExpiry;
                            $yeardata = new stdClass();
                            $yeardata->userid=$USER->id;
                            $yeardata->courseid =$course->id;
                            $yeardata->topicid=$topic->id;
                            $yeardata->moduleid=$module->cm;
                            $yeardata->schoolyear=$this->currentschoolyear;
                            $yeardata->instance=$quiz->id;
                            $yeardata->attemptid=$localattempt->id;
                            $yeardata->attemptno=$localattempt->attempt;
                            if(!$DB->record_exists("quizattempts", array("attemptid"=> $localattempt->id))){
                                $DB->insert_record("quizattempts", $yeardata);
                            }

                            $allquestions = self::get_questions($localattempt);
                            $localattempt->totalQuestions = $Qposition;
                        //     // $localattempt->lastAttempted = self::get_lastAttemptedquestion($localattempt, $allquestions);

                            unset($localattempt->userid);
                            unset($localattempt->uniqueid);
                            unset($localattempt->currentpage);
                            unset($localattempt->sumgrades);
                            unset($localattempt->timestart);
                            unset($localattempt->timefinish);
                            unset($localattempt->timemodified);
                            unset($localattempt->timecheckstate);
                            unset($localattempt->timemodifiedoffline);
                            $quizdata['current'] = $localattempt;
                            $quizdata['questions'] = $allquestions;
                        }
                        /*foreach ($attempts as $key => $attempt) {
                            if(!$DB->record_exists("quizattempts", array("schoolyear"=>$this->currentschoolyear,"courseid"=>$course->id, "topicid"=>$topic->id, "moduleid"=>$module->cm, "instance"=>$quiz->id, "attemptid"=>$attempt->id))){
                                continue;
                            }

                            // $attempt->questions = self::get_questions($attempt);
                            $attempt->questions = array();
                            $attempt->totalQuestions = $Qposition;
                            $attempt->currentPage = $attempt->currentpage;
                            $attempt->timeStart = intval($attempt->timestart);
                            $attempt->timeFinish = intval($attempt->timefinish);
                            $attempt->timeModified = intval($attempt->timemodified);
                            $attempt->timeModifiedOffline = intval($attempt->timemodifiedoffline);
                            $attempt->timeCheckState = intval($attempt->timecheckstate);
                            $attempt->maxScore = $CATGRADE;
                            $attempt->isHomework = $quizdata['isHomework'];
                            $attempt->homeworkType = $quizdata['homeworkType'];
                            $attempt->disableHints = $quizdata['disableHints'];
                            $attempt->disableExplanation = $quizdata['disableExplanation'];
                            // $attempt->sumGrades = $attempt->sumgrades;
                            $attempt->sumGrades = number_format(round((floatval($attempt->sumgrades)/floatval($quiz->sumgrades))*floatval($quiz->grade), 4),2);
                            unset($attempt->currentpage);
                            unset($attempt->timestart);
                            unset($attempt->timefinish);
                            unset($attempt->timemodified);
                            unset($attempt->timemodifiedoffline);
                            unset($attempt->timecheckstate);
                            unset($attempt->sumgrades);
                            if($attempt->state == "finished"){
                                $lastgrade = $attempt->sumGrades;
                                $attempt->attemptedAfterExpiry = $DB->record_exists_sql("SELECT qa.*, qa1.timefinish, h.duedate, IF(h.duedate<qa1.timefinish, 1, 0) as withintime 
                                FROM mdl_quizattempts qa 
                                INNER JOIN mdl_quiz_attempts qa1 on qa.attemptid = qa1.id 
                                INNER JOIN mdl_homework h on h.quiz=qa.moduleid
                                INNER JOIN mdl_institution_group_member igm on h.groupid = igm.groupid 
                                AND igm.userid = qa.userid AND igm.status=1 

                                WHERE h.duedate<qa1.timefinish and qa.attemptid=?", array($attempt->id));
                            }
                            array_push($allexistingattempt, $attempt);
                        }*/
                        $sqq = "SELECT * FROM {quiz_attempts} WHERE userid='$USER->id' AND quiz='$quiz->id' AND state='finished' ORDER BY id DESC LIMIT 1";
                        $quiz_attempt_last = $DB->get_record_sql($sqq, array());

                        $quizdata['lastGrade'] = $quiz_attempt_last ? $quiz_attempt_last->sumgrades : "";
                        // $moduledata['aaaaaaa'] = $sqq;
                        $quizdata['tarlAttempt'] = $tarlattempt;
                        $quizdata['remainingDays'] = $this->remainingDays;
                        $quizdata['premiumAccount'] = $this->premiumAccount;
                        $quizdata['isTarl'] = $this->isTarl?$this->isTarl:false;

                        $quizdata['totalParty'] = 0;
                        $quizdata['completedParty'] = 0;
                        $quizdata['completedPercent'] = "0";
                        $quizdata['section'] = $section->id;
                        $quizdata['isDiagnostic'] = $section->isdiagnostic;
                        if($quizdata['isTarl']){
                            if(!$section->isdiagnostic){
                                $childs = array_values($DB->get_records_sql("SELECT cs.*, tc.status FROM mdl_course_format_options cfo1 LEFT JOIN mdl_course_format_options cfo2 ON cfo1.name=cfo2.name AND cfo1.courseid=cfo2.courseid AND cfo1.value=cfo2.value INNER JOIN mdl_course_sections cs ON cfo2.sectionid=cs.id and cs.course=cfo2.courseid LEFT JOIN mdl_tarltopics_completion tc ON cs.course=tc.courseid AND cs.id=tc.topicid AND tc.userid=:userid WHERE cfo1.sectionid = :section AND cfo1.courseid = :course AND cfo1.name = 'parent'", array("course"=>$section->course, "section"=>$section->id, "userid"=>$USER->id)));
                                $quizdata['totalParty'] = sizeof($childs);
                                $quizdata['totalPartyElements'] = $childs;
                                if(sizeof($childs)){
                                    $totalstatus = array_column($childs, 'status');
                                    $statuscount = array_count_values($totalstatus);
                                    if(isset($statuscount[2])){
                                        $quizdata['completedParty'] = $statuscount[2];
                                    }
                                }
                            }
                            if($quizdata['completedParty'] > 0){
                                $quizdata['completedPercent'] = intval(($quizdata['completedParty']/$quizdata['totalParty'])*100);
                            }
                        }
                        $quizdata['premiumAccountExpiry'] = $this->premiumAccountExpiry;
                        $quizdata['premiumAccountExpired'] = $this->premiumAccountExpired;
                        $this->sendResponse($quizdata);
                    }
                // }

            }
        } else {
            $this->sendError("Failed", "Please try through Child Account");
        }
    }
    private function get_lastAttemptedquestion($localattempt, $allQuestions){
        global $DB;
        $lastattempted = 0;
        if(!empty($localattempt)){
           $lastattemptedquestionid = $DB->get_field_sql('SELECT qas.questionattemptid FROM {question_attempt_steps} qas INNER JOIN {question_attempts} qa ON qa.id = qas.questionattemptid INNER JOIN {question_attempt_step_data} qasd on qasd.attemptstepid = qas.id and (qasd.name like "%answer" or qasd.name like "P%" or qasd.name like "choice%") where qa.questionusageid = ? order by qas.id desc limit 0, 1', array($localattempt->uniqueid));
            foreach ($allQuestions as $Questions) {
                $lastattempted++;
                if($Questions['id'] == $lastattemptedquestionid){
                    break;
                }
            }
        }        return $lastattempted;
    }
    public function get_questions($localattempt){
        global $DB, $CFG, $USER, $TOGGLEDESC, $Qposition, $TANSLATIONQ;
        require_once($CFG->dirroot."/mod/quiz/lib.php");
        require_once($CFG->dirroot."/lib/filelib.php");
        $allquestion = array();
        if(empty($localattempt)){
            return $allquestion;
        }
        $qry="select q.id as 'qid',qa.id as 'id',q.name as 'name', q.questiontext as 'questiontext', q.qtype as 'qtype', qa.slot as 'slot', qc.contextid, qa.questionusageid, q.desctoogle, q.questioninstruction, q.questioninstructionaudio, q.generalfeedback, qa.maxmark, qa.maxfraction, qa.rightanswer, qa.responsesummary, q.translation, q.questionhint, q.questionhintaudio, q.timelimit, q.strands, q.competencies from {question_attempts} as qa left join {question} as q on qa.questionid = q.id INNER JOIN {question_categories} as qc ON qc.id=q.category where qa.questionusageid=?";
        $query_fetch_question = $DB->get_recordset_sql($qry, array($localattempt->uniqueid));
        $Qposition = 0;
        $gradearray = array("gaveup", "gradedwrong", "gradedright", "gradedpartial", "needsgrading");
        if(empty($localattempt->tarlattempt)){
            $localattempt->tarlattempt=1;
        }
        foreach ($query_fetch_question as $question) {

            $getquestionfunction = "getqtype_".$question->qtype;
            if(empty($question->questioninstruction)){
                $question->questioninstruction = "";
            }
            if (method_exists($this, $getquestionfunction)) {
                // echo $question->qtype."\n";
                if($questiondata = self::$getquestionfunction($question, $localattempt)){
                    if($question->qtype == "description" && (empty(trim(strip_tags($question->questiontext, array("<img>","<video>", '<a>')))) || trim(strip_tags($question->questiontext, array("<img>","<video>", '<a>'))) == '-' || trim(strip_tags($question->questiontext, array("<img>","<video>", '<a>'))) == '--')){
                        continue;
                    }
                    if($question->qtype == "description"){
                        if(!empty($questiondata['questionVideo'])){
                            if($localattempt->tarlattempt % 2 == 0 || $localattempt->tarlpreview){
                                $this->videoSkipped = true;
                                $this->videoSkippedurl = $questiondata['questionVideo'];
                                continue;
                            }
                        }
                    }
                    if($question->qtype == "multianswer" && $localattempt->lang == "fr"){
                        // $questiondata['questionText'] = str_replace('<table dir="rtl"', "<table", $questiondata['questionText']);
                    }
                    $Qposition++;
                    $statusdata = $DB->get_record_sql("SELECT * FROM `mdl_question_attempt_steps` WHERE questionattemptid = ? order BY id desc limit 0, 1", array($question->id));
                    $status = -1;
                    $marks = "0.00";
                    $marksfracetion = "0.00";
                    if(!empty($statusdata)){
                        $status = (in_array($statusdata->state, $gradearray)?array_search($statusdata->state, $gradearray):-1);
                        if($statusdata->fraction !== NULL){
                            $marks = (floatval($question->maxmark) * floatval($statusdata->fraction))/floatval($question->maxfraction);
                        } else {
                            $statusdata->fraction = "0.00";
                        }
                        $marksfracetion = $statusdata->fraction;
                    }

                    if(empty($questiondata['questionVideo'])){
                        $questiondata['questionVideo'] = "";
                    }
                    $questiondata['position'] = $Qposition;
                    $questiondata['haveToggle'] = false;
                    $questiondata['toggleQuestion'] = "";
                    $questiondata['timeLimit'] = !empty($question->timelimit)?intval($question->timelimit)*60:intval($question->timelimit);
                    $questiondata['timed'] = !empty($questiondata['timeLimit']);
                    $questiondata['strands'] = $question->strands;
                    $questiondata['competencies'] = $question->competencies;
                    $questiondata['rightAnswer'] = $question->rightanswer;
                    $questiondata['responseSummary'] = $question->responsesummary;
                    $questiondata['marks'] = number_format($marks, 2);
                    $questiondata['marksFraction'] = number_format($marksfracetion, 2);
                    $questiondata['maxMarks'] = number_format($question->maxmark, 2);
                    $questiondata['maxFraction'] = number_format($question->maxfraction, 2);
                    $questiondata['status'] = $status;
                    if($localattempt->disableHints){$questiondata['questionHint'] = NULL;}
                    if($localattempt->disableExplanation){$questiondata['generalFeedback'] = NULL;}
                    if($localattempt->disableTranslation || $localattempt->istarlattempt){
                        $questiondata['haveTranslation'] = false;
                        $questiondata['translation'] = null;
                        $TANSLATIONQ = null;
                    } else if(!empty($TANSLATIONQ)){
                        $questiondata['haveTranslation'] = !empty($TANSLATIONQ)?true:false;
                        $questiondata['translation'] = $TANSLATIONQ;
                        $TANSLATIONQ = null;
                    }
                    if($question->desctoogle){
                        $questiondata['haveToggle'] = !empty($TOGGLEDESC)?true:false;
                        $questiondata['toggleQuestion'] = strip_tags($TOGGLEDESC);
                    }
                    $questiondata['typeVideoUrl'] = $this->getQtypeVideo($questiondata);
                    array_push($allquestion, $questiondata);
                } else {
                    // echo $getquestionfunction."\n";
                }
            } else {
                // echo $getquestionfunction."\n";
            }
        }
        // $allquestion = array_map('utf8_encode', $allquestion);
        return $allquestion;
    }
    private function maputf8($data)
    {
        $data = (array)$data;
        foreach ($data as $key => $value) {
            if(is_string($value)){
                $data[$key] = utf8_encode($value);
            } else if(is_array($value) || is_object($value)){
                $data[$key] = $this->maputf8($value);
            }
        }
        return $data;
    }
    public function get_singlequestion($localattempt, $questionattemptid=0){
        global $DB, $CFG, $USER, $TOGGLEDESC, $TANSLATIONQ;
        require_once($CFG->dirroot.'/mod/quiz/locallib.php');
        require_once($CFG->dirroot.'/mod/quiz/lib.php');
        $allquestion = array();
        if(empty($localattempt)){
            return $allquestion;
        }
        $qry="select q.id as 'qid',qa.id as 'id',q.name as 'name', q.questiontext as 'questiontext', q.qtype as 'qtype', qa.slot as 'slot', qc.contextid, qa.questionusageid, q.desctoogle, q.questioninstruction, q.questioninstructionaudio, q.generalfeedback, qa.maxmark, qa.maxfraction, qa.rightanswer, qa.responsesummary, q.translation, q.questionhint, q.questionhintaudio, q.timelimit, q.strands, q.competencies from {question_attempts} as qa left join {question} as q on qa.questionid = q.id INNER JOIN {question_categories} as qc ON qc.id=q.category where qa.questionusageid=?";
        $query_fetch_question = $DB->get_recordset_sql($qry, array($localattempt->uniqueid));
        $Qposition = 0;
        $gradearray = array("gaveup", "gradedwrong", "gradedright", "gradedpartial", "needsgrading");
        foreach ($query_fetch_question as $question) {
            $getquestionfunction = "getqtype_".$question->qtype;
            if(empty($question->questioninstruction)){
                $question->questioninstruction = "";
            }
            if (method_exists($this, $getquestionfunction)) {
                // echo $question->qtype."\n";
                if($questiondata = self::$getquestionfunction($question, $localattempt)){
                    if($question->qtype != "description"){
                        $Qposition++;
                    }

                    if(empty($questiondata['questionVideo'])){
                        $questiondata['questionVideo'] = "";
                    }

                    $statusdata = $DB->get_record_sql("SELECT * FROM `mdl_question_attempt_steps` WHERE questionattemptid = ? order BY id desc limit 0, 1", array($question->id));
                    $status = -1;
                    $marks = "0.00";
                    $marksfracetion = "0.00";
                    if(!empty($statusdata)){
                        $status = (in_array($statusdata->state, $gradearray)?array_search($statusdata->state, $gradearray):-1);
                        if($statusdata->fraction !== NULL){
                            $marks = (floatval($question->maxmark) * floatval($statusdata->fraction))/floatval($question->maxfraction);
                        } else {
                            $statusdata->fraction = "0.00";
                        }
                        $marksfracetion = $statusdata->fraction;
                    }

                    $questiondata['position'] = $Qposition;
                    $questiondata['haveToggle'] = false;
                    $questiondata['toggleQuestion'] = "";
                    $questiondata['strands'] = $question->strands;
                    $questiondata['competencies'] = $question->competencies;
                    $questiondata['rightAnswer'] = $question->rightanswer;
                    $questiondata['timeLimit'] = !empty($question->timelimit)?intval($question->timelimit)*60:intval($question->timelimit);
                    $questiondata['timed'] = !empty($questiondata['timeLimit']);
                    $questiondata['responseSummary'] = $question->responsesummary;
                    $questiondata['marks'] = number_format($marks, 2);
                    $questiondata['marksFraction'] = number_format($marksfracetion, 2);
                    $questiondata['maxMarks'] = number_format($question->maxmark, 2);
                    $questiondata['maxFraction'] = number_format($question->maxfraction, 2);
                    $questiondata['status'] = $status;
                    if($localattempt->disableHints){$questiondata['questionHint'] = NULL;}
                    if($localattempt->disableExplanation){$questiondata['generalFeedback'] = NULL;}
                    $questiondata['ccccc'] = $localattempt->isTarl;
                    if($localattempt->isTarl){
                        $questiondata['haveTranslation'] = false;
                        $questiondata['translation'] = null;
                        $TANSLATIONQ = null;
                    }elseif(!empty($TANSLATIONQ)){
                        $questiondata['haveTranslation'] = !empty($TANSLATIONQ)?true:false;
                        $questiondata['translation'] = $TANSLATIONQ;
                        $TANSLATIONQ = null;
                    }
                    if($question->desctoogle){
                        $questiondata['haveToggle'] = !empty($TOGGLEDESC)?true:false;
                        $questiondata['toggleQuestion'] = strip_tags($TOGGLEDESC);
                    }
                    
                    if($questionattemptid == $question->id){
                        $questiondata['typeVideoUrl'] = $this->getQtypeVideo($questiondata);
                        array_push($allquestion, $questiondata);
                        break;
                    }
                } else {
                    // echo $getquestionfunction."\n";
                }
            } else {
                // echo $getquestionfunction."\n";
            }
        }
        return $allquestion;
    }
    private function prepareURL($link)
    {
        $linkparts = explode("?", $link);
        if(sizeof($linkparts) == 2){
            $params = explode("&", $linkparts[1]);
            foreach($params as $key => $value) {
                $val2 = explode("=", $value);
                if(sizeof($val2) == 2){
                    $val2[1] = urlencode($val2[1]);
                }
                $params[$key]=implode("=", $val2);
            }
            $linkparts[1] = implode("&", $params);
        }
        $link = implode("?", $linkparts);
        return $link;
    }
    public function getqtype_ddimageortext($question, $attempt){
        global $DB, $USER, $CFG, $wstoken;
        $baseurl = "/webservice/pluginfile.php";
        $baseurl = "/local/designer/file.php?id=";
        $questiondata = array();
        $isAttempted = false;
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        $questiondata['questionText'] = self::get_questionText($question);
        if(strip_tags($questiondata['questionText']) == "-" || strip_tags($questiondata['questionText']) == "--"){
            $questiondata['questionText'] = "";
        }
        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $questiondata['result'] = "";
        $width = 100;
        $height = 100;
        $questiondata['backgroundImage'] = "";
        if($file = $DB->get_record_sql("select * from {files} where contextid = ? and component = ? and itemid=? and filename != '.'", array($question->contextid, "qtype_ddimageortext", $question->qid))){
            // $fileurl = file_encode_url($CFG->wwwroot . $baseurl, '/'.$file->contextid.'/'.$file->component.'/'.$file->filearea.'/'.$question->questionusageid.'/'.$question->slot.'/'.$uniqueid.'/'.$rs_fetch_question->slot.'/'.$file->itemid.'/'.$file->filepath.$file->filename, false)."?token=".$wstoken;
            $fileurl = $this->prepareURL($CFG->wwwroot . $baseurl.$file->pathnamehash.'&filename='.$file->filename);

            $questiondata['backgroundImage'] = $fileurl;
            $data       = file_get_contents($fileurl);
            $size_info2 = getimagesizefromstring($data);
            $width = $size_info2[0];
            $height = $size_info2[1];
        }
        $alldrops = $DB->get_records("qtype_ddimageortext_drops", array("questionid"=>$question->qid));
        $maxqattempt = $DB->get_field_sql("SELECT max(qasd.attemptstepid) FROM mdl_question_attempt_step_data qasd INNER JOIN mdl_question_attempt_steps qas on qasd.attemptstepid = qas.id WHERE qas.questionattemptid = ? and qasd.name like'p%' order by qas.id desc", array($question->id));
        foreach ($alldrops as $key => $drop) {
            $drop->questionId = $drop->questionid;
            $drop->xLeft = $drop->xleft;
            $drop->yTop = $drop->ytop;
            $drop->PXLeft = round(($drop->xleft/$width)*100);
            $drop->PYTop = round(($drop->ytop/$height)*100);
            $drop->key = "p".$drop->no;
            unset($drop->questionid);
            unset($drop->xleft);
            unset($drop->ytop);
            $userResponse = "";
            if($maxqattempt){
                if($resdata = $DB->get_record("question_attempt_step_data", array("attemptstepid"=>$maxqattempt, "name"=>$drop->key))){
                    $isAttempted = true;
                    $userResponse = $resdata->value;
                }
            }
            $drop->userResponse = $userResponse;
            $alldrops[$key] = $drop;
        }
        $questiondata['allDrops'] = array_values($alldrops);
        // return $questiondata;
        $choiceorder=$DB->get_field_sql("SELECT qasd.value FROM `{question_attempts}`as qa LEFT JOIN {question_attempt_steps} as qas on qa.id = qas.questionattemptid LEFT JOIN {question_attempt_step_data} as qasd on qasd.attemptstepid = qas.id  WHERE qa.id=?", array($question->id));
        $alldrags = array_values($DB->get_records("qtype_ddimageortext_drags", array("questionid"=>$question->qid)));
        $allDragsopeions = array();
        if($choiceorder){
            $ddpcounter=0;
            foreach (explode(",", $choiceorder) as $value) {
                $ddpcounter++;
                $found_key = array_search($value, array_column($alldrags, 'no'));
                $alldrags[$found_key]->value = $ddpcounter;
                $allDragsopeions[] = $alldrags[$found_key];
            }
        }
        $multichar = false;
        foreach ($allDragsopeions as $key => $drag) {
            $drag->file = null;
            if($file = $DB->get_record_sql("select * from {files} where component = ? and filearea = ? and itemid = ? and filename != '.'", array("qtype_ddimageortext", "dragimage", $drag->id))){
                $fileurl = $this->prepareURL($CFG->wwwroot . $baseurl.$file->pathnamehash.'&filename='.$file->filename);

                // $fileurl = file_encode_url($CFG->wwwroot . $baseurl, '/'.$file->contextid.'/'.$file->component.'/'.$file->filearea.'/'.$attempt->uniqueid.'/'.$question->slot.'/'.$file->itemid.'/'.$file->filename, false)."?token=".$wstoken;
                $file->fileurl = $fileurl;
                $drag->file = $file;
            } else {
                if(strlen($drag->label) > 3){
                    $multichar = true;
                }
            }
            $drag->questionId = $drag->questionid;
            $drag->dragGroup = $drag->draggroup;
            $drag->multichar = $multichar;
            // $drag->value = $drag->no;
            unset($drag->questionid);
            unset($drag->draggroup);
            $allDragsopeions[$key] = $drag;
        }
        $questiondata['allDrags'] = $allDragsopeions;
        $questiondata['isAttempted'] = $isAttempted ;
        $questiondata['multichar'] = $multichar ;
        return $questiondata;
    }
    private function updaterepository_s3bucketlink($string){
        global $USER, $CFG;
        if(!empty($string)){
            preg_match_all('/\< *[img][^\>]*[src] *= *[\"\']{0,1}([^\"\']*)/i', $string, $matches);
            if(sizeof($matches[1])>0){
                foreach ($matches[1] as $key => $url) {
                    if(strpos($url, 'repository_s3bucket')){
                        $file = "pluginfile.php";
                        $tokenfile = "tokenpluginfile.php";
                        $token = get_user_key('core_files', $USER->id);
                        if (!$CFG->slasharguments) {
                            $tokenfile .= "?token={$token}&file=";
                        } else {
                            $tokenfile .= "/{$token}";
                        }
                        $newurl = str_replace($file, $tokenfile, $url);
                        $string = str_replace($url, $newurl, $string);
                    }
                }
            }
        }
        return $string;
    }
    public function get_questionAnswer($question, $option){
        $answertext = $this->updaterepository_s3bucketlink($option->answer);
        $answertext = preg_replace('/<span style=\"text-decoration: underline;">(.*?)<\/span>/', '<span><u>$1</u></span>', $answertext);
        $itemid = $question->questionusageid."/".$question->slot."/".$option->id;
        $options=array("includetoken"=>true);
        $answertext = file_rewrite_pluginfile_urls($answertext, "pluginfile.php", $question->contextid, "question", "answer", $itemid, $options);
        $answertext = $this->parseAudiofiles($answertext);
        return $answertext;
    }
    public function get_questionText($question){
        $questiontext = $this->updaterepository_s3bucketlink($question->questiontext);
        $questiontext = preg_replace('/<span style=\"text-decoration: underline;">(.*?)<\/span>/', '<span><u>$1</u></span>', $questiontext);
        $itemid = $question->questionusageid."/".$question->slot."/".$question->qid;
        $options=array("includetoken"=>true);
        $questiontext = file_rewrite_pluginfile_urls($questiontext, "pluginfile.php", $question->contextid, "question", "questiontext", $itemid, $options);
        $questiontext = $this->parseAudiofiles($questiontext);
        return $questiontext;
    }
    public function get_parseQuestionTextFiles($question, $field){
        $questiontext = $this->updaterepository_s3bucketlink($question->$field);
        $questiontext = preg_replace('/<span style=\"text-decoration: underline;">(.*?)<\/span>/', '<span><u>$1</u></span>', $questiontext);
        $itemid = $question->questionusageid."/".$question->slot."/".$question->qid;
        $options=array("includetoken"=>true);
        $questiontext = $this->file_rewrite_pluginfile_urls($questiontext, "pluginfile.php", $question->contextid, "question", $field, $itemid, $options);
        $questiontext = $this->parseAudiofiles($questiontext);
        return $questiontext;
    }
    public function get_questionFeedback($question){
        $generalfeedback = $this->updaterepository_s3bucketlink($question->generalfeedback);
        $generalfeedback = preg_replace('/<span style=\"text-decoration: underline;">(.*?)<\/span>/', '<span><u>$1</u></span>', $generalfeedback);
        $options=array("includetoken"=>true);
        $generalfeedback = $this->file_rewrite_pluginfile_urls($generalfeedback, "pluginfile.php", $question->contextid, "question", "generalfeedback", $question->questionusageid, $options);
        $generalfeedback = $this->parseAudiofiles($generalfeedback);
        return $generalfeedback;
    }
    public function file_rewrite_pluginfile_urls($text, $file, $contextid, $component, $filearea, $itemid, array $options=null) {
        global $CFG, $USER;

        $options = (array)$options;
        if (!isset($options['forcehttps'])) {
            $options['forcehttps'] = false;
        }

        $baseurl = "{$CFG->wwwroot}/local/designer/{$file}";
        if (!empty($options['includetoken'])) {
            $userid = $options['includetoken'] === true ? $USER->id : $options['includetoken'];
            $token = get_user_key('core_files', $userid);
            $finalfile = basename($file);
            $tokenfile = "token{$finalfile}";
            $file = substr($file, 0, strlen($file) - strlen($finalfile)) . $tokenfile;
            $baseurl = "{$CFG->wwwroot}/local/designer/{$file}";

            if (!$CFG->slasharguments) {
                $baseurl .= "?token={$token}&file=";
            } else {
                $baseurl .= "/{$token}";
            }
        }

        $baseurl .= "/{$contextid}/{$component}/{$filearea}/";

        if ($itemid !== null) {
            $baseurl .= "$itemid/";
        }

        if ($options['forcehttps']) {
            $baseurl = str_replace('http://', 'https://', $baseurl);
        }

        if (!empty($options['reverse'])) {
            return str_replace($baseurl, '@@PLUGINFILE@@/', $text);
        } else {
            return str_replace('@@PLUGINFILE@@/', $baseurl, $text);
        }
    }
    public function parseAudiofiles($questiontext){
        preg_match_all('/<a\s[^>]*>.*?<\/a>/i', $questiontext, $result);
        foreach ($result[0] as $key => $match) {
            $oldmatch = $match;
            try {
                $a = new SimpleXMLElement($match);
                if($a['href']){
                    $url = $a['href'];
                    if($type = $this->isAudio($url)){
                        $questiontext = str_replace($match, '<a data-url="'.$url.'" toggleplay class="fa fa-play-circle" ></a>', $questiontext);
                    } else if($type = $this->isVideo($url)){
                        $questiontext = str_replace($match, '<video width="320" height="240" controls><source src="'.$url.'" type="'.$type.'">Your browser does not support the video tag.</video>', $questiontext);
                    } else {
                        
                    }
                }
            } catch (Exception $e) {
                
            }
        }
        return $questiontext;
    }
    private function isVideo($url){
        $url = get_headers($url,1);
        $type = "audio/ogg";
        if(is_array($url['Content-Type'])){ //In some responses Content-type is an array
            $video = strpos($url['Content-Type'][1],'video');
            $type = $url['Content-Type'][1];
        }else{
            $video = strpos($url['Content-Type'],'video');
            $type = $url['Content-Type'];
        }
        if($video !== false)
            return $type;
        
        return false;
    }
    private function isAudio($url){
        $url = get_headers($url,1);
        $type = "video/mp4";
        if(is_array($url['Content-Type'])){ //In some responses Content-type is an array
            $video = strpos($url['Content-Type'][1],'audio');
            $type = $url['Content-Type'][1];
        }else{
            $video = strpos($url['Content-Type'],'audio');
            $type = $url['Content-Type'];
        }
        if($video !== false)
            return $type;
        
        return false;
    }
    public function get_parseQuestionString($question, $type){
        global $CFG;
        $string = $question->$type;
        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $result);
        foreach ($result['href'] as $link) {
            $updatedlink = str_replace($CFG->wwwroot."/pluginfile.php", $CFG->wwwroot."/webservice/pluginfile.php", $link)."?token=".$this->token;
            $string = str_replace($link, $updatedlink, $string);
        }
        $string = preg_replace('/<span style=\"text-decoration: underline;">(.*?)<\/span>/', '<span><u>$1</u></span>', $string);
        $itemid = $question->questionusageid."/".$question->slot."/".$question->qid;
        $options=array("includetoken"=>true);
        $string = file_rewrite_pluginfile_urls($string, "pluginfile.php", $question->contextid, "question", $type, $itemid, $options);
        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $resultfiles);

        return $resultfiles['href'];
    }
    public function getqtype_multichoice($question, $attempt){
        global $DB, $USER, $CFG, $wstoken;
        $baseurl = "/webservice/pluginfile.php";
        $isAttempted = false;
        $questiondata = array();
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        $questiondata['questionText'] = self::get_questionText($question);
        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $questiondata['result'] = "";
        if($choice = $DB->get_record("qtype_multichoice_options", array("questionid"=>$question->qid))){
            if(!$choice->single){
                $maxqattempt = $DB->get_field_sql("SELECT max(qasd.attemptstepid) FROM mdl_question_attempt_step_data qasd INNER JOIN mdl_question_attempt_steps qas on qasd.attemptstepid = qas.id WHERE qas.questionattemptid = ? and qasd.name like'choice%' order by qas.id desc", array($question->id));
                $questiondata['type'] = "multiselect";
            } else {
                $maxqattempt = $DB->get_field_sql("SELECT max(qasd.attemptstepid) FROM mdl_question_attempt_step_data qasd INNER JOIN mdl_question_attempt_steps qas on qasd.attemptstepid = qas.id WHERE qas.questionattemptid = ? and qasd.name like'answer' order by qas.id desc", array($question->id));
            }
            $questiondata['isRadioButton'] = ($choice->single)?true:false;
            $questiondata['shuffleAnswers'] = $choice->shuffleanswers;
        }        
        $query_fetch_optstep=$DB->get_records_sql("SELECT qasd.value FROM `{question_attempts}`as qa LEFT JOIN {question_attempt_steps} as qas on qa.id = qas.questionattemptid LEFT JOIN {question_attempt_step_data} as qasd on qasd.attemptstepid = qas.id WHERE qa.questionusageid = ? and `slot` = ? and qasd.name='_order'", array($attempt->uniqueid, $question->slot));
        $answerseq="";
        foreach($query_fetch_optstep as $rs_fetch_optstep)
        {
            $answerseq=$rs_fetch_optstep->value;
        }
        $answerseqdata=explode(",",$answerseq);
        $answerseqdataArr=array();
        $optionValue = 0;
        $userResponse = array();
        if($maxqattempt){
            $resdata = $DB->get_records("question_attempt_step_data", array("attemptstepid"=>$maxqattempt));
            foreach ($resdata as $key => $res) {
                if($res->name =="-submit"){ continue; }
                $isAttempted = true;
                if($questiondata['isRadioButton']){
                    array_push($userResponse, $res->value);
                } else {
                    if($res->value == 1){
                        array_push($userResponse, str_replace("choice", "", $res->name));
                    }
                }
            }
        }
        if($questiondata['type'] == "multichoice"){
            $questiondata['userResponse'] = (sizeof($userResponse)>0?array_pop($userResponse):"");        
        } else {
            $questiondata['userResponse'] = json_encode($userResponse);        
        }
        foreach ($answerseqdata as $key => $value) {
            $query_fetch_options = $DB->get_record_sql("select an.id , an.answer as 'answer' , an.answerformat , an.fraction as 'answerfraction' from {question_answers} as an  where an.id=".$value);
            $query_fetch_options->answer = self::get_questionAnswer($question, $query_fetch_options);
            $query_fetch_options->value=$optionValue;
            $query_fetch_options->answer=self::cleanse_option($query_fetch_options->answer);
            $query_fetch_options->answerFormat=$query_fetch_options->answerformat;
            $query_fetch_options->fraction=$query_fetch_options->answerfraction;
            unset($query_fetch_options->answerformat);
            unset($query_fetch_options->answerfraction);
            $answerseqdataArr[] = $query_fetch_options;
            $optionValue++;
        }
        $questiondata['options'] = $answerseqdataArr;
        $questiondata['isAttempted'] = $isAttempted ;
        return $questiondata;
    }
    public function getqtype_shortanswer($question, $attempt = null){
        global $DB, $USER, $CFG, $wstoken;
        $baseurl = "/webservice/pluginfile.php";
        $isAttempted = false;
        $questiondata = array();
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        $questiondata['questionText'] = self::get_questionText($question);
        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $query_fetch_options = $DB->get_recordset_sql("select an.id , an.answer as 'answer' , an.answerformat , an.fraction from {question_answers} as an  where an.question=?", array($question->qid));
        $answerseqdataArr = array();
        $firstanswer = "";
        foreach ($query_fetch_options as $option) {
            $option->answer = self::get_questionAnswer($question, $option);
            $option->answer=self::cleanse_option($option->answer);
            $option->answerFormat=$option->answerformat;
            if(empty($firstanswer) &&  $option->fraction > 0){
                $firstanswer = $option->answer;
            }
            unset($option->answerformat);
            $answerseqdataArr[] = $option;
        }
        $questiondata['options'] = $answerseqdataArr;
        $questiondata['firstanswer'] = $firstanswer;
        $questiondata['result'] = "";
        $userResponse = "";
        $resdata = $DB->get_recordset_sql("SELECT qasd.* FROM mdl_question_attempt_step_data qasd INNER JOIN mdl_question_attempt_steps qas on qasd.attemptstepid = qas.id and qasd.name = 'answer' WHERE qas.questionattemptid = ?", array($question->id));
        foreach ($resdata as $value) {
            $userResponse = $value->value;
            $isAttempted = true;
        }
        $questiondata['userResponse'] = $userResponse;
        $questiondata['isAttempted'] = $isAttempted ;
        return $questiondata;
    }
    public function getqtype_numerical($question, $attempt = null){
        global $DB, $USER, $CFG, $wstoken;
        $baseurl = "/webservice/pluginfile.php";
        $isAttempted = false;
        $questiondata = array();
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        $questiondata['questionText'] = self::get_questionText($question);
        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $questiondata['result'] = "";
        $questiondata['units'] = array_values($DB->get_records("question_numerical_units", array("question"=>$question->qid)));
        $settingoptions = $DB->get_record("question_numerical_options", array("question"=>$question->qid));
        $questiondata['unitsOnLeft'] = ($settingoptions->unitsleft)?true:false;
        $questiondata['unitsType'] = ($settingoptions->showunits == 2)?"dropdown":($settingoptions->showunits == 1)?"radio":"nothing";
        $resdata = $DB->get_recordset_sql("SELECT qasd.* FROM mdl_question_attempt_step_data qasd INNER JOIN mdl_question_attempt_steps qas on qasd.attemptstepid = qas.id and qasd.name in ('answer', 'unit') WHERE qas.questionattemptid = ?", array($question->id));
        $userResponse = array();
        foreach ($resdata as $value) {
            $isAttempted = true;
            $userResponse[$value->name] = $value->value;
        }
        $questiondata['userResponse'] = json_encode($userResponse);
        $questiondata['isAttempted'] = $isAttempted ;
        return $questiondata;
    }
    public function getqtype_truefalse($question, $attempt = null){
        global $DB, $USER, $CFG, $wstoken;
        $baseurl = "/webservice/pluginfile.php";
        $isAttempted = false;
        $questiondata = array();
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        $questiondata['questionText'] = self::get_questionText($question);
        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $questiondata['result'] = "";
        $query_fetch_options = $DB->get_recordset_sql("select an.id , an.answer as 'answer' , an.answerformat , an.fraction as 'answerfraction' from {question_attempts} as qa left join {question} as q on qa.questionid = q.id left join {question_answers} as an on q.id = an.question where qa.questionusageid=? and qa.slot=?", array($attempt->uniqueid, $question->slot));
        $maxqattempt = $DB->get_field_sql("SELECT max(qasd.attemptstepid) FROM mdl_question_attempt_step_data qasd INNER JOIN mdl_question_attempt_steps qas on qasd.attemptstepid = qas.id WHERE qas.questionattemptid = ? and qasd.name like'answer' order by qas.id desc", array($question->id));
        $userResponse = "";
        if($maxqattempt){
            if($resdata = $DB->get_record("question_attempt_step_data", array("attemptstepid"=>$maxqattempt))){
                $isAttempted = true;
                $userResponse = $resdata->value;
            }
        }
        $questiondata['userResponse'] = $userResponse;          
        $questiondata['isAttempted'] = $isAttempted ;
        $allanswer=array();
        $optionValue_Arr=array("False","True");
        $optionValue = 0;
        foreach($query_fetch_options as $rs_fetch_options)
        {
            $rs_fetch_options->value = array_search($rs_fetch_options->answer,$optionValue_Arr);
            $rs_fetch_options->answerFormat=$rs_fetch_options->answerformat;
            $rs_fetch_options->fraction=$rs_fetch_options->answerfraction;
            unset($rs_fetch_options->answerformat);
            unset($rs_fetch_options->answerfraction);
            $allanswer[]=$rs_fetch_options;
        }
        $questiondata['options'] = $allanswer;
        return $questiondata;
    }
    public function getqtype_calculated($question, $attempt = null){
        global $DB, $USER, $CFG, $wstoken;
        $isAttempted = false;
        $baseurl = "/webservice/pluginfile.php";
        $questiondata = array();
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        $questiondata['questionText'] = self::get_questionText($question);
        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $questiondata['result'] = "";
        $userResponse = "";
        $resdata = $DB->get_recordset_sql("SELECT qasd.* FROM mdl_question_attempt_step_data qasd INNER JOIN mdl_question_attempt_steps qas on qasd.attemptstepid = qas.id and qasd.name = 'answer' WHERE qas.questionattemptid = ?", array($question->id));
        foreach ($resdata as $value) {
            $isAttempted = true;
            $userResponse = $value->value;
        }
        $questiondata['userResponse'] = $userResponse;
        $questiondata['isAttempted'] = $isAttempted ;
        return $questiondata;
    }
    public function getqtype_gapselect($question, $attempt = null){
        global $DB, $USER, $CFG, $wstoken;
        $isAttempted = false;
        $baseurl = "/webservice/pluginfile.php";
        $questiondata = array();
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        $questiondata['questionText'] = self::get_questionText($question);
        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $questiondata['result'] = "";
        $maxqattempt = $DB->get_field_sql("SELECT max(qasd.attemptstepid) FROM mdl_question_attempt_step_data qasd INNER JOIN mdl_question_attempt_steps qas on qasd.attemptstepid = qas.id WHERE qas.questionattemptid = ? and qasd.name like'p%' order by qas.id desc", array($question->id));
        $userResponse = array();
        if($maxqattempt){
            $resdata = $DB->get_records("question_attempt_step_data", array("attemptstepid"=>$maxqattempt));
            foreach ($resdata as $key => $res) {
                if($res->name =="-submit"){ continue; }
                $isAttempted = true;
                array_push($userResponse, array("key"=>$res->name, "value"=>$res->value));
            }
        }
        $questiondata['userResponse'] = json_encode($userResponse); 
        $questiondata['isAttempted'] = $isAttempted ;
        $allanswer = array();
        $query_fetch_options = $DB->get_records("question_answers", array("question"=>$question->qid));
        $optionValue = 1;
        foreach ($query_fetch_options as $key => $fetch_option) {
            $fetch_option->value=$optionValue;
            $fetch_option->answer = self::get_questionAnswer($question, $fetch_option);
            $fetch_option->answer=self::cleanse_option($fetch_option->answer);
            $fetch_option->answerFormat=$fetch_option->answerformat;
            $fetch_option->feedbackFormat=$fetch_option->feedbackformat;
            unset($fetch_option->answerformat);
            unset($fetch_option->feedbackformat);

            array_push($allanswer, $fetch_option);
            $optionValue++;
        }
        $questiondata['cseq'] = array();
        if($allanswerseq = $DB->get_field_sql("SELECT d.value  FROM `mdl_question_attempt_step_data` d inner join mdl_question_attempt_steps s on s.id=d.attemptstepid WHERE s.questionattemptid = ? and d.name=? ORDER BY d.id  DESC", array($question->id,'_choiceorder1'))){
            $allanswerseq = explode(",", $allanswerseq);
            if($isAttempted){
                // $questiondata['cseq'] = array_reverse($allanswerseq);
                $questiondata['cseq'] = $allanswerseq;
            }
            $newallanswer = array();
            foreach ($allanswerseq as $key => $seq) {
                $allanswer[$seq - 1]->value = $key+1;
                array_push($newallanswer, $allanswer[$seq - 1]);
            }
            $allanswer = $newallanswer;
        }
        $questiondata['options'] = $allanswer;
        return $questiondata;
    }
    public function getqtype_match($question, $attempt = null){
        global $DB, $USER, $CFG, $wstoken;
        $baseurl = "/webservice/pluginfile.php";
        $isAttempted = false;
        $questiondata = array();
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        $questiondata['questionText'] = self::get_questionText($question);
        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $questiondata['result'] = "";


        $query_fetch_choiceorder=$DB->get_recordset_sql("SELECT qasd.value FROM `{question_attempts}`as qa LEFT JOIN {question_attempt_steps} as qas on qa.id = qas.questionattemptid LEFT JOIN {question_attempt_step_data} as qasd on qasd.attemptstepid = qas.id  WHERE qa.questionusageid = ? and `slot` = ? and qasd.name='_choiceorder'", array($attempt->uniqueid, $question->slot));
        $choiceorder="";
        foreach($query_fetch_choiceorder as $rs_fetch_choiceorder)
        {
            $choiceorder=$rs_fetch_choiceorder->value;
        }
        $query_fetch_stemorder=$DB->get_records_sql("SELECT qasd.value FROM `{question_attempts}`as qa LEFT JOIN {question_attempt_steps} as qas on qa.id = qas.questionattemptid LEFT JOIN {question_attempt_step_data} as qasd on qasd.attemptstepid = qas.id WHERE qa.questionusageid = ? and `slot` = ? and qasd.name='_stemorder'", array($attempt->uniqueid, $question->slot));
        $stemorder="";
        foreach($query_fetch_stemorder as $rs_fetch_stemorder)
        {
            $stemorder=$rs_fetch_stemorder->value;
        }
        $choiceorderData = explode(",",$choiceorder);
        $choiceorderDataArr= array();
        $ans_data = new stdClass();
        $optionValue = 0;
        $ans_data->id="0";
        $ans_data->answerText="Choisir…";
        $ans_data->value=$optionValue;
        $choiceorderDataArr[] = $ans_data;
        foreach ($choiceorderData as $key => $value) {
            $optionValue ++;
            $qry_matchoption = $DB->get_record_sql("SELECT * from  {qtype_match_subquestions} as qms where qms.id=".$value);
            $ans_data = new stdClass();
            $ans_data->id=$qry_matchoption->id;
            $ans_data->answerText=$qry_matchoption->answertext;
            $ans_data->value=$optionValue;
            $choiceorderDataArr[] = $ans_data;
        }
        $stemorderData = explode(",",$stemorder);
        $stemorderDataArr= array();
        $match_o_value=0;
        $maxqattempt = $DB->get_field_sql("SELECT max(qasd.attemptstepid) FROM mdl_question_attempt_step_data qasd INNER JOIN mdl_question_attempt_steps qas on qasd.attemptstepid = qas.id WHERE qas.questionattemptid = ? and qasd.name like'sub%' order by qas.id desc", array($question->id));
        foreach ($stemorderData as $key => $value) {
            $qry_matchoption = $DB->get_records_sql("SELECT  qmsq.id as qmsqid, qa.questionusageid as quesattemptid,qc.contextid as contextid,qmsq.id as id, qmsq.questionid as questionid, qmsq.questiontext as questiontext, qmsq.questiontextformat as questiontextformat, qmsq.answertext as answertext  FROM {qtype_match_subquestions} as qmsq  INNER JOIN {question} as q ON qmsq.questionid=q.id  INNER JOIN {question_categories} as qc ON qc.id=q.category  INNER JOIN {question_attempts} as qa ON qa.questionid=q.id where qmsq.id=".$value);
            $stemoption = "";
            foreach ($qry_matchoption as $key => $value) {
                $questions=$value->questiontext; //@@PUGLINFILE@@
                $quesattemptid = $value->quesattemptid;
                $qtypecontext = $value->contextid;
                $qmsqid = $value->qmsqid;
                $questionfile=$DB->get_records_sql("SELECT * FROM  {files}  WHERE contextid=$qtypecontext ");
                foreach($questionfile as $image){
                    $file='pluginfile.php';
                    $cntxid=$image->contextid;
                    $component=$image->component;
                    $filearea=$image->filearea;
                    $itemid=$image->itemid;
                    $filepath=$image->filepath;
                    $filename=$image->filename;
                }
                $value->key = "sub".$match_o_value;
                $match_o_value++;
                $value->qmsqId = $value->qmsqid;
                $value->quesAttemptId = $value->quesattemptid;
                $value->contextId = $value->contextid;
                $value->questionId = $value->questionid;
                $value->questionText = $value->questiontext;
                $value->questionTextFormat = $value->questiontextformat;
                $value->answerText = $value->answertext;
                $userResponse = "";
                if($maxqattempt){
                    if($resdata = $DB->get_record("question_attempt_step_data", array("attemptstepid"=>$maxqattempt, "name"=>$value->key))){
                        $isAttempted = true;
                        $userResponse = $resdata->value;
                    }
                }
                $value->userResponse = $userResponse;
                unset($value->qmsqid);
                unset($value->quesattemptid);
                unset($value->contextid);
                unset($value->questionid);
                unset($value->questiontext);
                unset($value->questiontextformat);
                unset($value->answertext);
                $stemoption=$value;
            }
            $stemorderDataArr[] = $stemoption;
        }
        $questiondata['choiceOrder']=$choiceorderDataArr;
        $questiondata['stemOrder']=$stemorderDataArr;
        $questiondata['isAttempted'] = $isAttempted ;
        return $questiondata;
    }
    public function getqtype_description($question, $attempt = null){
        global $DB, $CFG, $USER, $TOGGLEDESC, $TANSLATIONQ;
        $questiondata = array();
        $isAttempted = false;
        $isAttempted = true;
        $questiondata['isAttempted'] = $isAttempted ;
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        $questiondata['questionText'] = self::get_questionText($question);

        $pattern = '/<video.*?src="(.*?)".*?>/i';
        if (preg_match($pattern, $questiondata['questionText'], $matches)) {
            $questiondata['questionVideo'] = $matches[1]; 
        } else {
            $questiondata['questionVideo'] = ""; 
        }


        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $questiondata['haveToggle'] = false;
        $questiondata['toggleQuestion'] = "";
        $questiondata['result'] = "";
        if($question->translation){
            $TANSLATIONQ = $questiondata;
            return null;
        } else if($question->desctoogle){
            $TOGGLEDESC = $questiondata['questionText'];
            return null;
        } else {
            $TOGGLEDESC = $questiondata['questionText'];
            return $questiondata;
        }
    }
    public function is_word_selected($place, $response) {
        $responseplace = 'p' . $place;
        if (isset($response[$responseplace]) && (($response[$responseplace] == "on" ) || ($response[$responseplace] == "true" ) )) {
            return true;
        } else {
            return false;
        }
    }
    public function get_wordselectresponse($question)
    {
        global $DB, $CFG, $USER, $PAGE;
        $allres = $DB->get_records_sql("SELECT qasd.name, qasd.value FROM mdl_question_attempt_step_data as qasd WHERE qasd.attemptstepid = (SELECT max(id) FROM mdl_question_attempt_steps as qas WHERE qas.questionattemptid = :questionattemptid ) AND qasd.name like 'p%'", array("questionattemptid"=>$question->id));
        $returndata = array();
        foreach ($allres as $key => $res) {
            $returndata[$res->name] = $res->value;
        }
        return $returndata;
    }
    public function prepare_wordselecthtml($question, $response)
    {
        global $DB, $CFG, $USER, $PAGE;
        $output = '';
        require_once("{$CFG->dirroot}/question/type/questionbase.php");
        require_once("{$CFG->dirroot}/question/type/wordselect/question.php");
        $wordselectoptions = $DB->get_record("question_wordselect", array("questionid"=>$question->qid));
        $q = new qtype_wordselect_question();
        $output .= html_writer::start_div('qtext');
        /*initialised */
        $q->init($question->questiontext, $wordselectoptions->delimitchars);
        $correctplaces = $q->get_correct_places($question->questiontext, $wordselectoptions->delimitchars);
        $items = $q->get_words();
        $options_correctness = 0;
        $options_rightanswer = 0;
        $options_readonly = 0;
        if(!empty($response)){
            $options_correctness = 1;
            $options_rightanswer = 1;
            $options_readonly = 1;
        }
        foreach ($items as $place => $item) {
            $word = $item->get_without_delim();
            $correctnoselect = false;
            $wordattributes = array("role" => "checkbox");
            $afterwordfeedback = '';
            $wordattributes['name'] = "p{$place}";
            $wordattributes['id'] = "span_p{$place}";
            $wordattributes['for'] = "p{$place}";
            $iscorrectplace = $q->is_correct_place($correctplaces, $place);
            $checkbox = "";
            /* if the current word/place exists in the response */
            $isselected = $q->is_word_selected($place, $response);
            if ($isselected) {
                $wordattributes['class'] = 'selected';
            }
            if ($isselected && $options_correctness == 1) {
                if ($iscorrectplace) {
                    $afterwordfeedback = '<i class="icon fa fa-check text-success fa-fw " title="Correct" aria-label="Correct"></i>';
                    $wordattributes['title'] = get_string('correctresponse', 'qtype_wordselect');
                    $wordattributes['class'] = 'selected correctresponse';
                } else {
                    // $afterwordfeedback = $this->feedback_image(0);
                    $afterwordfeedback = '<i class="icon fa fa-remove text-danger fa-fw" title="Incorrect" aria-label="Incorrect"></i>';
                    $wordattributes['title'] = ' ' . get_string('incorrectresponse', 'qtype_wordselect');
                    $wordattributes['class'] = 'selected incorrectresponse';
                }
            } else if ($iscorrectplace) {
                if ($options_correctness == 1) {
                    if ($options_rightanswer == 1) {
                        $wordattributes['title'] = get_string('correctanswer', 'qtype_wordselect');
                        $wordattributes['class'] = 'correctposition';
                        $correctnoselect = true;
                    }
                }
            }
            /* skip empty places when tabbing */
            if ($word > "") {
                $wordattributes['tabindex'] = '1';
            }
            if ($options_readonly) {
                /*$wordattributes['tabindex'] = '';
                if ($iscorrectplace) {
                    if($isselected == true){
                        $wordattributes['class'] = 'readonly correctresponse';
                    } else {

                    }
                }

                if (!($iscorrectplace)) {
                    if ($isselected == true) {
                        $wordattributes['class'] = 'readonly incorrectresponse ';
                    } else if (($question->multiword == true)) {
                        $wordattributes['class'] = 'readonly multiword ';
                    }
                }*/
            } else {
                // $qasdata = $qa->get_last_qt_var($question->field($place));
                /* when scrolling back and forth between questions
                 * previously selected value into each place. This
                 * is retrieved from the question_attempt_step_data
                 * table
                 */
                if (($qasdata == "on") || ($qasdata == "true")) {
                    $wordattributes['class'] = 'selected selectable';
                    $wordattributes['aria-checked'] = 'true';
                } else {
                    $class = 'selectable';
                    if ($question->multiword == true) {
                        $class .= ' multiword';
                    }
                    $wordattributes['class'] = $class;
                    $wordattributes['aria-checked'] = 'false';
                }
                $properties = array(
                    'type' => 'checkbox',
                    'name' => $wordattributes['name'],
                    'id' => $wordattributes['name'],
                    'hidden' => 'true',
                    'class' => 'selcheck');
                if ($isselected == true) {
                    $properties['checked'] = "true";
                    $wordattributes['aria-checked'] = 'true';
                }
                $checkbox = html_writer::empty_tag('input', $properties);
            }

            if ($item->isselectable == true) {
                if ($correctnoselect == true) {
                    $word = "[" . $word . "]";
                }
                $output .= $checkbox;
                $output .= html_writer::tag('span', $word, $wordattributes);
                $output .= $afterwordfeedback;
            } else {
                // For non selectable items such as the tags for tables etc.
                $output .= $word;
            }
        }
        $output .= html_writer::end_div();
        return $output;
    }
    public function getqtype_wordselect($question, $attempt = null){
        global $DB, $CFG, $USER;
        $isAttempted = false;
        $questiondata = array();
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        // $question->questiontext = self::get_questionText($question);
        $response = $this->get_wordselectresponse($question);
        $question->questiontext = self::prepare_wordselecthtml($question, $response);
        $questiondata['questionText'] = self::get_questionText($question);
        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $questiondata['result'] = "";
        $questiondata['isAttempted'] = !empty($response);
        return $questiondata;
    }
    public function getqtype_multianswer($question, $attempt = null){
        global $DB, $CFG, $USER, $isAttempted_multianswer;
        $isAttempted_multianswer = false;
        $questiondata = array();
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        $questiondata['questionText'] = self::get_questionText($question);
        $questiondata['questionText'] = str_replace(' ', ' ', $questiondata['questionText']);
        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $questiondata['result'] = "";
        $lang = "fr";
        if(strpos($question->questiontext,'text-align: right')){
            $lang = "ar";
        }
        $questiondata['subQuestion'] = self::get_subquestion($question, $attempt, $lang);
        $questiondata['isAttempted'] = $isAttempted_multianswer ;
        return $questiondata;
    }
    public function getqtype_ddwtos($question, $attempt = null){
        global $DB, $CFG, $USER, $isAttempted_multianswer;
        $questiondata = array();
        $isAttempted = false;
        $questiondata['id'] = $question->id;
        $questiondata['qid'] = $question->qid;
        $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
        $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
        $questiondata['questionText'] = self::get_questionText($question);
        $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
        $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
        $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
        $questiondata['generalFeedback'] = self::get_questionFeedback($question);
        $questiondata['type'] = $question->qtype;
        $questiondata['slot'] = $question->slot;
        $questiondata['contextId'] = $question->contextid;
        $questiondata['status'] = -1;
        $questiondata['result'] = "";
        $allanswer = array();
        $query_fetch_options = $DB->get_records("question_answers", array("question"=>$question->qid));
        $optionValue = 0;
        foreach ($query_fetch_options as $key => $fetch_option) {
            $fetch_option->order=$optionValue+1;
            $fetch_option->answer = self::get_questionAnswer($question, $fetch_option);
            $fetch_option->value=$optionValue;
            $fetch_option->answerFormat=$fetch_option->answerformat;
            $fetch_option->feedbackFormat=$fetch_option->feedbackformat;
            $fetch_option->feedback=json_encode(unserialize($fetch_option->feedback));
            unset($fetch_option->answerformat);
            unset($fetch_option->feedbackformat);
            array_push($allanswer, $fetch_option);
            $optionValue++;
        }
        if($allanswerseq = $DB->get_field_sql("SELECT d.value  FROM `mdl_question_attempt_step_data` d inner join mdl_question_attempt_steps s on s.id=d.attemptstepid WHERE s.questionattemptid = ? and d.name=? ORDER BY d.id  DESC", array($question->id,'_choiceorder1'))){
            $allanswerseq = explode(",", $allanswerseq);
            $newallanswer = array();
            foreach ($allanswerseq as $key => $seq) {
                $allanswer[$seq - 1]->value = $key+1;
                array_push($newallanswer, $allanswer[$seq - 1]);
            }
            $allanswer = $newallanswer;
        } else {
            shuffle($allanswer);
        }
        $questiondata['options'] = $allanswer;
        $maxqattempt = $DB->get_field_sql("SELECT max(qasd.attemptstepid) FROM mdl_question_attempt_step_data qasd INNER JOIN mdl_question_attempt_steps qas on qasd.attemptstepid = qas.id WHERE qas.questionattemptid = ? and qasd.name like'p%' order by qas.id desc", array($question->id));
        $userResponse = array();
        if($maxqattempt){
            $resdata = $DB->get_records("question_attempt_step_data", array("attemptstepid"=>$maxqattempt));
            foreach ($resdata as $key => $res) {
                if($res->name =="-submit"){ continue; }
                array_push($userResponse, array("key"=>$res->name, "value"=>$res->value));
                $isAttempted = true;
            }
        }
        $questiondata['userResponse'] = json_encode($userResponse); 
        $questiondata['isAttempted'] = $isAttempted ;
        return $questiondata;
    }
    public function get_subquestion($parentquestion, $attempt = null, $lang = "fr"){
        global $DB, $USER, $CFG, $wstoken, $isAttempted_multianswer;
        $allquestiondata = array();
        $baseurl = "/webservice/pluginfile.php";
        $allquestions = $DB->get_records("question", array("parent"=>$parentquestion->qid));
        $subqno = 1;
        $maxqattempt = $DB->get_field_sql("SELECT max(qasd.attemptstepid) FROM mdl_question_attempt_step_data qasd INNER JOIN mdl_question_attempt_steps qas on qasd.attemptstepid = qas.id WHERE qas.questionattemptid = ? and qasd.name like'sub%_answer' order by qas.id desc", array($parentquestion->id));
        foreach ($allquestions as $key => $question) {
            if($question->qtype == "multichoice"){
                if(strpos($question->questiontext, "MULTICHOICE_V")){
                    $question->qtype = "multichoicev";
                } else if(strpos($question->questiontext, "MULTICHOICE_H")){
                    $question->qtype = "multichoiceh";
                } else {
                    $question->qtype = "dropdown";
                }
            }
            // print_r($question);
            $isAttempted = false;
            $questiondata = array();
            $questiondata['id'] = $question->id;
            $questiondata['questionTitle'] = self::get_parseQuestionTextFiles($question, "questioninstruction");
            $questiondata['questionHint'] = self::get_parseQuestionTextFiles($question, "questionhint");
            $questiondata['key'] = "sub".$subqno."_answer";
            $questiondata['questionText'] = $question->questiontext;
            $questiondata['questionHintAudio'] = self::get_parseQuestionString($question, 'questionhintaudio');
            $questiondata['questionTitleAudio'] = self::get_parseQuestionString($question, 'questioninstructionaudio');
            $questiondata['audio'] = self::get_deginersfiles("question", "questionaudio", $question->qid);
            $questiondata['type'] = $question->qtype;
            $questiondata['status'] = -1;
            $questiondata['result'] = "";
            $userResponse = "";
            if($maxqattempt){
                if($resdata = $DB->get_record("question_attempt_step_data", array("attemptstepid"=>$maxqattempt, "name"=>$questiondata['key']))){
                    $userResponse = $resdata->value;
                    $isAttempted = true;
                    $isAttempted_multianswer = true;
                }
            }
            $questiondata['userResponse'] = $userResponse;
            $questiondata['isAttempted'] = $isAttempted ;
            $allanswer = array();
            if($question->qtype == "dropdown"){
                $defaultOption = new stdClass();
                $defaultOption->id=0;
                $defaultOption->question=$question->id;
                // $defaultOption->answer=(isset($this->chooseoptions[$this->courselangauge])?$this->chooseoptions[$this->courselangauge]:$this->chooseoptions['fr']);
                $defaultOption->answer=(isset($this->chooseoptions[$lang])?$this->chooseoptions[$lang]:$this->chooseoptions['fr']);
                $defaultOption->fraction="0.0000000";
                $defaultOption->feedback="";
                $defaultOption->id=0;
                $defaultOption->value=-1;
                $defaultOption->answerFormat="1";
                $defaultOption->feedbackFormat="1";
                array_push($allanswer, $defaultOption);
            }
            $query_fetch_options = $DB->get_records("question_answers", array("question"=>$question->id));
            $optionValue = 0;
            $firstanswer = "";

            foreach ($query_fetch_options as $key => $fetch_option) {
                $fetch_option->value=$optionValue;
                $fetch_option->answer = self::get_questionAnswer($question, $fetch_option);
                $fetch_option->answer=self::cleanse_option($fetch_option->answer);
                $fetch_option->answerFormat=$fetch_option->answerformat;
                $fetch_option->feedbackFormat=$fetch_option->feedbackformat;
                if(empty($firstanswer) &&  $fetch_option->fraction > 0){
                    $firstanswer = $fetch_option->answer;
                }
                unset($fetch_option->answerformat);
                unset($fetch_option->feedbackformat);
                array_push($allanswer, $fetch_option);
                $optionValue++;
            }
            $questiondata['options'] = $allanswer;
            $questiondata['firstanswer'] = $firstanswer;
            array_push($allquestiondata, $questiondata);
            $subqno++;
        }
        return $allquestiondata;
    }
    public function saveAnswer($args){
        global $DB, $USER, $CFG;
        $wsquestionatmpid = $args['wsquestionatmpid'];
        $wsanswer_data = $args['wsanswer_data'];
        $wsanswer_final = $args['wsanswer_final'];
        $saveddata = array();
        self::update_mygrade();
        $skipanswercheck = $args['skipanswercheck'];
        $this->skipanswercheck = $skipanswercheck;
        if((empty($wsquestionatmpid) || (empty($wsanswer_data) && $wsanswer_data !== 0 && $wsanswer_data !== "0" )) && $skipanswercheck != 1){
            $this->sendError("Invalid  moduleid", "Invalid  moduleid");
        } else {
            $time=time();
            $qry="select max(id) as id,max(sequencenumber) as 'seq' FROM {question_attempt_steps} where questionattemptid=$wsquestionatmpid";
            $query_fetch_seq = $DB->get_records_sql($qry);
            $qas_seq=0;
            $qas_id="";
            foreach($query_fetch_seq as $rs_fetch_seq)
            {
                $qas_seq=$rs_fetch_seq->seq;
                $qas_id=$rs_fetch_seq->id;
            }
            $get_qtype = $DB->get_record_sql("SELECT q.*, qsa.questionusageid, qsa.slot FROM  {question_attempts} AS qsa INNER JOIN {question} AS q ON qsa.`questionid` = q.id WHERE qsa.id =".$wsquestionatmpid);
            if(!empty($get_qtype))
            {
                $k = "q".$get_qtype->questionusageid.":".$get_qtype->slot."_";
                array_push($saveddata, array("name"=>"slots", "value"=>$get_qtype->slot));
                array_push($saveddata, array("name"=>$k.":flagged", "value"=>0));
                array_push($saveddata, array("name"=>$k."-submit", "value"=>"Check"));
                $question_type = $get_qtype->qtype;
                if($question_type == "match" || $question_type == "ddimageortext" || $question_type == "gapselect" || $question_type == "multianswer" || $question_type == "ddwtos" || $question_type == "wordselect"){
                    // array_push($saveddata, array("name"=>$k.":-submit", "value"=>"check"));
                    if(is_array($wsanswer_data) && sizeof($wsanswer_data) > 0){
                        $qasd_val=array();
                        if($qas_seq !="")
                        {
                            foreach ($wsanswer_data as $key => $answer_data) {
                                if($answer_data['key'] == ""): continue; endif;
                                array_push($saveddata, array("name"=>$k.$answer_data['key'], "value"=>$answer_data['value']));
                            }
                            $status = 1;
                        }
                    }
                } else if($question_type == "truefalse" || $question_type == "shortanswer" || $question_type == "calculated" || $question_type == "numerical"){
                    array_push($saveddata, array("name"=>$k."answer", "value"=>$wsanswer_data));
                    // array_push($saveddata, array("name"=>$k.":-submit", "value"=>"check"));
                    $status = 1;
                    $message = 'Answer updated';
                } else if($question_type == "multichoice"){
                    if($choice = $DB->get_record("qtype_multichoice_options", array("questionid"=>$get_qtype->id))){
                        $get_qtype->single = $choice->single;
                        $get_qtype->shuffleAnswers = $choice->shuffleanswers;
                    } 
                    if($get_qtype->single && !is_array($wsanswer_data)){
                        array_push($saveddata, array("name"=>$k."answer", "value"=>$wsanswer_data));
                        $status = 1;
                    } else if(is_array($wsanswer_data)) {
                        foreach ($wsanswer_data as $key => $value) {
                            array_push($saveddata, array("name"=>$k.'choice'.$value, "value"=>1));
                            $status = 1;
                        }
                    }
                } else {
                   $message = 'Invalid Answer';

                }
                if($status){
                    $questiondata = null;
                    if($attempt = $DB->get_record("quiz_attempts", array("uniqueid"=>$get_qtype->questionusageid))){
                        try {

                            require_once($CFG->dirroot."/mod/quiz/classes/external.php");
                            $QuizEX = new mod_quiz_external();
                            array_push($saveddata, array("name"=>"attempt", "value"=>$attempt->id));
                            array_push($saveddata, array("name"=>$k.":sequencecheck", "value"=>$qas_seq+1));
                            $homework = $DB->get_record_sql("SELECT h.* FROM mdl_homework h INNER JOIN mdl_course_modules cm on cm.id = h.quiz INNER JOIN mdl_institution_group_member igm on h.groupid = igm.groupid WHERE igm.userid=? and h.quiz=? and h.status = 1 and igm.status=1", array($USER->id, $attempt->quiz));
                            $attempt->isHomework = false;
                            $attempt->homeworkType = 0;
                            $attempt->disableHints = false;
                            $attempt->disableExplanation = false;
                            
                            $attempt->isTarl = false;
                            $TarlChk = $DB->get_record_sql("SELECT * FROM {quiz} WHERE id=?", array($attempt->quiz));
                            $tarlCourse = (int)$TarlChk->course;
                            if(in_array($tarlCourse, $this->publicflowcourse)){
                                $attempt->isTarl = true;
                            }

                            if($homework){
                                $attempt->isHomework = true;
                                $attempt->homeworkType = $homework->type;
                                $attempt->disableHints = $homework->disablehints?true:false;
                                $attempt->disableExplanation = $homework->disableexplanation?true:false;
                            }
                            $data = $QuizEX->process_attempt($attempt->id, $saveddata, 0, 0, array());
                            $allquestions = self::get_singlequestion($attempt, $wsquestionatmpid);
                            if(sizeof($allquestions)){
                                $questiondata = array_pop($allquestions);
                            }
                            $status = 1;
                            $message = "success";
                            $this->sendResponse(array("submitted"=>true, "question"=>$questiondata));
                            
                        } catch (Exception $e) {
                            $this->catcherror = $e;
                            $this->email = $USER->email;
                            // print_r($e);
                            $this->sendResponse(array("submitted"=>false, "question"=>$questiondata));
                        }
                    } else {
                        $message="Invalid Attempt";
                        $this->sendError($message, $message);
                    }

                } else {
                    $this->sendError($message, $message);
                }
            } else {
                $message = 'Invalid Question';
                $this->sendError($message, $message);
            }
        }
    }
    public function getQuizSummary($args){
        global $DB, $CFG, $USER, $CATGRADE, $Qposition;
        $this->get_categorygrade();
        require_once($CFG->dirroot.'/mod/quiz/locallib.php');
        require_once($CFG->dirroot.'/mod/quiz/lib.php');
        // require_once($CFG->libdir . '/completionlib.php');
        // require_once($CFG->dirroot . '/course/format/lib.php');
        $attemptid = $args['attemptid'];
        if(!empty($attemptid)){
            if($localattempt = $DB->get_record("quiz_attempts", array("id"=>$attemptid, "userid"=>$USER->id))){
                $cm = get_coursemodule_from_id('quiz', $localattempt->quiz);
                $quiz = $DB->get_record("quiz", array("id"=>$localattempt->quiz));
                $bestgrade = quiz_get_best_grade($cm, $USER->id);
                $localattempt->userId = $localattempt->userid;
                $localattempt->uniqueid = $localattempt->uniqueid;
                $localattempt->quizGrade = number_format($quiz->grade, 2);
                $localattempt->quizSumGrades = number_format($quiz->sumgrades, 2);
                $localattempt->quizGotGrades = number_format($localattempt->sumgrades, 2);
                $localattempt->currentPage = $localattempt->currentpage;
                $localattempt->sumGrades = $localattempt->sumgrades;
                $localattempt->timeStart = $localattempt->timestart;
                $localattempt->lang = $quiz->lang;
                $localattempt->timeFinish = $localattempt->timefinish;
                $localattempt->timeModified = $localattempt->timemodified;
                $localattempt->timecheckState = $localattempt->timecheckstate;
                $localattempt->timeModifiedOffline = $localattempt->timemodifiedoffline;
                $localattempt->allQuestions = self::get_questions($localattempt);
                $localattempt->lastAttempted = self::get_lastAttemptedquestion($localattempt, $allQuestions);
                $localattempt->totalQuestions = $Qposition;
                $localattempt->maxScore = $CATGRADE;
                $localattempt->sumGrades = number_format(round((floatval($localattempt->sumgrades)/floatval($quiz->sumgrades))*floatval($quiz->grade), 4),2);
                unset($localattempt->userid);
                unset($localattempt->uniqueid);
                unset($localattempt->currentpage);
                unset($localattempt->sumgrades);
                unset($localattempt->timestart);
                unset($localattempt->timefinish);
                unset($localattempt->timemodified);
                unset($localattempt->timecheckstate);
                unset($localattempt->timemodifiedoffline);
                $this->sendResponse($localattempt);
            } else {
                $this->sendError("Invalid access to summary", "Invalid access to summary");
            }
        } else {
            $this->sendError("Invalid attemptid", "Invalid attemptid");
        }
    }
    public function getLanguages($args){
        $langs = get_string_manager()->get_list_of_translations();
        $alllangauges = array();
        foreach ($langs as $key => $lang) {
            $lg = new stdClass();
            $lg->text = $lang;
            $lg->value = $key;
            array_push($alllangauges, $lg);
        }
        $this->sendResponse(array("alllangs"=>$alllangauges));
    }
    public function finishAttempt($args){
        global $CFG, $USER, $DB, $PARENTUSER;
        $gradeid = 0;
        if(!empty($PARENTUSER->currentChild)){
            $gradeid = $PARENTUSER->currentChild->grade;
        }
        self::update_mygrade();
        $attemptid = $args["attemptid"];
        $finishattempt = (int)$args["finishattempt"];
        $timeup = $args["timeup"];
        $tournament_id=($args['tournament_id'])?$args['tournament_id']:0;
        //$this->sendResponse($tournament_id);
        if($args["lang"]){
            $this->userlangauge = $args["lang"];
        }

        if($attempt = $DB->get_record("quiz_attempts", array("id"=>$attemptid, "userid"=>$USER->id))){
            if($attempt->state == "inprogress"){
                $userdata=$DB->get_record("childusers",array("userid"=>$USER->id));
                // $quiz=$DB->get_record_sql("select qz.*, cm.id as cmid, cm.section from mdl_quiz_attempts q inner join mdl_course_modules cm on cm.instance = q.quiz inner join mdl_modules m on cm.module=m.id and m.name=? inner join mdl_quiz qz on qz.id = cm.instance where q.id=?",array("quiz", $attemptid));
                $quiz=$DB->get_record_sql("select qz.*, cm.id as cmid, cm.section from mdl_course_modules cm inner join mdl_modules m on cm.module=m.id and m.name=? inner join mdl_quiz qz on qz.id = cm.instance where qz.id=?",array("quiz", $attempt->quiz));
                $cmid = $quiz->cmid;
                $sectionid = $quiz->section;
                $gender=strtolower($userdata->gender);
                require_once($CFG->dirroot."/mod/quiz/classes/external.php");
                $QuizEX = new mod_quiz_external();
                array_push($this->functioncalled, array("process_attempt start", time()));
                $data = $QuizEX->process_attempt($attemptid, array(), $finishattempt, $timeup, array());
                array_push($this->functioncalled, array("process_attempt finish", time()));
                $isPassed = false;
                if($data['state'] == "finished"){
                    if(in_array($quiz->course, $this->publicflowcourse)){
                        // $this->autoexecutionscript($USER->id, "generateTournamentHistory", 2, $attemptid,$tournament_id);
                        // $this->autoexecutionscript($USER->id, "generateXp", 2, $attemptid,$tournament_id);
                    } else {
                        self::generateTournamentHistory($USER->id,$attemptid,$tournament_id);
                        self::generateXp($attemptid,$tournament_id);
                    }
                    $attempt = $DB->get_record("quiz_attempts", array("id"=>$attemptid, "userid"=>$USER->id));
                    if($gradeitem = $DB->get_record("grade_items", array("itemtype"=>"mod", "itemmodule"=>"quiz", "iteminstance"=>$quiz->id))){
                        if(($attempt->sumgrades/$quiz->sumgrades)*$gradeitem->grademax >= $gradeitem->gradepass){
                            $isPassed = true;
                            $this->autoexecutionscript($USER->id, "updatePotraits", 2, $gender,$cmid);
                            // self::updatePotraits($gender,$cmid);
                        }
                    }

                    if($attemptrecord = $DB->get_record("quizattempts", array("attemptid"=> $attempt->id, "schoolyear"=>$this->currentschoolyear))){
                        $attemptrecord->finishtime = $attempt->timefinish;
                        $attemptrecord->passed = ($isPassed)?1:0;
                        $DB->update_record("quizattempts", $attemptrecord);
                        // if($submittedattempt = $DB->get_record_sql("SELECT qas.*, qa.timefinish, qa.sumgrades, qa.quiz, st.id as stid, h.categoryid FROM {quizattempts} qas INNER JOIN {quiz_attempts} qa ON qas.attemptid = qa.id AND qa.timefinish != 0 INNER JOIN {homework} h ON h.quiz = qas.moduleid INNER JOIN {institution_group_member} igm ON h.groupid=igm.groupid AND igm.tutor=1 AND igm.schoolyear=h.schoolyear INNER JOIN {institution_group_member} igm1 ON igm1.groupid = h.groupid AND igm1.tutor=0 AND igm1.schoolyear=h.schoolyear AND igm1.userid = qa.userid AND igm1.status=1 LEFT JOIN {scorecardteacher} st ON st.attemptid = qa.id WHERE qas.id = ? AND st.id IS NULL", array($attemptrecord->is))){
                        //     $this->generatescore($attemptrecord);
                        // }
                    }
                    $returndata = array();
                    $returndata["status"]=1;
                    $returndata["message"]=$data['state'];
                    $returndata["isPassed"]=$isPassed;
                    $returndata["course"]=$quiz->course;
                    $returndata["isTarl"]=false;
                    $returndata["successMessage"]="";
                    $returndata["assignedxp"]="";
                    $returndata["successMessageTopic"]="";

                    if(in_array($quiz->course, $this->publicflowcourse)){
                        $section = $DB->get_record_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs LEFT JOIN {course_format_options} cfo ON cfo.sectionid=cs.id AND cfo.name='parent' WHERE cs.id=:id", array("id"=>$sectionid));
                        $cmodule = $DB->get_record("course_modules", array("id"=>$cmid));
                        $attempt->cmid = $cmid;
                        $returndata["isTarl"] = true;
                        $currenttarldiagnostic=$DB->get_record_sql("SELECT * FROM {tarldiagnostic} WHERE topic=:sectionid AND quiz=:quiz AND userid=:userid ORDER BY ID DESC", array("sectionid"=>$sectionid, "quiz"=>$cmid, "userid"=>$USER->id));
                        $newmaintopic = $currenttarldiagnostic->maintopic;
                        if($section->isdiagnostic){
                            $returndata["nextquiz"] = $isPassed?$quiz->nextquiz:$quiz->prevquiz;
                            $returndata["ismainquiz"] = $quiz->ismainquiz;
                            $returndata["component"] = $quiz->component;
                        } else {
                            $returndata["quiz"] = $quiz;
                            if($isPassed){
                                $totalattempt = $DB->get_field_sql("SELECT count(id) FROM {tarldiagnostic} WHERE userid=:userid AND maintopic=:topic", array("userid"=>$USER->id, "topic"=>$currenttarldiagnostic->maintopic));
                                $returndata["totalattempt"] = $totalattempt;                                
                                if($currenttarldiagnostic->maintopic != $currenttarldiagnostic->topic){
                                    $returndata["nextquiz"] = $currenttarldiagnostic->maintopic;
                                    $returndata["component"] = '';
                                } else {
                                    $DB->set_field('tarltopics_completion', 'status', 2, array('userid' => $USER->id, "topicid"=>$currenttarldiagnostic->topic));
                                    if(!$currenttarldiagnostic->diagnostic && !$currenttarldiagnostic->preview){
                                        array_push($this->functioncalled, array("marktarlstatus-3792", $currenttarldiagnostic->topic, 2));
                                        $this->marktarlstatus($currenttarldiagnostic->topic, 2);
                                    }
                                    array_push($this->functioncalled, array("get_nexttopic", $section));
                                    $nexttopic = $this->get_nexttopic($section);
                                    $returndata["nexttopic"] = $nexttopic;
                                    if($nexttopic){
                                        $returndata["ismainquiz"] = false;
                                        $returndata["component"] = "";
                                        if($nexttopic->parent == $section->parent){
                                            $returndata["nextquiz"] = $nexttopic->id;
                                            $newmaintopic = $nexttopic->id;
                                        } else {
                                            $returndata["nextquiz"] = '';
                                            $returndata["component"] = $nexttopic->id;
                                        }
                                    } else {
                                        $returndata["nextquiz"] = "";
                                        $returndata["ismainquiz"] = false;
                                        $returndata["component"] = "";
                                    }
                                }
                            } else {
                                // if($currenttarldiagnostic->maintopic != $currenttarldiagnostic->topic){
                                //     $returndata["nextquiz"] = $currenttarldiagnostic->maintopic;
                                //     $returndata["component"] = '';
                                // } else {
                                    $totalattempt = $DB->get_field_sql("SELECT count(id) FROM {tarldiagnostic} WHERE userid=:userid AND maintopic=:topic", array("userid"=>$USER->id, "topic"=>$currenttarldiagnostic->maintopic));
                                    $returndata["totalattempt"] = $totalattempt;
                                    $returndata["totalattemptquery"] = array("SELECT count(id) FROM {tarldiagnostic} WHERE userid=:userid AND maintopic=:topic", array("userid"=>$USER->id, "topic"=>$currenttarldiagnostic->maintopic));
                                    $returndata["totalattemptsectionid"] = $sectionid;
                                    $returndata["totalattemptcurrenttarldiagnostic"] = $currenttarldiagnostic;
                                    $returndata["nextquiz"] = $sectionid;
                                    $returndata["ismainquiz"] = false;
                                    $returndata["component"] = "";
                                    $totalattempt = $this->getattemptno($totalattempt);
                                    if($totalattempt>1){
                                        $quiz=$DB->get_record_sql("SELECT
                                            qz.*, cm.id as cmid, cm.section 
                                            FROM mdl_tarldiagnostic td 
                                            INNER JOIN mdl_course_modules cm on cm.id=td.quiz
                                            INNER JOIN mdl_quiz qz on qz.id = cm.instance 
                                            WHERE td.maintopic = td.topic AND td.maintopic=:maintopic order by td.id desc limit 0, 1",array("maintopic"=>$currenttarldiagnostic->maintopic));
                                        $returndata["mainquiz"] = $quiz;
                                    }
                                    $returndata["totalattempt1"] = $totalattempt;
                                    switch ($totalattempt) {
                                        case 2:
                                            if($quiz->attempt3quiz){
                                                $returndata["nextquiz"] = $quiz->attempt3quiz;
                                                $returndata["component"] = '';
                                            }
                                            break;
                                        case 3:
                                            if($quiz->attempt4quiz){
                                                $returndata["nextquiz"] = $quiz->attempt4quiz;
                                                $returndata["component"] = '';
                                            }
                                            break;
                                        case 4:
                                            if($quiz->attempt5quiz){
                                                $returndata["nextquiz"] = $quiz->attempt5quiz;
                                                $returndata["component"] = '';
                                            }
                                            break;
                                        case 5:
                                            if($quiz->attempt6quiz){
                                                $returndata["nextquiz"] = $quiz->attempt6quiz;
                                                $returndata["component"] = '';
                                            }
                                            break;
                                        default:
                                            break;
                                    }

                                // }
                            }
                        }
                        $attempt->ispassed = $isPassed?1:0;
                        $attempt->nextquiz = $returndata["nextquiz"];
                        $attempt->component = $returndata["component"];
                        if($isPassed && $quiz->component){
                            if(!$currenttarldiagnostic->preview){
                                if(!$currenttarldiagnostic->diagnostic){
                                    array_push($this->functioncalled, array("marktarlstatus-3876", $quiz->component, 2));
                                    $this->marktarlstatus($quiz->component, 2);
                                } else {
                                    $allsiblings = array_values($DB->get_records_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs INNER JOIN {course_format_options} cfo ON cfo.sectionid=cs.id AND cfo.courseid=cs.course AND cfo.name='parent' WHERE cs.isattempt=:isattempt AND cfo.value=:parent AND cs.course=:course AND (cs.forgrades = 0 OR FIND_IN_SET(:csforgrades,cs.forgrades)) order by cs.section", array("parent"=>$section->parent, "isattempt"=>$section->isattempt, "course"=>$section->course, "csforgrades"=>$gradeid)));
                                    $skey = array_search($section->id, array_column($allsiblings, "id"));
                                    if($skey !== false){
                                        if(($skey+1) == sizeof($allsiblings)){
                                            $this->marktarlstatus($quiz->component, 2);
                                            // array_push($this->functioncalled, array("get_nexttopic", $componentsection));
                                            // $nexttopic = $this->get_nexttopic($componentsection);
                                            // $returndata["nexttopic"] = $nexttopic;
                                            // if($nexttopic){
                                            //     $returndata["ismainquiz"] = false;
                                            //     $returndata["component"] = "";
                                            //     if($nexttopic->parent == $componentsection->parent){
                                            //         $returndata["nextquiz"] = $nexttopic->id;
                                            //     } else {
                                            //         $require2nddiagnostic = true;
                                            //         $returndata["nextquiz"] = '';
                                            //         $returndata["component"] = $nexttopic->id;
                                            //     }
                                            // }
                                        }
                                    }




                                    $this->allsiblingsaaaa = $allsiblings;
                                }
                                if($returndata["nextquiz"]){
                                    array_push($this->functioncalled, array("assignautoexecutionscript_component", array($USER->id, "assign_component", 1, $quiz->component, $this->userlangauge)));
                                    $this->autoexecutionscript($USER->id, "assign_component", 1, $quiz->component, $this->userlangauge);
                                } else {
                                    array_push($this->functioncalled, array("assign_component", $quiz->component));
                                }
                                $returndata["totalcomplete"] = $this->assign_component($quiz->component);
                            }
                        }
                        if($currenttarldiagnostic){
                            if(empty($currenttarldiagnostic->timefinish)){
                                $currenttarldiagnostic->ispassed = $attempt->ispassed;
                                $currenttarldiagnostic->nextquiz = $attempt->nextquiz;
                                $currenttarldiagnostic->ispassed = $attempt->ispassed;
                                $currenttarldiagnostic->component = $attempt->component;
                            }
                            if(empty($currenttarldiagnostic->timestart)){
                                $currenttarldiagnostic->timestart = $attempt->timestart;
                                $update = true;
                            }
                            if(empty($currenttarldiagnostic->timefinish)){
                                $currenttarldiagnostic->timefinish = $attempt->timefinish;
                                $update = true;
                            }
                            $DB->update_record("tarldiagnostic", $currenttarldiagnostic);
                        }

                        $returndata['currenttarldiagnostic'] = $currenttarldiagnostic;
                        $activeparent = $DB->get_record_sql("SELECT cs.id, cs.name, cs.section, cs.course, cs1.id as parentid, cs1.section as parentsection, cs1.name as parentname, cs1.isdiagnostic as parentisdiagnostic, cs2.id as grandparentid, cs2.section as grandparentsection, cs2.name as grandparentname, cs2.isdiagnostic as grandparentisdiagnostic FROM mdl_course_sections cs INNER JOIN mdl_course_format_options cfo ON cfo.courseid = cs.course AND cfo.sectionid =cs.id AND cfo.name = 'parent' INNER JOIN mdl_course_sections cs1 ON cs1.course = cfo.courseid AND cs1.section = cfo.value INNER JOIN mdl_course_format_options cfo1 ON cfo1.courseid = cs1.course AND cfo1.sectionid =cs1.id AND cfo1.name = 'parent' INNER JOIN mdl_course_sections cs2 ON cs2.course = cfo1.courseid AND cs2.section = cfo1.value WHERE cs.id=:sectionid", array("sectionid"=>$sectionid));
                        $returndata['activeparent']=$activeparent;
                        $require2nddiagnostic = false;

                        if($section->isdiagnostic && $isPassed && empty($returndata["nextquiz"]) && !empty($returndata["component"])){
                            $componentsection = $DB->get_record_sql("SELECT cs.*, cfo.value as parent FROM mdl_course_sections cs LEFT JOIN mdl_course_format_options cfo ON cfo.sectionid=cs.id AND cfo.name='parent' WHERE cs.id=:id", array("id"=>$returndata["component"]));
                            $allsiblings = array_values($DB->get_records_sql("SELECT cs.*, cfo.value as parent FROM mdl_course_sections cs INNER JOIN mdl_course_format_options cfo ON cfo.sectionid=cs.id AND cfo.courseid=cs.course AND cfo.name='parent' WHERE cfo.value=:parent AND cs.course=:course AND (cs.forgrades = 0 OR FIND_IN_SET(:csforgrades,cs.forgrades)) AND cs.isattempt=0 order by cs.section", array("parent"=>$componentsection->parent, "course"=>$componentsection->course, "csforgrades"=>$gradeid)));
                            $fkey = array_search($returndata["component"], array_column($allsiblings, "id"));
                            $returndata["componentsectionid"]=$returndata["component"];
                            $returndata["componentsection"]=$componentsection;
                            $returndata["allsiblings"]=$allsiblings;
                            $returndata["componentsectionfkey"]=$fkey;
                            if($fkey !== false){
                                if(($fkey+1) == sizeof($allsiblings)){
                                    $nexttopic = $this->get_nexttopic($componentsection);
                                    $returndata["nexttopic"] = $nexttopic;
                                    if($nexttopic){
                                        $returndata["ismainquiz"] = false;
                                        $returndata["component"] = "";
                                        if($nexttopic->parent == $componentsection->parent){
                                            $returndata["nextquiz"] = $nexttopic->id;
                                        } else {
                                            if(isset($this->publicflowdiagnostic[$activeparent->course]) && is_array($this->publicflowdiagnostic[$activeparent->course]) && in_array($activeparent->parentid, $this->publicflowdiagnostic[$activeparent->course])){

                                            } else {
                                                $require2nddiagnostic = true;
                                            }
                                            $returndata["nextquiz"] = '';
                                            $returndata["component"] = $nexttopic->id;
                                        }
                                    }
                                }
                            }
                        }                        
                        if($returndata["component"]){
                            $componentparent = $DB->get_record_sql("SELECT cs.id, cs.name, cs.section, cs.course, cs1.id as parentid, cs1.section as parentsection, cs1.name as parentname, cs2.id as grandparentid, cs2.section as grandparentsection, cs2.name as grandparentname FROM mdl_course_sections cs INNER JOIN mdl_course_format_options cfo ON cfo.courseid = cs.course AND cfo.sectionid =cs.id AND cfo.name = 'parent' INNER JOIN mdl_course_sections cs1 ON cs1.course = cfo.courseid AND cs1.section = cfo.value INNER JOIN mdl_course_format_options cfo1 ON cfo1.courseid = cs1.course AND cfo1.sectionid =cs1.id AND cfo1.name = 'parent' INNER JOIN mdl_course_sections cs2 ON cs2.course = cfo1.courseid AND cs2.section = cfo1.value WHERE cs.id=:sectionid", array("sectionid"=>$returndata["component"]));
                            if(empty($returndata["nextquiz"])){
                                if($currenttarldiagnostic->diagnostic){
                                    $returndata["successMessage"]="levelassignconfirmation";
                                    $returndata["successMessageTopic"]=$componentparent->parentname;
                                } else {
                                    $returndata["successMessage"]="componentconfirmation";
                                    $returndata["successMessageTopic"]=$componentparent->name;
                                }
                            }
                            $returndata['componentparent']=$componentparent;
                            if($currenttarldiagnostic->diagnostic != 1 || $require2nddiagnostic){
                                if($componentparent->parentid != $activeparent->grandparentid && $s=$DB->get_record("course_sections", array("id"=>$activeparent->grandparentid))){
                                    array_push($this->functioncalled, array("getfirstchild", $s, 'assessment'));
                                    if($t = $this->getfirstchild($s, 'assessment')){
                                        $returndata["successMessageTopic"]=$activeparent->grandparentname;
                                        $returndata["successMessage"]="afterlastcomponent";
                                        $returndata["successMessage1"]="show 2nd diagnostic";
                                        $returndata["componenttoassign"]=$returndata["component"];
                                        $returndata["component"] = $t->id;
                                        $returndata["2nddiagnostic"]=$t;
                                    }
                                }
                            }
                        }
                        $quiz->actualcomponent = $returndata["component"];
                        if($currenttarldiagnostic->preview && empty($returndata["nextquiz"])){
                            $returndata["component"] = $currenttarldiagnostic->previewfor;
                            array_push($this->functioncalled, array("marktarlstatus", $currenttarldiagnostic->previewfor, 1));
                            $this->marktarlstatus($currenttarldiagnostic->previewfor, 1);
                            $returndata["successMessage"]="levelassignconfirmation";
                            $componentparent1 = $DB->get_record_sql("SELECT cs.id, cs.name, cfo.value as parent, cs1.id as parentid, cs1.section as parentsection, cs1.name as parentname, cs.section, cs.course, cs.forgrades from {course_sections} cs INNER JOIN {course_format_options} cfo ON cfo.courseid = cs.course AND cfo.sectionid =cs.id AND cfo.name = 'parent' LEFT JOIN {course_sections} cs1 on cs1.course = cfo.courseid AND cs1.section = cfo.value where cs.id=:sectionid", array("sectionid"=>$currenttarldiagnostic->previewfor));
                            $returndata['componentparent1']=$componentparent1;
                            $returndata["successMessage1"]="2nd diagnostic completed";
                            $returndata["successMessageTopic"]=$componentparent1->parentname;
                        }
                        $quiz->component = $returndata["component"];


                        if($returndata["nextquiz"]  && $topicdata=$DB->get_record("course_sections", array("id"=>$returndata["nextquiz"]))){
                            $d = new stdClass();
                            $d->userid = $USER->id;
                            $d->schoolyear = $this->currentschoolyear;
                            $d->courseid = $quiz->course;
                            $d->topic = $topicdata->id;
                            $d->maintopic = $currenttarldiagnostic->diagnostic?$d->topic:$newmaintopic;
                            $tpdata = $DB->get_record_sql("SELECT cs.id, cs.name, cs.section, cs.course, cs.isdiagnostic, cs1.id as parentid, cs1.section as parentsection, cs1.name as parentname, cs1.isdiagnostic as parentisdiagnostic FROM mdl_course_sections cs INNER JOIN mdl_course_format_options cfo ON cfo.courseid = cs.course AND cfo.sectionid =cs.id AND cfo.name = 'parent' INNER JOIN mdl_course_sections cs1 ON cs1.course = cfo.courseid AND cs1.section = cfo.value WHERE cs.id=:id", array("id"=>$d->maintopic));
                            $d->maintopicparent = $tpdata->parentid;
                            $d->diagnostic = $topicdata->isdiagnostic;
                            $d->previspassed = $isPassed;
                            $d->preview = $currenttarldiagnostic->preview;
                            $d->previewfor = $currenttarldiagnostic->previewfor;
                            // $d->component = $quiz->component;
                            // $d->nextquiz = $returndata["nextquiz"];
                            $d->createdby = $USER->id;
                            $d->createddate = time();
                            if($currenttarldiagnostic->preview){
                                $d->assignedcomponent = $currenttarldiagnostic->assignedcomponent;
                            }
                            $DB->insert_record("tarldiagnostic",$d);
                        } else if($topicdata=$DB->get_record("course_sections", array("id"=>$quiz->component))) {
                            $tarltopics = new stdClass();
                            $tarltopics->userid = $USER->id;
                            $tarltopics->courseid = $quiz->course;
                            $tarltopics->topicid = $quiz->component;
                            $tarltopics->startdate = time();
                            $DB->insert_record("tarltopics", $tarltopics);
                            if(!$returndata["componenttoassign"]){
                                array_push($this->functioncalled, array("marktarlstatus", $quiz->component, 1));
                                $this->marktarlstatus($quiz->component, 1);
                                array_push($this->functioncalled, array("assign_component", $topicdata->id));
                                $this->assign_component($topicdata->id);
                            }

                            $d = new stdClass();
                            $d->userid = $USER->id;
                            $d->courseid = $quiz->course;
                            if($returndata["componenttoassign"]){
                                $d->preview = 1;
                                $d->previewfor = $returndata["componenttoassign"];
                                $this->marktarlstatus($currenttarldiagnostic->topic, 2);
                                if($firstchild = $DB->get_record_sql("SELECT cs.*, cfo.value as parent FROM mdl_course_sections cs INNER JOIN mdl_course_modules cm on FIND_IN_SET(cm.id,cs.sequence) AND cm.deletioninprogress=0 INNER JOIN mdl_modules as m on m.id=cm.module AND m.name='quiz' INNER JOIN mdl_quiz q on q.id=cm.instance AND q.ismainquiz=1 INNER join mdl_course_format_options cfo on cs.id = cfo.sectionid AND cfo.name='parent' WHERE cs.course = :course AND (cs.forgrades = 0 OR FIND_IN_SET(:sectionforgrades,cs.forgrades)) AND (q.forgrades = 0 OR FIND_IN_SET(:quizforgrades,q.forgrades)) AND cs.isdiagnostic=1 AND cfo.value=:section  GROUP BY cs.id order by cs.section asc limit 0,1", array("sectionforgrades"=>$gradeid, "quizforgrades"=>$gradeid, "course"=>$quiz->course, "section"=>$topicdata->section))){
                                    $d->topic = $firstchild->id;
                                    $d->maintopic = $firstchild->id;
                                } else {
                                    $d->topic = $topicdata->id;
                                    $d->maintopic = $topicdata->id;
                                }
                            } else {
                                if($firstchild = $DB->get_record_sql("SELECT cs.*, cfo.value as parent FROM mdl_course_sections cs left join mdl_course_format_options cfo on cs.id = cfo.sectionid and cfo.name='parent' WHERE course = ? and cfo.value=? order by section asc limit 0,1", array($quiz->course, $topicdata->section))){
                                    $d->topic = $firstchild->id;
                                    $d->maintopic = $firstchild->id;
                                } else {
                                    $d->topic = $topicdata->id;
                                    $d->maintopic = $topicdata->id;
                                }
                            }
                            $tpdata = $DB->get_record_sql("SELECT cs.id, cs.name, cs.section, cs.course, cs.isdiagnostic, cs1.id as parentid, cs1.section as parentsection, cs1.name as parentname, cs1.isdiagnostic as parentisdiagnostic FROM mdl_course_sections cs INNER JOIN mdl_course_format_options cfo ON cfo.courseid = cs.course AND cfo.sectionid =cs.id AND cfo.name = 'parent' INNER JOIN mdl_course_sections cs1 ON cs1.course = cfo.courseid AND cs1.section = cfo.value WHERE cs.id=:id", array("id"=>$d->maintopic));
                            $d->maintopicparent = $tpdata->parentid;
                            $d->actualcomponent = $quiz->actualcomponent;
                            $d->assignedcomponent = $topicdata->id;
                            $d->schoolyear = $this->currentschoolyear;
                            $d->previspassed = $isPassed;
                            $d->diagnostic = $topicdata->isdiagnostic;
                            $d->createdby = $USER->id;
                            $d->createddate = time();
                            $DB->insert_record("tarldiagnostic",$d);
                        }
                        $returndata["componentflow"] = array();
                        $returndata["assignedxp"] = "";
                        if(empty($currenttarldiagnostic->diagnostic) && empty($currenttarldiagnostic->preview) && $currenttarldiagnostic->ispassed && $currenttarldiagnostic->topic == $currenttarldiagnostic->maintopic){
                            $currenttarldiagnostic->xp = $this->generateTarlXp($quiz->course, $currenttarldiagnostic->diagnostic,$returndata["totalattempt"]);
                            $currenttarldiagnostic->xp = intval($currenttarldiagnostic->xp);
                            $DB->update_record("tarldiagnostic", $currenttarldiagnostic);

                        }
                        if($returndata["component"]){
                            if(empty($returndata["nextquiz"])){
                                $returndata["componentflow"]=$this->get_parenttopics($returndata["component"]);
                                if(empty($currenttarldiagnostic->preview)){
                                    if($currenttarldiagnostic->diagnostic){
                                        $returndata["assignedxp"] = $this->generateTarlXp($quiz->course, $currenttarldiagnostic->diagnostic,$returndata["component"]);

                                    } else {
                                        $returndata["assignedxp"] = $DB->get_field_sql("SELECT SUM(xp) FROM {tarldiagnostic} where ispassed=1 AND topic=maintopic and maintopicparent=:maintopicparent AND userid=:userid ", array("maintopicparent"=>$currenttarldiagnostic->maintopicparent, "userid"=>$USER->id));
                                    }
                                }
                            }
                        }
                    }
                    $this->sendResponse($returndata);
                } else {
                     $this->sendError("Failed to finishd Attempt", "Failed to finishd Attempt");
                }
            } else {
                $this->sendError("Attempt already complated", "Attempt already completed");
            }
        } else {
            $this->sendError("Invalid quiz attempt id", "Invalid quiz attempt id");
        }
    }
    private function generateTournamentHistory($userid,$attempt_id,$tournament_id){
        global $DB;
        $std=new stdClass();
        $std->tournament_id=$tournament_id;
        $std->userid=$userid;
        $std->attempt_id=$attempt_id;
        $std->createddate=time();
        $DB->insert_record("tournament_quiz_attempt_history",$std);
    }
    public function testXPGeneration($args){
        $attemptid = $args['attemptid'];
        self::generateXp($attemptid,0);
    }
    public function generateTarlXp($courseid, $isattemptflow=0,$componant=0){
        global $DB,$USER,$PARENTUSER;
        $gradeid = "";
        if(!empty($PARENTUSER->currentChild)){
            $gradeid = $PARENTUSER->currentChild->grade;
        }

        $xpsetting = $DB->get_record_sql("select * from {tarlxplogic} where FIND_IN_SET(?, topics) AND diagnostic=?", array($componant,$isattemptflow));
        if($xpsetting){
            $type = $isattemptflow?"Tarl Attempt-{$componant}":"Tarl CA-{$componant}";
            $std=new stdClass();
            $std->userid=$USER->id;
            $std->tournament_id=0;
            $std->xp_type=$type;
            $std->gradegradeid=0;
            $std->categoryid=$gradeid;
            $std->courseid=$courseid;
            $std->moduleid=0;
            $std->grade=0;
            $std->gotgrade=$xpsetting->xp;
            $std->maxgrade=0;
            $std->gotgrademax=0;
            $std->gradedate=time();
            $std->createddate=time();
            $std->modifieddate=time();
            $DB->insert_record("xphistory",$std);
            return strval($xpsetting->xp);
        }
        return "";
    }
    public function generateXp($attemptid,$tournament_id=0){
        global $DB, $CFG;
        if($attempt = $DB->get_record("quiz_attempts", array("id"=>$attemptid))){
            $scoredata = $DB->get_record_sql("SELECT 
            gg.id as gradeid, 
            cm.id as cmid, 
            qa.attempt as attemptno,
            cm.course, 
            c.category, 
            gg.rawgrade, 
            gg.rawgrademax, 
            xt.scoremultiplier, 
            xt.roundon, 
            xt.bonus_score,
            gg.timecreated, 
            gg.timemodified 
            FROM mdl_quiz_attempts qa
            INNER JOIN mdl_course_modules cm on cm.instance = qa.quiz
            INNER JOIN mdl_modules m on m.id = cm.module and m.name='quiz'
            INNER JOIN mdl_course c on c.id = cm.course
            INNER JOIN mdl_grade_items gi on gi.itemtype = 'mod' AND gi.itemmodule = 'quiz' AND gi.iteminstance = qa.quiz
            INNER JOIN mdl_grade_grades gg on gg.itemid = gi.id AND gg.userid = qa.userid
            LEFT JOIN mdl_xpsetting xt on xt.gradeid = c.category
            WHERE 
            qa.id = ?", array($attemptid));
            $oldgotgrade = 0;
            if($scoredata){
                if($defaultscoring = $DB->get_record("xpsetting", array("gradeid"=>0))){
                    $scoremultiplier = $defaultscoring->scoremultiplier;
                    $scorebonus = $defaultscoring->bonus_score;
                    $roundon = $defaultscoring->roundon;
                }
                if(!empty($scoredata->roundon)){ $roundon = $defaultscoring->roundon; }
                if(!empty($scoredata->scoremultiplier)){ $scoremultiplier = $defaultscoring->scoremultiplier; }
                if(!empty($scoredata->bonus_score)){ $scorebonus = $defaultscoring->bonus_score; }
                $xpgrade = new stdClass();
                $xpgrade->userid = $attempt->userid;
                $xpgrade->gradegradeid = $scoredata->gradeid;
                $xpgrade->categoryid = $scoredata->category;
                $xpgrade->courseid = $scoredata->course;
                $xpgrade->moduleid = $scoredata->cmid;
                $xpgrade->attemptno = $scoredata->attemptno;
                $xpgrade->tournament_id=$tournament_id;
                $oldgotgrade = $DB->get_field_sql("Select max(finalgrade) FROM {xphistory} where userid=:userid and moduleid=:moduleid ", array("userid"=>$attempt->userid, "moduleid"=>$scoredata->cmid));
                $totalhints = $DB->get_field_sql('select count(distinct uh.id) from mdl_userwallethistory uh INNER JOIN mdl_question_attempts qaa on uh.message like CONCAT(\'%question_id":"\', qaa.id, "%") INNER JOIN mdl_quiz_attempts qa on qa.userid = uh.userid AND qaa.questionusageid = qa.uniqueid WHERE uh.type="hint" and qa.id=:attemptid AND uh.userid = :userid', array("userid"=>$attempt->userid, "attemptid"=>$attemptid));
                $gotgrade = floatval($scoredata->rawgrade)/floatval($scoredata->rawgrademax)*$roundon*$scoremultiplier;
                $xpgrade->totalhints=$totalhints;
                $homework = $DB->get_record_sql("SELECT h.* FROM mdl_homework h INNER JOIN mdl_institution_group_member igm on igm.groupid = h.groupid and h.schoolyear = igm.schoolyear WHERE igm.userid = :userid AND h.schoolyear = :schoolyear AND h.quiz= :cmid ", array("userid"=>$attempt->userid, "schoolyear"=>$this->currentschoolyear, "cmid"=>$scoredata->cmid));
                if($gotgrade < 0){$gotgrade = 0;}
                $gotgrademax = $roundon*$scoremultiplier;
                if($attempt->attempt == 1 && $scoredata->rawgrade == $scoredata->rawgrademax){
                    $gotgrade += ($gotgrade * $scorebonus/100);
                    $gotgrademax += ($gotgrademax * $scorebonus/100);
                } 

                $xpgrade->finalgrade = $gotgrade;
                if(!is_null($oldgotgrade)){
                    if($oldgotgrade < $gotgrade){
                        $gradediff = $gotgrade-$oldgotgrade;
                        $gotgrade = intval($gradediff / $xpgrade->attemptno);
                    } else {
                        $gotgrade = 0;
                    }
                }
                $compreq = 0;
                if($totalhints > 0){$compreq = $totalhints * 10;}
                if(!empty($compreq)){
                    if($gotgrade > $compreq){
                        $gotgrade = $gotgrade - $compreq;
                    } else {
                        $gotgrade = 0;
                    }
                }
                if($xpgrade->attemptno==1 && $homework){
                    if(empty($homework->duedate) || $homework->duedate > time()){
                        $gotgrade = $gotgrade * 2;
                    } else {
                        $gotgrade = $gotgrade * 1.5;
                    }
                    $xpgrade->homeworkbonusdone = 1;
                }
                $xpgrade->grade = $scoredata->rawgrade;
                $xpgrade->maxgrade = $scoredata->rawgrademax;
                $xpgrade->gotgrade = $gotgrade;
                $xpgrade->gotgrademax = $gotgrademax;
                $xpgrade->gradedate = $scoredata->gradeid;
                $xpgrade->createddate = time();
                $DB->insert_record("xphistory", $xpgrade);
                // if(!empty($xpgrade->id)){
                //     if($oldgotgrade <= $gotgrade){
                //         $xpgrade->modifieddate = time();
                //         $DB->update_record("xphistory", $xpgrade);
                //     }
                // } else {
                //     $xpgrade->createddate = time();
                //     $DB->insert_record("xphistory", $xpgrade);
                // }
            }
        }
    }
    private function generateXpGrading(){
        global $DB, $CFG;
        echo "Executing...";
        $currentday = time();
        echo "Current Time:- ".date("Y-m-d H:i:s")."\n";
        $sql="SELECT gg.id as gradeid,xt.gradeid as xtgrade,xt.scoremultiplier,xt.roundon, xt.bonus_score, gg.userid as userid, gi.courseid, gi.categoryid, cm.id as cmid, gg.rawgrade, gg.rawgrademax, qa.attempt, gg.timecreated, gg.timemodified FROM mdl_grade_grades AS gg INNER JOIN mdl_grade_items AS gi ON gi.id=gg.itemid and gi.itemmodule = 'quiz' INNER JOIN mdl_course_modules as cm ON cm.instance=gi.iteminstance AND cm.course=gi.courseid INNER JOIN mdl_quiz q on cm.instance = q.id INNER JOIN mdl_quiz_attempts AS qa ON gg.userid=qa.userid AND qa.quiz=cm.instance AND qa.sumgrades = FORMAT((gg.rawgrade/gg.rawgrademax)*q.sumgrades,5) LEFT JOIN mdl_xphistory AS xp ON xp.moduleid=cm.id and xp.userid = gg.userid LEFT JOIN mdl_xpsetting AS xt ON xt.gradeid=gi.categoryid WHERE gg.rawgrade IS NOT NULL and xp.id IS NULL LIMIT 0, 2500";
        /*return $sql;*/

        $scoremultiplier = 100;
        $scorebonus = 10;
        $roundon = 10;
        $sqldiff ="SELECT gg.id as gradeid,xt.gradeid as xtgrade,xt.scoremultiplier,xt.roundon, xt.bonus_score, gg.userid as userid, gi.courseid, gi.categoryid, cm.id as cmid, gg.rawgrade, gg.rawgrademax, qa.attempt, gg.timecreated, gg.timemodified FROM mdl_grade_grades AS gg INNER JOIN mdl_grade_items AS gi ON gi.id=gg.itemid and gi.itemmodule = 'quiz' INNER JOIN mdl_course_modules as cm ON cm.instance=gi.iteminstance AND cm.course=gi.courseid  INNER JOIN mdl_quiz q on cm.instance = q.id INNER JOIN mdl_quiz_attempts AS qa ON gg.userid=qa.userid AND qa.quiz=cm.instance AND qa.sumgrades=FORMAT((gg.rawgrade/gg.rawgrademax)*q.sumgrades,5) INNER JOIN mdl_xphistory AS xp ON xp.moduleid=cm.id and xp.userid = gg.userid and xp.grade != gg.rawgrade LEFT JOIN mdl_xpsetting AS xt ON xt.gradeid=gi.categoryid WHERE gg.rawgrade IS NOT NULL LIMIT 0, 2500";
        $alldatadiff=$DB->get_records_sql($sqldiff);
        $alldata=$DB->get_records_sql($sql);
        $alldata = array_merge($alldata, $alldatadiff);
        echo "Total record:- ".sizeof($alldata);
        if($defaultscoring = $DB->get_record("xpsetting", array("gradeid"=>0))){
            $scoremultiplier = $defaultscoring->scoremultiplier;
            $scorebonus = $defaultscoring->bonus_score;
            $roundon = $defaultscoring->roundon;
        }
        foreach ($alldata as $key => $data){
            if(!empty($data->roundon)){ $roundon = $defaultscoring->roundon; }
            if(!empty($data->scoremultiplier)){ $scoremultiplier = $defaultscoring->scoremultiplier; }
            if(!empty($data->bonus_score)){ $scorebonus = $defaultscoring->bonus_score; }
            $oldrecord = $DB->get_record("xphistory", array(
                "userid"=>$data->userid,
                "categoryid"=>$data->categoryid,
                "courseid"=>$data->courseid,
                "moduleid"=>$data->cmid,
            ));
            if(empty($oldrecord)){
                $oldrecord = array();
                $oldrecord = (object)$oldrecord;
                $oldrecord->userid  =$data->userid;
                $oldrecord->categoryid  =$data->categoryid;
                $oldrecord->courseid  =$data->courseid;
                $oldrecord->moduleid  =$data->cmid;
                $oldrecord->createddate  =time();
            }
            $gotgrade = floatval($data->rawgrade)/floatval($data->rawgrademax)*$roundon*$scoremultiplier;
            if($gotgrade < 0){$gotgrade = 0;}
            $gotgrademax = $roundon*$scoremultiplier;
            if($data->attempt == 1 && $data->rawgrade == $data->rawgrademax){
                $gotgrade += ($gotgrade * $scorebonus/100);
                $gotgrademax += ($gotgrademax * $scorebonus/100);
            } 
            $oldrecord->grade  =$data->rawgrade;
            $oldrecord->maxgrade  =$data->rawgrademax;
            $oldrecord->gradegradeid  =$data->gradeid;
            $oldrecord->gradedate  = (!empty($data->timemodified)?$data->timemodified:$data->timecreated);
            $oldrecord->modifieddate  = time();
            $oldrecord->gotgrade  =$gotgrade;
            $oldrecord->gotgrademax  =$gotgrademax;
            echo "<pre>";
            echo empty($oldrecord->id)?"New record created in xphistory":"record updated in xphistory for :- ".$oldrecord->id;
            echo "</pre>";
            if(!empty($oldrecord->id)){
                $DB->update_record("xphistory", $oldrecord);
            } else {
                $DB->insert_record("xphistory", $oldrecord);
            }
        } 
    }

    private function updatePotraits($gender,$cmid){
        global $DB,$USER;
        $currenttime = time();
        $potarr=array();
        $potarr1=array();
        if($DB->record_exists("potraitactivity", array("userid"=>$USER->id, "moduleid"=>$cmid))){
            return;
        } else {
            $sql="SELECT * FROM {potraitgroup} WHERE (gender=? or gender=0 ) AND status=? AND end_time >=?  ";
            $checkPotraitGroupData=$DB->get_records_sql($sql,array("gender"=>$gender,"status"=>1,"end_time"=>$currenttime));
            if(sizeof($checkPotraitGroupData) > 0){
                $groupids = implode(",", array_keys($checkPotraitGroupData));
                $allpotraits = $DB->get_records_sql("select p.*, up.id as upid from mdl_potrait p left join mdl_userpotrait up on up.potraitid=p.id and up.ratio = up.completed and up.userid = ? where p.group_id in(?) and up.id is null", array($USER->id, $groupids));
                if(sizeof($allpotraits)>0){
                    $allpotraitids = array_keys($allpotraits);
                    shuffle($allpotraitids);
                    $potraitid = array_pop($allpotraitids);
                    $currectportrait = $allpotraits[$potraitid];
                    $olddata = $DB->get_record("userpotrait", array("userid"=>$USER->id, "potraitid"=>$potraitid));
                    if(empty($olddata)){
                        $olddata = new stdClass();
                        $olddata->userid = $USER->id;
                        $olddata->potraitid = $potraitid;
                        $olddata->ratio = $currectportrait->ratio;
                        $olddata->createddate = time();
                        $olddata->completed = 0;
                    }
                    $olddata->modifieddate = time();
                    $olddata->completed ++;
                    $pactivity = new stdClass();
                    $pactivity->userid = $USER->id;
                    $pactivity->moduleid = $cmid;
                    $pactivity->createddate = time();
                    if(!empty($olddata->id)){
                        $DB->update_record("userpotrait", $olddata);
                    } else {
                        $olddata->id = $DB->insert_record("userpotrait", $olddata);
                    }
                    $pactivity->userpotraitid = $olddata->id;
                    $DB->insert_record("potraitactivity", $pactivity);
                }
            }
        }
    }
    public function parseJwt ($tokendata) {
        $base64Url = explode(".", $tokendata)[1];
        $base64 = str_replace( "_","/",  str_replace( "-","+",  $base64Url));
        $jsonPayload = urldecode(implode("", array_map(array($this, 'mapb64'), str_split(base64_decode($base64)))));
        
        return json_decode($jsonPayload);
    }
    public function mapb64($c){
        return '%' . substr(('00' . dechex(ord($c))), -2);
    }
    public function createChild($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        // $firstname = trim($args["firstname"]);
        // $lastname = trim($args["lastname"]);
        $firstname = "Child";
        $lastname = "user".time();
        $grade = $args["grade"];
        $password = md5(bin2hex(random_bytes(10)));
        $charname = $args["charname"];
        $gender = $args["gender"];
        $image = $args["image"];
        $errors = array();
        // if(empty($firstname)){
        //     array_push($errors, "Firstname is required");
        // }
        // if(empty($lastname)){
        //     array_push($errors, "Lastname is required");
        // }
        if(empty($grade)){
            array_push($errors, "Grade is required");
        }
        if(empty($charname)){
            array_push($errors, "Character name is required");
        }
        // if(empty($gender)){
        //     array_push($errors, "Gender is required");
        // }
        if(sizeof($PARENTUSER->children) == 3){
            array_push($errors, "You  already have 3 Adventure");
        }
        if(sizeof($errors)>0){
            $this->sendError("Failed to create adventure ", implode(", ", $errors));
        } else {
            $userinsert  = new stdClass();
            $userinsert->username     = $USER->username."_".$USER->id."_".time();
            $userinsert->password     = md5($password);
            $userinsert->firstname    = $firstname;
            $userinsert->lastname     = $lastname;
            $userinsert->email        = $userinsert->username."@".explode("@", $USER->email)[1];
            $userinsert->timecreated  = time();
            $userinsert->timemodified = time();
            $userinsert->middlename   = " ";
            $userinsert->confirmed    = 1;
            $userinsert->mnethostid   = 1;
            $userinsert->alternatename   = $charname;
            $userinsert->id = $DB->insert_record('user', $userinsert);
            if($userinsert->id){
                $childdata = new stdClass();
                $childdata->parentid = $USER->id;
                $childdata->realparentid = $USER->id;
                $childdata->userid = $userinsert->id;
                $childdata->grade = $grade;
                $childdata->gender = $gender;
                $childdata->image = ($image)?$image:"";
                $childdata->createdby = $USER->id;
                $childdata->createddate = time();
                $childdata->id =$DB->insert_record('childusers', $childdata);
                if($token = self::get_usertoken($userinsert->id)){
                    self::validatetoken($token);
                    $responsedata = new stdClass();
                    $responsedata->status = 1;
                    $responsedata->message = "Adeventure created";
                    $responsedata->token = $token;
                    $responsedata->id = $userinsert->id;
                    $responsedata->userDetails = $PARENTUSER;
                    $this->sendResponse($responsedata);
                } else {
                    $this->sendError("Loggin Failed", "Invalid Request"); 
                    $responsedata = new stdClass();
                    $responsedata->status = 1;
                    $responsedata->id = $userinsert->id;
                    $responsedata->message = "Adeventure created, but failed to get token";
                    $responsedata->token = "";
                    $responsedata->userDetails = null;
                    $this->sendResponse($responsedata);
                }
            } else {
                $this->sendError("Failed to create Adventure", "Failed to create Adventure");
            }
        }
    }

    public function updateChild($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $id = trim($args["id"]);
        $firstname = trim($args["firstname"]);
        $lastname = trim($args["lastname"]);
        $grade = $args["grade"];
        $password = $args["password"];
        $charname = $args["charname"];
        $gender = $args["gender"];
        $errors = array();
        if(empty($firstname)){
            array_push($errors, "Firstname is required");
        }
        if(empty($lastname)){
            array_push($errors, "Lastname is required");
        }
        if(empty($id)){
            array_push($errors, "Invalid Child");
        }
        if(empty($grade)){
            array_push($errors, "Grade is required");
        }
        if(empty($charname)){
            array_push($errors, "Character name is required");
        }
        // if(empty($gender)){
        //     array_push($errors, "Gender is required");
        // }
        // if(sizeof($PARENTUSER->children) == 3){
        //     array_push($errors, "You  already have 3 Adeventure");
        // }
        if(!$DB->record_exists("user", array("id"=>$id))){
            array_push($errors, "Invalid Child");
        }
        if(sizeof($errors)>0){
            $this->sendError("Failed to update adevnture ", implode(", ", $errors));
        } else {
            $userupdate  = new stdClass();
            $userupdate->id    = $id;
            $userupdate->firstname    = $firstname;
            $userupdate->lastname     = $lastname;
            $userupdate->alternatename   = $charname;
            $DB->update_record('user', $userupdate);
            if($child = $DB->get_record("childusers", array("userid"=>$id))){
                $child->grade = $grade;
                // $child->gender = $gender;
                $DB->update_record('childusers', $child);
            }
            $returndata = array();
            $returndata["status"]=1;
            $returndata["message"]="Adeventure Updated";
            $this->sendResponse($returndata);
        }
    }
    public function getMainAccount($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        if(!empty($PARENTUSER)){
                $responsedata = new stdClass();
                $responsedata->userDetails = $PARENTUSER;
                $responsedata->examMode = true;
                $responsedata->missionMode = false;
                if($this->missionMode){
                    $responsedata->missionMode = true;
                }
                $this->sendResponse($responsedata);
        } else {
            $this->sendError("Unable to get Parent profile", "Failed to create Adventure");
        }
    }
    public function getChildren($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        if(!empty($PARENTUSER)){
                $responsedata = new stdClass();
                $responsedata->children = $PARENTUSER->children;
                $this->sendResponse($responsedata);
        } else {
            $this->sendError("Unable to get Parent profile", "Failed to create Adventure");
        }
    }

    // Demo API For Characters get

    public function getCharacter($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        // $gender = $args['gender'];
        $grade = $args['grade'];
        $allimage = array();

        if(!empty($grade)) {
            $sql = "SELECT * FROM {characterimages} WHERE (grades LIKE '%,{$grade},%' OR grades LIKE '{$grade},%' OR grades LIKE '%,{$grade}' OR grades = '{$grade}')";
            $allcharacterimage = $DB->get_records_sql($sql);
            if(!$allcharacterimage){
                $allcharacterimage = $DB->get_records("characterimages", array("grades"=>NULL)); 
            }
        }else{
            $allcharacterimage = $DB->get_records("characterimages", array("grades"=>NULL)); 
        }
        foreach ($allcharacterimage as $key => $characterimage) {
            if(strpos($characterimage->image, "://")){
                array_push($allimage, $characterimage->image);
            } else {
                array_push($allimage, $CFG->wwwroot."/local/designer/images/characters/".$characterimage->image);
            }
        }
        $this->sendResponse(array("images"=>$allimage));
    }

    public function setCharacter($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid;
        $image = $args['image'];
        $childid = $args['childid'];
        if(empty($image) || empty($childid)){
            $this->sendError("failed to set character", "Missing parameter");
        } else {
            $currentChild = array_filter($PARENTUSER->children, function($v, $k) {
                global $childid;
                return $v->id == $childid;
            }, ARRAY_FILTER_USE_BOTH);
            if(empty($currentChild)){
                $this->sendError("failed to set character", "Invalid Childid");
            } else {
                $currentChild = array_pop($currentChild);
                $childupdate = new stdClass();
                $childupdate->id = $currentChild->childId; 
                $childupdate->image = $image; 
                if($DB->update_record("childusers", $childupdate)){
                    $this->sendResponse(array("message"=>"Successfull"));
                } else {
                    $this->sendError("failed to set character", "failed to set character");
                }
            }
        }
    }
    public function setRegions($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid;
        $region = $args['region'];
        $provinces = $args['provinces'];
        $childid = $args['childid'];
        if(empty($region) || empty($provinces) || empty($childid)){
            $this->sendError("failed to set character", "Missing parameter");
        } else {
            $currentChild = array_filter($PARENTUSER->children, function($v, $k) {
                global $childid;
                return $v->id == $childid;
            }, ARRAY_FILTER_USE_BOTH);
            if(empty($currentChild)){
                $this->sendError("failed to set character", "Invalid Childid");
            } else {
                $currentChild = array_pop($currentChild);
                $childupdate = new stdClass();
                $childupdate->id = $currentChild->childId; 
                $childupdate->region = $region; 
                $childupdate->provinces = $provinces; 
                if($DB->update_record("childusers", $childupdate)){
                    $this->sendResponse(array("message"=>"Successfull"));
                } else {
                    $this->sendError("failed to Update region details", "failed to Update region details");
                }
            }
        }
    }
    //End
    

    public function updateProfile($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $firstname = trim($args["firstname"]);
        $lastname = trim($args["lastname"]);
        $errors = array();
        if(empty($firstname)){
            array_push($errors, "Firstname name is required");
        }
        if(empty($lastname)){
            array_push($errors, "Lastname name is required");
        }
        if(sizeof($errors)>0){
            $this->sendError("Failed to update profile ", implode(", ", $errors));
        } else {
            $userupdate  = new stdClass();
            $userupdate->id    = $USER->id;
            $userupdate->firstname    = $firstname;
            $userupdate->lastname     = $lastname;
            $DB->update_record('user', $userupdate);
            $returndata = array();
            $returndata["status"]=1;
            $returndata["message"]="Profile Updated";
            $this->sendResponse($returndata);
        }
    }
    public function requestPasswordReset($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $bytes = bin2hex(random_bytes(3));
        $messagehtml = "Hi $USER->email,<br>We have got a password reset request, use the below verification code.<br><strong>$bytes</strong><br>If you haven't requested this, please ignore this email.<br><br>Regards,<br>fivestudents<br>info@fivestudents.com <br>";
        $supportuser = core_user::get_support_user();
        $emailuser = new stdClass();
        $emailuser->email = $USER->email; 
        $emailuser->maildisplay = true;
        $emailuser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
        $emailuser->id = -1;
        $emailuser->firstnamephonetic = false;
        $emailuser->lastnamephonetic = false;
        $emailuser->middlename = false;
        $emailuser->alternatename = false;
        $subject = "Password reset request";
        if(email_to_user($emailuser, $supportuser, $subject, $messagehtml, $messagehtml)){
            if($oldrequest = $DB->get_record_sql("select * from {user_password_resets_req} where userid=? and timeexpire > ? and used=?", array($USER->id, time(), 0))){
                $oldrequest->token = $bytes;
                $oldrequest->timerequested = time();
                $oldrequest->timeexpire = strtotime("+5 minute");
                $DB->update_record('user_password_resets_req', $oldrequest);
            } else {
                $newrequest->userid = $USER->id;
                $newrequest->token = $bytes;
                $newrequest->timerequested = time();
                $newrequest->timeexpire = strtotime("+5 minute");
                $DB->insert_record('user_password_resets_req', $newrequest);
            }
            $returndata = array();
            $returndata["status"]=1;
            $returndata["message"]="Verification token sent to your phone";
            $this->sendResponse($returndata);
        } else {
            $this->sendError("Password reset request", "Failed to send verificatiopn email");
        }
    }
    public function passwordReset($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $token = $args['token'];
        $password = $args['password'];
        if(empty($password)){
            $this->sendError("Failed to rest password", "Password required");
        } else if($oldrequest = $DB->get_record_sql("select * from {user_password_resets_req} where userid=? and timeexpire > ? and used=? and token = ?", array($USER->id, time(), 0, $token))){
            $user = new stdClass();
            $user->id = $USER->id;
            $user->password = md5($password);
            $DB->update_record('user', $user);
            $oldrequest->used = 1;
            $DB->update_record('user_password_resets_req', $oldrequest);
            $returndata = array();
            $returndata["status"]=1;
            $returndata["message"]="Password update Successfull";
            $this->sendResponse($returndata);
        } else {
            $this->sendError("Failed to reset password", "Invalid Token");
        }
    }
    public function requestForgotPassword($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $username = $args["username"];
        if($user = $DB->get_record_sql("select * from {user} where deleted = 0 and (username=? or email= ?)", array($username, $username))){
            $bytes = bin2hex(random_bytes(3));
            $messagehtml = "Hi $user->firstname,<br>We have got a forgot password request, use the below verification code.<br><strong>$bytes</strong><br>If you haven't requested this, please ignore this email.<br><br>Regards,<br>fivestudents<br>info@fivestudents.com ";
            $supportuser = core_user::get_support_user();
            $emailuser = new stdClass();
            $emailuser->email = $user->email; 
            $emailuser->maildisplay = true;
            $emailuser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
            $emailuser->id = -1;
            $emailuser->firstnamephonetic = false;
            $emailuser->lastnamephonetic = false;
            $emailuser->middlename = false;
            $emailuser->alternatename = false;
            $subject = "Forgot Password request";
            if(email_to_user($emailuser, $supportuser, $subject, $messagehtml, $messagehtml)){
                if($oldrequest = $DB->get_record_sql("select * from {user_password_resets_req} where userid=? and timeexpire > ? and used=?", array($user->id, time(), 0))){
                    $oldrequest->token = $bytes;
                    $oldrequest->timerequested = time();
                    $oldrequest->timeexpire = strtotime("+5 minute");
                    $DB->update_record('user_password_resets_req', $oldrequest);
                } else {
                    $newrequest->userid = $user->id;
                    $newrequest->token = $bytes;
                    $newrequest->timerequested = time();
                    $newrequest->timeexpire = strtotime("+5 minute");
                    $DB->insert_record('user_password_resets_req', $newrequest);
                }
                $returndata = array();
                $returndata["status"]=1;
                $returndata["message"]="Verification token sent to your email";
                $this->sendResponse($returndata);
            } else {
                $this->sendError("Forgot password ", "Failed to send Verification code");
            }
        } else {
            $this->sendError("Forgot password ", "Failed to get your accound");
        }
    }
    public function ForgotPasswordReset($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $username = $args["username"];
        $token = $args['token'];
        $password = $args['password'];
        if(empty($username)){
            $this->sendError("Failed to rest password", "Username required");
        } else if($user = $DB->get_record_sql("select * from {user} where deleted = 0 and (username=? or email= ?)", array($username, $username))){
            if(empty($password)){
                $this->sendError("Failed to rest password", "Password required");
            } else if($oldrequest = $DB->get_record_sql("select * from {user_password_resets_req} where userid=? and timeexpire > ? and used=? and token = ?", array($user->id, time(), 0, $token))){
                $user = new stdClass();
                $user->id = $user->id;
                $user->password = md5($password);
                $DB->update_record('user', $user);
                $oldrequest->used = 1;
                $DB->update_record('user_password_resets_req', $oldrequest);
                $returndata = array();
                $returndata["status"]=1;
                $returndata["message"]="Password update Successfull";
                $this->sendResponse($returndata);
            } else {
                $this->sendError("Failed to reset password", "Invalid Token");
            }
        } 
    }
    public function getGrades($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $allcategory = self::get_category($parent=0);
        $onlyvisiblechild = self::onlyvisiblechild($allcategory);
        if(sizeof($allcategory)){
            $this->sendResponse(array("allgrade"=>$onlyvisiblechild));
        } else {
            $this->sendError("Failed to get grades", "grade not found");
        }
    }
    private function onlyvisiblechild($allcategory){
        $filtered = array();
        foreach ($allcategory as $key => $category) {
            if(!$category->isLaunched){
                continue;
            }
            if(sizeof($category->subGrade)>0){
                $subcategory = self::onlyvisiblechild($category->subGrade);
                foreach ($subcategory as $key => $cat) {
                    if(in_array($cat->parent, array(14, 15))){
                        $cat->name .= " " . $category->name;
                    }
                    array_push($filtered, $cat);
                }
            } else {
                array_push($filtered, $category);
            }
        }
        return $filtered;
    }
    public function assignFreeSubscription($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid,$free, $gradeid;
        $childid = $args['childid'];
        if(empty($childid)){
            $this->sendError("failed to assign Grade", "Missing parameter childid");
        } else {
            $currentChild = array_filter($PARENTUSER->children, function($v, $k) {
                global $childid,$free;
                return $v->id == $childid;
            }, ARRAY_FILTER_USE_BOTH);
            if(empty($currentChild)){
                $this->sendError("failed to assign Grade", "Invalid Childid");
            } else {
                $currentChild= array_pop($currentChild);
                // print_r($currentChild);
                // die;
                if(!empty($currentChild->currentSubscription)){
                    $this->sendError("failed to assign Grade", "Grade already assigned");
                } else {
                    $currentyeadending = strtotime("30 June ".date("Y"));
                    if($currentyeadending < time()){
                        $currentyeadending = strtotime("30 June ".date("Y", strtotime("+1 year")));
                    }
                    $insertenroledata = new stdClass();
                    $insertenroledata->user_id     = $PARENTUSER->id;
                    $insertenroledata->subscription_id     = 0;
                    $insertenroledata->course_cat_id     = $currentChild->grade;
                    $insertenroledata->package     = "free";
                    $insertenroledata->free     = 1;
                    $insertenroledata->enrolment_date_from     = time();
                    $insertenroledata->enrolment_date_to     = $currentyeadending;
                    $insertenroledata->enrolsubscriptionid     = $currentyeadending;
                    $insertenroledata->assignedto     = $currentChild->id;
                    $insertenroledata->assigneddate     = time();
                    $insertenroledata->id=$DB->insert_record('custom_enrol_details', $insertenroledata);   
                    $childdata = new stdClass();
                    $childdata->id = $currentChild->childId;
                    $childdata->subscriptionid = $insertenroledata->id;
                    $childdata->subscriptionassigned = time();
                    $childdata->subscriptionmodified = time();
                    self::enrolluserincourses($currentChild->id, $currentChild->grade, "self", $currentyeadending);
                    $DB->update_record('childusers', $childdata);
                    $this->sendResponse(array("message"=>"Successfull.."));
                }
            }
        }
    }
    public function enrolluserincourses($userid, $categoryid, $type, $currentyeadending){
        global $DB, $USER, $CFG, $PARENTUSER;
        $allcourses = $DB->get_records("course", array("category"=>$categoryid));
        foreach ($allcourses as $key => $course) {
            // if($type != "self"){
            //     // print_r($course);
            //     self::removeSelfEnrolment($course->id, $userid);
            // }
            self::enrolCourse($course->id, $userid, $type, $currentyeadending);
        }
    }
    private function checkEnrollInCourses($courseid){
        global $DB, $USER, $CFG, $PARENTUSER;
        $currentyeadending = strtotime("30 June ".date("Y"));
        if($currentyeadending < time()){
            $currentyeadending = strtotime("30 June ".date("Y", strtotime("+1 year")));
        }
        if(!empty($PARENTUSER->currentChild)){
            $currentChild = $PARENTUSER->currentChild;
            $type = "self";
            if($currentChild->currentSubscription){
                $type = "manual";
            }
            self::enrolCourse($courseid, $currentChild->id, $type, $currentyeadending);
        }
    }
    private function enrolCourse($courseid, $userid, $type, $currentyeadending) {  
        global $DB, $CFG;
        $type = "self";
        if (!$DB->record_exists_sql('select e.*, ue.userid from {user_enrolments} ue inner join {enrol} e on e.id = ue.enrolid and ue.userid=? and e.courseid=?', array($userid, $courseid))) {
            $enrollmentID = $DB->get_record_sql('SELECT * FROM {enrol} WHERE enrol = ? AND courseid = ?', array($type, $courseid));
            if(!empty($enrollmentID->id)) {
                $currecttime = time();
                $enrol_manual = enrol_get_plugin($type); 
                $enrol_manual->enrol_user($enrollmentID, $userid, 5, time(), 0);
            }
        } else {
            $enrollment = $DB->get_record_sql('select ue.* from {user_enrolments} ue inner join {enrol} e on e.id = ue.enrolid and ue.userid=? and e.courseid=?', array($userid, $courseid));
            if($enrollment->timeend < strtotime("-1 day")){
                $enrollment->timeend = 0;
                $DB->update_record("user_enrolments", $enrollment);
            }
        }
    }
    private function isInternalUser($courseid, $userid) {  
        global $DB, $CFG, $ISINTERNAL;
        $type = "self";
        if ($DB->record_exists_sql('select ue.* from {user_enrolments} ue inner join {enrol} e on e.id = ue.enrolid and ue.userid=? and e.courseid=? and e.enrol != ?', array($userid, $courseid, $type))) {
            // $ISINTERNAL = true;
            return true;
        }
        return false;
    }
    private function removeSelfEnrolment($courseid, $userid){
        global $DB, $CFG;
        if($enrol = $DB->get_record("enrol", array("courseid"=>$courseid, "enrol"=>"self"))){
            if($DB->get_record("user_enrolments", array("enrolid"=>$enrol->id, "userid"=>$userid))){
                if(file_exists($CFG->dirroot."/enrol/self/lib.php")){
                    require_once($CFG->dirroot."/enrol/self/lib.php");
                    $slefenrol = new enrol_self_plugin();
                    $slefenrol->unenrol_user($enrol, $userid);
                }
            }
        }
    }
    public function getChildAccess($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid,$free, $gradeid;
        $childid = $args['childid'];
        $devicetoken = $args['devicetoken'];
        $devicename = $args['devicename'];
        $errors = array();
        if(empty($childid)){
            array_push($errors, "Missing parameter childid");
        }
        if(empty($devicetoken)){
            array_push($errors, "Missing parameter devicetoken");
        }
        if(empty($devicename)){
            array_push($errors, "Missing parameter devicename");
        }
        if(sizeof($errors) > 0){
            $this->sendError("failed to assign Grade", implode(", ", $errors));
        } else {
            $currentChild = array_filter($PARENTUSER->children, function($v, $k) {
                global $childid,$free;
                return $v->id == $childid;
            }, ARRAY_FILTER_USE_BOTH);
            if(empty($currentChild)){
                $this->sendError("failed to get access-", "Invalid Childid", 401);
            } else {
                $currentChild= array_pop($currentChild);
                if(empty($currentChild->currentSubscription)){
                    $currentyeadending = strtotime("30 June ".date("Y"));
                    if($currentyeadending < time()){
                        $currentyeadending = strtotime("30 June ".date("Y", strtotime("+1 year")));
                    }
                    $insertenroledata = new stdClass();
                    $insertenroledata->user_id     = $PARENTUSER->id;
                    $insertenroledata->subscription_id     = 0;
                    $insertenroledata->course_cat_id     = $currentChild->grade;
                    $insertenroledata->package     = "free";
                    $insertenroledata->free     = 1;
                    $insertenroledata->enrolment_date_from     = time();
                    $insertenroledata->enrolment_date_to     = $currentyeadending;
                    $insertenroledata->enrolsubscriptionid     = $currentyeadending;
                    $insertenroledata->assignedto     = $currentChild->id;
                    $insertenroledata->assigneddate     = time();
                    $insertenroledata->id=$DB->insert_record('custom_enrol_details', $insertenroledata);   
                    $childdata = new stdClass();
                    $childdata->id = $currentChild->childId;
                    $childdata->subscriptionid = $insertenroledata->id;
                    $childdata->subscriptionassigned = time();
                    $childdata->subscriptionmodified = time();
                    self::enrolluserincourses($currentChild->id, $currentChild->grade, "self", $currentyeadending);
                    $DB->update_record('childusers', $childdata);
                }
                $DB->delete_records('external_tokens', array('userid' => $currentChild->id));
                $DB->set_field('userlogindevices', 'loginstatus', 0, array('userid' => $currentChild->id));
                if($token = self::get_usertoken($currentChild->id)){
                    self::validatetoken($token);
                    $responsedata = new stdClass();
                    $responsedata->token = $token;
                    $responsedata->userDetails = $PARENTUSER;
                    $alllogindevices = $DB->get_records_sql("select * from {userlogindevices} where userid = ? and loginstatus= ? and token != ?", array($currentChild->id, 1, $devicetoken));
                    if(sizeof($alllogindevices)>=3){
                        foreach ($alllogindevices as $key => $logindevice) {
                            unset($alllogindevices[$key]->logintoken);
                            $alllogindevices[$key]->loginStatus = $alllogindevices[$key]->loginstatus;
                            $alllogindevices[$key]->createdDate = intval($alllogindevices[$key]->createddate);
                            $alllogindevices[$key]->modifiedDate = intval($alllogindevices[$key]->modifieddate);
                            unset($alllogindevices[$key]->loginstatus);
                            unset($alllogindevices[$key]->createddate);
                            unset($alllogindevices[$key]->modifieddate);
                        }
                        // $this->sendError("Failed to get access", array("message"=>"already loggedin on 3 Device", "logindevices"=>array_values($alllogindevices)), 420);
                        $this->sendError("Failed to get access", "already loggedin on 3 Device", 420);
                    } else {
                        self::successlogindevices($currentChild->id, $token, $devicetoken, $devicename, 1);
                        $this->sendResponse($responsedata);
                    }
                } else {
                    $this->sendError("Failed to get access", "Failed to get access");
                }
            }
        }
    }
    private function successlogindevices($userid, $logintoken, $devicetoken, $devicename, $status){
        global $DB;
        if(!$DB->record_exists("userlogindevices", array("userid"=>$userid, "token"=>$devicetoken, "loginstatus"=>1))){
            $logindevice = new stdClass();
            $logindevice->userid = $userid;
            $logindevice->logintoken = $logintoken;
            $logindevice->token = $devicetoken;
            $logindevice->name = $devicename;
            $logindevice->loginstatus = $status;
            $logindevice->status = $status;
            $logindevice->createddate = time();
            $DB->insert_record("userlogindevices", $logindevice);
        }
        $allotherdevices = $DB->get_records_sql("select * from {userlogindevices} where token = ? and loginstatus = ? AND userid != ? order by id desc", array($devicetoken, 1, $userid));
        foreach ($allotherdevices as  $device) {
            $device->loginstatus = 0;
            $device->modifieddate = time();
            $DB->update_record("userlogindevices", $device);
        }
    }
    private function get_usertoken($userid){
        global $CFG, $DB, $USER;
        $token = "";
        try {
            $user = $DB->get_record("user", array("id"=>$userid));
            if($user){
                if($query = $DB->get_record('external_tokens', array('userid' => $userid))){
                    return $query->token;
                } else {
                    require_once($CFG->libdir . '/externallib.php');
                    \core\session\manager::set_user($user);
                    $serviceshortname = "moodle_mobile_app";
                    $service = $DB->get_record('external_services', array('shortname' => $serviceshortname, 'enabled' => 1));
                    if(!empty($service)){
                        $tokendata = external_generate_token_for_current_user($service);
                        external_log_token_request($tokendata);
                        $token = $tokendata->token;
                    }
                }
            }
        } catch (Exception $e) {
            $token = "";
        }
        return $token;
    }
    public function removeChildAccess($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid,$free, $gradeid;
        $childid = $args['childid'];
        $devicetoken = $args['devicetoken'];
        $errors = array();
        if(empty($childid)){
            array_push($errors, "Missing parameter childid");
        }
        if(empty($devicetoken)){
            array_push($errors, "Missing parameter devicetoken");
        }
        if(sizeof($errors) > 0){
            $this->sendError("failed to assign Grade", implode(", ", $errors));
        } else {
            $currentChild = array_filter($PARENTUSER->children, function($v, $k) {
                global $childid,$free;
                return $v->id == $childid;
            }, ARRAY_FILTER_USE_BOTH);
            if(empty($currentChild)){
                $this->sendError("failed to get access", "Invalid Childid");
            } else {
                $currentChild= array_pop($currentChild);
                if($logindevice = $DB->get_record("userlogindevices", array("userid"=>$currentChild->id, "token"=>$devicetoken, "loginstatus"=>1))){
                    $logindevice->loginstatus = 0;
                    $logindevice->modifieddate = time();
                    if($DB->update_record("userlogindevices", $logindevice)){
                        $this->sendResponse(array("message"=>"Successfuly loggedout"));
                    } else {
                        $this->sendError("Failed to loggout", "Failed to loggout");
                    }
                } else {
                    $this->sendError("Failed to loggout", "not loggedin to this devices");
                }
            }
        }
    }
    public function getUnassignedPacks($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid,$free, $gradeid;
        $gradeid = 0;
        if(!empty($PARENTUSER->currentChild)){
            $gradeid = $PARENTUSER->currentChild->grade;
        }
        $allpacks = array_filter($PARENTUSER->subscriptions, function($v, $k) {
            global $gradeid;
            return (empty($v->assignedTo) && (empty($gradeid) || $gradeid == $v->categoryId));
        }, ARRAY_FILTER_USE_BOTH);
        if(empty($allpacks)){
            $this->sendError("unassigned pack not found", "unassigned pack not found");
        } else {
            $this->sendResponse(array("allsubscription"=>array_values($allpacks)));
        }
    }
    public function assignpaidSubscription($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid, $subsid;
        $childid = $args['childid'];
        $subsid = $args['subscriptionid'];
        $errors = array();
        if(empty($childid)){
            array_push($errors, "Missing parameter childid");
        }
        if(empty($subsid)){
            array_push($errors, "Missing parameter subscriptionid");
        }
        if(sizeof($errors) > 0){
            $this->sendError("failed to assign Subscription", implode(", ", $errors));
        } else {
            $currentChild = array_filter($PARENTUSER->children, function($v, $k) {
                global $childid,$free;
                return $v->id == $childid;
            }, ARRAY_FILTER_USE_BOTH);
            if(empty($currentChild)){
                array_push($errors, "Invalid Childid");
            }
            $allpacks = array_filter($PARENTUSER->subscriptions, function($v, $k) {
                global $subsid;
                return (empty($v->assignedTo) && ($subsid == $v->id));
            }, ARRAY_FILTER_USE_BOTH);
            if(empty($allpacks)){
                array_push($errors, "Invalid Subscription selected");
            }
            if(sizeof($errors) > 0){
                $this->sendError("failed to assign Subscription", implode(", ", $errors));
            } else {
                $currentChild= array_pop($currentChild);
                $subscription= array_pop($allpacks);
                if(!empty($currentChild->currentSubscription)){
                    $this->sendError("failed to assign Grade", "Grade already assigned");
                } else if($currentChild->grade != $subscription->categoryId){
                    $this->sendError("failed to assign Grade", "Invalid Subscription selected");
                } else {
                    $currentyeadending = strtotime("30 June ".date("Y"));
                    if($currentyeadending < time()){
                        $currentyeadending = strtotime("30 June ".date("Y", strtotime("+1 year")));
                    }
                    $childdata = new stdClass();
                    $childdata->id = $currentChild->childId;
                    $childdata->subscriptionid = $subscription->id;
                    $childdata->subscriptionassigned = time();
                    $childdata->subscriptionmodified = time();
                    self::enrolluserincourses($currentChild->id, $currentChild->grade, "manual", $currentyeadending);
                    $DB->update_record('childusers', $childdata);
                    $subsdatadata = new stdClass();
                    $subsdatadata->id = $subscription->id;
                    $subsdatadata->assignedto = $currentChild->id;
                    $subsdatadata->assigneddate = time();
                    $DB->update_record('custom_enrol_details', $subsdatadata);
                    $this->sendResponse(array("message"=>"Successfull.."));
                }
            }
        }
    }
    public function sendInvites($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid, $subsid;
        $email = $args['email'];
        $errors = array();
        if(empty($email)){
            array_push($errors, "Missing parameter email");
        } else if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Invalid email: ".$email);
        }
        if(sizeof($errors) > 0){
            $this->sendError("failed to assign Subscription", implode(", ", $errors));
        } else {
            $currentChild = array_filter($PARENTUSER->children, function($v, $k) {
                global $childid,$free;
                return $v->id == $childid;
            }, ARRAY_FILTER_USE_BOTH);
            if(empty($currentChild)){
                array_push($errors, "Invalid Childid");
            }
            $allpacks = array_filter($PARENTUSER->subscriptions, function($v, $k) {
                global $subsid;
                return (empty($v->assignedTo) && ($subsid == $v->id));
            }, ARRAY_FILTER_USE_BOTH);
            if(empty($allpacks)){
                array_push($errors, "Invalid Subscription selected");
            }
            if(sizeof($errors) > 0){
                $this->sendError("failed to assign Subscription", implode(", ", $errors));
            } else {
                $currentChild= array_pop($currentChild);
                $subscription= array_pop($allpacks);
                if(!empty($currentChild->currentSubscription)){
                    $this->sendError("failed to assign Grade", "Grade already assigned");
                } else if($currentChild->grade != $subscription->categoryId){
                    $this->sendError("failed to assign Grade", "Invalid Subscription selected");
                } else {
                    $currentyeadending = strtotime("30 June ".date("Y"));
                    if($currentyeadending < time()){
                        $currentyeadending = strtotime("30 June ".date("Y", strtotime("+1 year")));
                    }
                    $childdata = new stdClass();
                    $childdata->id = $currentChild->childId;
                    $childdata->subscriptionid = $subscription->id;
                    $childdata->subscriptionassigned = time();
                    $childdata->subscriptionmodified = time();
                    self::enrolluserincourses($currentChild->id, $currentChild->grade, "manual", $currentyeadending);
                    $DB->update_record('childusers', $childdata);
                    $subsdatadata = new stdClass();
                    $subsdatadata->id = $subscription->id;
                    $subsdatadata->assignedto = $childdata->id;
                    $subsdatadata->assigneddate = time();
                    $DB->update_record('custom_enrol_details', $subsdatadata);
                    $this->sendResponse(array("message"=>"Successfull.."));
                }
            }
        }
    }
    public function prerequisiteData($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid, $subsid;
        $responsedata = array();
        $signupurl = array("fr"=>"https://fivestudents.com/free-trial/", "ar"=>"https://fivestudents.com/ar/free-trial-a/");
        $buyurl = array("fr"=>"https://fivestudents.com/pricing_najah/", "ar"=>"https://fivestudents.com/ar/pricing-najah/");
        $userlang = $args['lang'];
        $alllangs = get_string_manager()->get_list_of_translations();
        if(!empty($userlang) && array_key_exists($userlang, $alllangs)){
            $this->userlangauge = $userlang;
        }
        $responsedata['links'] = self::get_links($this->userlangauge);
        $responsedata['videos'] = self::get_videos($this->userlangauge);
        $responsedata['audios'] = self::get_audios($this->userlangauge);
        $this->sendResponse($responsedata);
    }
    public function get_links($lang = ""){
        global $DB, $CFG, $PARENTUSER;
        $encodeurl = "";
        if(!empty($PARENTUSER)){
            $encodeurl = $CFG->wproot."/app-autologin/?access=".base64_encode(md5(strtolower($PARENTUSER->emailId)))."&to=";
        }
        $alllinks = array();
        if(empty($lang)){
            $lang = $this->userlangauge;
        }
        $type =  array("buyLink"=>"Buy Link", "signupLink"=>"Signup Link", "privacy"=>"Privacy", "terms"=>"Terms and condition link", "faqLink"=>"Terms and condition link", "reportLink"=>"Terms and condition link", "forgotPassword"=>"Forgot Password link" );

        foreach ($type as $key => $value) {
            $link = $DB->get_field_sql("select link from {managelinks} where lang =? and type = ?", array($lang, $key));
            if($key=="forgotPassword"){
                $alllinks[$key] = ($link)?$link:"";
            } else {
                $alllinks[$key] = ($link)?$encodeurl.$link:"";
            }
        }
        return $alllinks;
    }
    public function get_audios($lang = ""){
        global $DB, $CFG;
        $alllinks = array();
        if(empty($lang)){
            $lang = $this->userlangauge;
        }
        $type =  array("quiz_success"=>2, "quiz_fail"=>2, "quiz_intro"=>5, "quiz_init_dialogue"=>5, "quiz_start_prelude"=>5);

        foreach ($type as $key => $value) {
            if(!isset($alllinks[$key])){
                $alllinks[$key] = array();
            }
            for ($i=1; $i <= $value; $i++) { 
                $alllinks[$key][] = self::get_deginersfiles("staticaudios".$lang, $key, $i);
            }
        }
        return $alllinks;
    }
    public function get_videos($lang = ""){
        global $DB, $CFG;
        $allrecords = $DB->get_records_sql("select * from {managevideos} where active = 1 order by category, lang, sortorder", array("active"));
        $allvideos = array();
        foreach ($allrecords as $key => $record) {
            $newdata = new stdClass();
            $newdata->name = $record->title;
            $newdata->videoUrl = $record->url;
            $newdata->thumbnilUrl = $record->thumburl;
            $newdata->description = $record->description;
            $newdata->duration = intval($record->duration)*1000;
            $newdata->grade = $record->category;
            $newdata->lang = $record->lang;
            $newdata->order = $record->sortorder;
            $newdata->publishedDate = $record->publisheddate;
            $newdata->active = $record->active;
            array_push($allvideos, $newdata);
        }
        return $allvideos;
    }
    public function getDeviceList($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid, $subsid;
        $responsedata = array();
        $alllogindevices = $DB->get_records_sql("select * from {userlogindevices} where userid = ? and loginstatus= ?", array($PARENTUSER->currentChild->id, 1));
        foreach ($alllogindevices as $key => $logindevice) {
            unset($alllogindevices[$key]->logintoken);
            $alllogindevices[$key]->loginStatus = $alllogindevices[$key]->loginstatus;
            $alllogindevices[$key]->createdDate = intval($alllogindevices[$key]->createddate);
            $alllogindevices[$key]->modifiedDate = intval($alllogindevices[$key]->modifieddate);
            unset($alllogindevices[$key]->loginstatus);
            unset($alllogindevices[$key]->createddate);
            unset($alllogindevices[$key]->modifieddate);
        }
        $responsedata['devices'] = array_values($alllogindevices);
        $this->sendResponse($responsedata);
    }
    public function updateFcmDevice($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid, $subsid;
        $responsedata = array();
        $fcmid = $args["fcmid"];
        $devicetoken = $args["devicetoken"]?$args["devicetoken"]:"";
        $devicename = $args["devicename"]?$args["devicename"]:"";
        $lang = $args["lang"]?$args["lang"]:"fr";
        if(empty($fcmid)){
            $this->sendError("failed to update Device", "FCM id is required");
        } else {
            $check = array("active"=>1, "parentid"=>$PARENTUSER->id, "fcmid"=>$fcmid);
            if(isset($PARENTUSER->currentChild->id)){
                $check['childid'] = $PARENTUSER->currentChild->id;
            } else {
                $check['childid'] = null;
            }
            if($olddata = $DB->get_record("userfcmdevices", $check)){
                $responsedata['status'] = 1;
                $responsedata['message'] = "already registered";
                $olddata->lang = $lang;
                $olddata->devicetoken = $devicetoken;
                $olddata->devicename = $devicename;
                $olddata->modifiedtime = time();
                if($DB->update_record("userfcmdevices", $olddata)){
                    $responsedata['status'] = 1;
                    $responsedata['message'] = "Updated Successfuly";
                    $this->sendResponse($responsedata);
                } else {
                    $this->sendError("failed to update Device", "failed to update Device, please try again");
                }
                $this->sendResponse($responsedata);
            } else {
                $data = new stdClass();
                $data->parentid = $PARENTUSER->id;
                if(isset($PARENTUSER->currentChild->id)){
                    $data->childid = $PARENTUSER->currentChild->id;
                }
                $data->fcmid = $fcmid;
                $data->lang = $lang;
                $data->devicetoken = $devicetoken;
                $data->devicename = $devicename;
                $data->active = 1;
                $data->createddate = time();
                if($DB->insert_record("userfcmdevices", $data)){
                    $responsedata['status'] = 1;
                    $responsedata['message'] = "Updated Successfuly";
                    $this->sendResponse($responsedata);
                } else {
                    $this->sendError("failed to update Device", "failed to update Device, please try again");
                }
            }
        }
    }
    public function get_categorygrade($categoryid=0){
        global $CATGRADE, $PARENTUSER, $XPSETTING;
        if(!empty($XPSETTING->roundon)){
            $grade = $XPSETTING->roundon;
        }
        if(empty($grade)){
            $grade = 10;
        }
        $CATGRADE = $grade;
        return $grade;
    }
    public function set_categorygrade($categoryid=0){
        global $DB, $CATGRADE, $PARENTUSER, $XPSETTING;
        $XPSETTING = $DB->get_record("xpsetting", array("gradeid"=>$categoryid));
    }
    function cleanse_option($option){

        $option =  strip_tags($option, array("<span>","<br>", "<strong>", "<li>", "<ul>", "<li>","<sup>","<sub>","<img>", '<i>', '<b>', '<u>', '<a>'));
        $option = str_replace("\r\n", "", $option);
        return $option;
    }
    public function deleteChild($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid,$free, $gradeid;
        $childid = $args['childid'];
        $errors = array();
        // if(empty($childid)){
        //     $this->sendError("Missing Child", "Missing Child");
        // } else if($child = $DB->get_record("childusers", array("parentid"=>$PARENTUSER->id, "userid"=>$childid))){
        //     if($DB->delete_records("childusers", array("parentid"=>$PARENTUSER->id, "userid"=>$childid))){
        //         $this->sendResponse(array("message"=>"Successfuly deleted"));
        //     } else {
        //         $this->sendError("Failed to delete child", "Failed to delete child");
        //     }
        // } else {
        //     $this->sendError("Unable to get your Child", "Unable to get your Child");
        // }
        $this->sendError("API Disabled", "API Disabled");
    }
    public function getRegions($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid,$free, $gradeid;
        $selectedregion = $args['region'];
        $allregions = array(
            "Beni Mellal-Khénifra"=>array("Province de Béni-Mellal", "Province de Azilal", "Province de Fquih Ben Salah", "Khenifra Province", "Khouribga Province"),
            "Casablanca-Settat"=>array("Prefecture de Casablanca", "Mohammedia Prefecture", "Province de El Jadida", "Province de Nouaceur", "Province de Médiouna", "Province de Benslimane", "Province de Berrechid", "Settat Province", "Province de Sidi Bennour"),
            "Dakhla-Oued Ed-Dahab"=>array("Province de Oued Ed Dahab", "Province de Aousserd"),
            "Draa-Tafilalet"=>array("Errachidia Province", "Ouarzazate Province", "Midelt Province", "Tinghir Province", "Zagora Province"),
            "Fez-Meknes"=>array("Prefecture de Fez", "Meknes Prefecture", "Province de El Hajeb", "Province de Ifrane", "Moulay Yaâcoub Province", "Sefrou Province", "Province de Boulemane", "Taounate Province", "Province de Taza"),
            "Guelmim-Oued Noun"=>array("Guelmim Province", "Assa-Zag Province", "Tan-Tan Province", "Province de Sidi Ifni"),
            "Laayoune-Sakia El Hamra"=>array("Province de Laâyoune", "Boujdour Province", "Tarfaya Province", "Es-Semara Province"),
            "Marrakech-Safi"=>array("Prefecture de Marrakech", "Chichaoua Province", "Al Haouz Province", "Province de El Kelaâ des Sraghna", "Province de Essaouira", "Rehamna Province", "Safi Province", "Youssoufia Province"),
            "Oriental"=>array("Oujda-Angad Prefecture", "Province de Nador", "Driouch Province", "Jerada Province", "Province de Berkane", "Taourirt Province", "Guercif Province", "Province de Figuig"),
            "Rabat-Salé-Kénitra"=>array("Prefecture de Rabat", "Prefecture de Salé", "Skhirate-Témara Prefecture", "Province de Kenitra", "Province de Khemisset", "Province de Sidi Kacem", "Province de Sidi Slimane"),
            "Souss-Massa"=>array("Prefecture de Agadir Ida-Outanane", "Prefecture de Inezgane-Aït Melloul", "Province de Chtouka-Aït Baha", "Province de Taroudant", "Tiznit Province", "Tata Province"),
            "Tangier-Tetouan, Al Hoceima"=>array("Tangier-Assilah Prefecture", "Prefecture de M'diq-Fnideq", "Tetouan Province", "Fahs-Anjra Province", "Province de Larache", "Al Hoceïma Province", "Chefchaouen Province", "Province de Ouezzane"),
            "Other"=>array("Autre")
        );
        $alldata = array();
        if(!empty($selectedregion)){
            if(in_array($selectedregion, $allregions) && is_array($allregions[$selectedregion])){
                $alldata = $allregions;
            }
        } else {
            foreach ($allregions as $region => $provinces) {
                sort($provinces);
                $allregions[$region] = $provinces;
                array_push($alldata, $region);
            }
        }
        $this->sendResponse(array("data"=>$alldata, "alldata"=>$allregions));
    }
    public function getGradelevels($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $allcategory = self::get_category($parent=0);
        if(sizeof($allcategory)){
            $this->sendResponse(array("allgrade"=>$allcategory));
        } else {
            $this->sendError("Failed to get grades", "grade not found");
        }
    }
    public function get_category($parent=0){
        global $DB, $USER, $CFG, $PARENTUSER;
        $allcategory = array();
        $categories = $DB->get_records_sql("select id, name, parent, visible from {course_categories} where parent = ? and visible=1 AND id != 1 order by sortorder asc", array($parent));
        foreach ($categories as $key => $category) {
            if(in_array($category->id, $this->skippedcategory)){continue;}
            $category->subGrade = self::get_category($category->id);
            $category->isLaunched = ($category->visible)?true:false;
            array_push($allcategory, $category);
        }
        return $allcategory;
    }
    public function getValidateUsername($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $username = $args['username'];
        if($DB->record_exists("user", array("username"=>$username))){
            $suggestedusername = self::getusernamesuggestion($username, array());
            $this->sendError("error", "Username already taken", 301,$suggestedusername );
        } else {
            $this->sendResponse(array("status"=>"Success"));
        }
    }
    private function getusernamesuggestion($username, $allsuggestion = array()){
        global $DB;
        $allchar = array("",".","-","_","","@");
        shuffle($allchar);
        $suggestionchar = array_pop($allchar);
        $newusername = $username.$suggestionchar.rand(10,1000);
        if(!$DB->record_exists("user", array("username"=>$newusername))){
            array_push($allsuggestion, $newusername);
        }
        if(sizeof($allsuggestion) < 5){
            $allsuggestion = self::getusernamesuggestion($username, $allsuggestion);
        }
        return $allsuggestion;
    }
    private function getusernamefromcharacter($charname){
        global $DB;
        $allchar = array("",".","-","_","","@");
        shuffle($allchar);
        $suggestionchar = array_pop($allchar);
        $newusername = $charname.$suggestionchar.rand(1000,1000000000);
        if(!$DB->record_exists("user", array("username"=>$newusername))){
            return $newusername;
        }
        $newusername = self::getusernamesuggestion($charname);
        return $newusername;
    }
    private function generateRandomString($length = 5) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function registerAdventure($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        /*removed first and lastname*/
        $firstname = "Child";
        $lastname = "user".time();
        /*removed first and lastname*/
        $grade = $args["grade"];
        $charname = trim($args["charname"]);
        $firstname = $args["firstname"];
        $lastname = $args["lastname"];
        $gender = $args["gender"];
        $region = $args["region"];
        $provinces = $args["provinces"];
        $devicetoken = $args["devicetoken"];
        $devicename = $args["devicename"];
        $referralCode=$args['referralCode'];
        $username= self::getusernamefromcharacter($charname);
        $allimage = array();
        $allcharacterimage = $DB->get_records("characterimages", array("gender"=>$gender)); 
        foreach ($allcharacterimage as $key => $characterimage) {
            if(strpos($characterimage->image, "://")){
                array_push($allimage, $characterimage->image);
            } else {
                array_push($allimage, $CFG->wwwroot."/local/designer/images/characters/".$characterimage->image);
            }
        }
        shuffle($allimage);
        $image = array_pop($allimage);
        $errors = array();
        /*removed first and lastname*/
        // $allpart = explode(" ", $charname);
        // if(sizeof($allpart) > 1){
        //     $firstname = array_pop($allpart);
        //     $lastname = implode(" ", $allpart);

        // } else {
        //     $firstname = $charname;
        //     $lastname = $charname;
        // }
        /*removed first and lastname*/
        if(empty($grade)){
            array_push($errors, "Grade is required");
        }
        if(empty($charname)){
            array_push($errors, "Character name is required");
        }
        if(empty($firstname)){
            array_push($errors, "Firstname is required");
        }
        if(empty($lastname)){
            array_push($errors, "Lastname is required");
        }
        if(sizeof($PARENTUSER->children) >= 3){
            array_push($errors, "You  already have 3 Adventure");
        }
        if(sizeof($errors)>0){
            $this->sendError("Failed to create adventure ", implode(", ", $errors));
        } else {
            $userinsert  = new stdClass();
            $userinsert->username     = $username;
            $userinsert->password     = md5($password);
            $userinsert->firstname    = $firstname;
            $userinsert->lastname     = $lastname;
            $userinsert->email        = $userinsert->username."@testmail".rand(0,1000).".com";
            $userinsert->timecreated  = time();
            $userinsert->timemodified = time();
            $userinsert->middlename   = " ";
            $userinsert->confirmed    = 1;
            $userinsert->mnethostid   = 1;
            $userinsert->alternatename   = $charname;
            $userinsert->id = $DB->insert_record('user', $userinsert);
            if($userinsert->id){
                $childdata = new stdClass();
                $childdata->parentid = ($PARENTUSER->id?$PARENTUSER->id:$userinsert->id);
                $childdata->realparentid = ($PARENTUSER->id?$PARENTUSER->id:$userinsert->id);
                $childdata->userid = $userinsert->id;
                $childdata->grade = $grade;
                $childdata->gender = $gender;
                $childdata->image = ($image)?$image:"";
                $childdata->region = $region;
                $childdata->provinces = $provinces;
                $childdata->createdby = $USER->id;
                $childdata->createddate = time();
                $childdata->id =$DB->insert_record('childusers', $childdata);
                $childdata->referralcode = self::generateRandomString(rand(3,6)).$childdata->id.self::generateRandomString(rand(3,6));
                $this->checknewdevice($devicetoken, $userinsert->id, $grade);
                $DB->update_record("childusers", $childdata);
                $this->referraluser($devicetoken, $referralCode, $childdata);
                $this->registerionbonus($childdata);
                if($token = self::get_usertoken($userinsert->id)){
                    //$allotherdevices = $DB->get_records_sql("select * from {userlogindevices} where token = ? and loginstatus = ? and userid != ?", array($token, 1, $userid));

                    /*
                          if(!$DB->record_exists("userlogindevices", array("userid"=>$userid, "token"=>$devicetoken, "loginstatus"=>1))){
            $logindevice = new stdClass();
            $logindevice->userid = $userid;
            $logindevice->logintoken = $logintoken;
            $logindevice->token = $devicetoken;
            $logindevice->name = $devicename;
            $logindevice->loginstatus = $status;
            $logindevice->status = $status;
            $logindevice->createddate = time();
            $DB->insert_record("userlogindevices", $logindevice);
        }
                    */
        /* if(!$DB->record_exists("userlogindevices", array("userid"=>$userid, "token"=>$devicetoken, "loginstatus"=>1))){
            //$gradesettingsql="SELECT * FROM  "
         }*/

                    self::validatetoken($token);
                    self::successlogindevices($userinsert->id, $token, $devicetoken, $devicename, 1);
                    $responsedata = new stdClass();
                    $responsedata->status = 1;
                    $responsedata->message = "Adeventure created";
                    $responsedata->token = $token;
                    $responsedata->id = $userinsert->id;
                    $responsedata->userDetails = $PARENTUSER;
                    $this->sendResponse($responsedata);
                } else {
                    $responsedata = new stdClass();
                    $responsedata->status = 1;
                    $responsedata->id = $userinsert->id;
                    $responsedata->message = "Adeventure created, but failed to get token";
                    $responsedata->token = "";
                    $responsedata->userDetails = null;
                    $this->sendResponse($responsedata);
                }
            } else {
                $this->sendError("Failed to create Adventure", "Failed to create Adventure");
            }
        }
    }
    public function registerionbonus($childdata){
        global $DB, $USER, $CFG, $PARENTUSER;
        if(empty($childdata->userid)){
            return;
        }
        $wallet1 = self::get_wallet($childdata->userid);
        if($amount=$DB->get_field_sql("select registerionbonus from {xpsetting} where gradeid=?", array($childdata->grade) )){
        } else {
            $amount = $amount=$DB->get_field_sql("select registerionbonus from {xpsetting} where gradeid=?", array(0));
        }
        if(empty($amount)){
            return;
        }
        $history = new stdClass();
        $history->userid = $childdata->userid;
        $history->type= "signupbonus";
        $history->amount = $amount;
        $history->oldbalance= $wallet1->ballance;
        $history->newbalance = $history->oldbalance + $history->amount;
        $history->fromuser = $user->id;
        $history->createddate = time();
        if($DB->insert_record("userwallethistory", $history)){
            $walletupdate = new stdClass();
            $walletupdate->id = $wallet1->id;
            $walletupdate->ballance = $history->newbalance;
            $walletupdate->modifieddate = time();
            $DB->update_record("userwallet", $walletupdate);
        }
    }
    public function referraluser($devicetoken, $referralCode, $childdata){
        global $DB, $USER, $CFG, $PARENTUSER;
        if(empty($childdata) || empty($referralCode) || empty($devicetoken) || $DB->record_exists("userlogindevices", array("token"=>$devicetoken)) || !$DB->record_exists("childusers", array("referralcode"=>$referralCode))){
            return;
        }
        if($refdata=$DB->get_record("childusers",array("referralcode"=>$referralCode))){
            if($amount=$DB->get_field_sql("select referralcoin from {xpsetting} where gradeid=?", array($childdata->grade) )){
            } else {
                $amount = $amount=$DB->get_field_sql("select referralcoin from {xpsetting} where gradeid=?", array(0));
            }
            if(empty($amount)){
                return;
            }
            $wallet1 = self::get_wallet($refdata->userid);
            $wallet = self::get_wallet($childdata->userid);
            $history = new stdClass();
            $history->userid = $childdata->userid;
            $history->type= "referedfrom";
            $history->amount = $amount;
            $history->oldbalance= 0;
            $history->newbalance = $history->oldbalance + $history->amount;
            $history->fromuser = $refdata->userid;
            $history->createddate = time();
            if($DB->insert_record("userwallethistory", $history)){
                $wallet->ballance = $history->newbalance;
                $wallet->modifieddate = time();
                $DB->update_record("userwallet", $wallet);
                $history1 = new stdClass();
                $history1->userid = $refdata->userid;
                $history1->type= "refereduser";
                $history1->amount = $amount;
                if(!empty($wallet1)){
                    $history1->oldbalance = intval($wallet1->ballance);
                } else {
                    $history1->oldbalance= 0;
                }
                $history1->newbalance = $history1->oldbalance + $history1->amount;
                $history1->fromuser = $childdata->userid;
                $history1->createddate = time();
                $DB->insert_record("userwallethistory", $history1);
                $walletupdate = new stdClass();
                $walletupdate->id = $wallet1->id;
                $walletupdate->ballance = $history1->newbalance;
                $walletupdate->modifieddate = time();
                $DB->update_record("userwallet", $walletupdate);

                // $DB->execute("update {wallet} set ballance=? and modifieddate=? where id=?", array($history1->newbalance, time(), $wallet1->id));
            }
        }
    }
    public function getLastAdventure($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $devicetoken = $args["devicetoken"];
        $fromtime = strtotime("1 September 2021");
        // echo "select u.* from {userlogindevices} ud inner join {user} u on u.id=ud.userid and u.deleted = 0 where ud.token=? and ud.loginstatus=? and ud.createddate > ? order by ud.id desc";
        // print_r(array($devicetoken,1, $fromtime));
        $loginuser = $DB->get_record_sql("select u.*, c.parentid from {userlogindevices} ud inner join {user} u on u.id=ud.userid and u.deleted = 0 and u.confirmed=1 inner join {childusers} c on c.userid = u.id AND c.deleted=0 where ud.token=? and ud.loginstatus=? and u.timecreated > ? order by ud.id desc", array($devicetoken,1, $fromtime));
        if(!empty($loginuser)){
            if($token = self::get_usertoken($loginuser->id)){
                self::validatetoken($token);
                $responsedata = new stdClass();
                $responsedata->status = 1;
                $responsedata->message = "Account Found";
                $responsedata->token = $token;
                $responsedata->id = $loginuser->id;
                $responsedata->userDetails = $PARENTUSER;
                $this->sendResponse($responsedata);
            } else {
                $responsedata = new stdClass();
                $responsedata->status = 0;
                $responsedata->id = $loginuser->id;
                $responsedata->message = "User Found, but failed to get token";
                $responsedata->token = "";
                $responsedata->userDetails = null;
                $this->sendResponse($responsedata);
            }
        } else {
            $responsedata = new stdClass();
            $responsedata->status = 1;
            $responsedata->id = 0;
            $responsedata->message = "User not Found";
            $responsedata->token = "";
            $responsedata->userDetails = null;
            $this->sendResponse($responsedata);
        }

    }
    public function addCoinToWallet($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $type = $args["type"];
        $value = $args["coins"];
        $alltype = array("spinnwheel", "spinnwheelads", "ads");
        if(empty($value)){
            $this->sendError("Failed to update Wallet", "Coins required");
        } else if(in_array($type, $alltype)){
            $wallet = self::get_wallet();
            $history = new stdClass();
            $history->userid = $USER->id;
            $history->type= $type;
            $history->amount = intval($value);
            if(!empty($wallet)){
                $history->oldbalance= intval($wallet->ballance);
            } else {
                $history->oldbalance= 0;
            }
            $history->newbalance = $history->oldbalance + $history->amount;
            $history->fromuser = 0;
            $history->createddate = time();
            if($DB->insert_record("userwallethistory", $history)){
                if(!empty($wallet)){
                    $wallet->ballance = $history->newbalance;
                    $wallet->modifieddate = time();
                    $DB->update_record("userwallet", $wallet);
                } else {
                    $wallet = new stdClass();
                    $wallet->userid = $USER->id;
                    $wallet->ballance = $history->newbalance;
                    $wallet->createddate = time();
                    $wallet->modifieddate = time();
                    $DB->insert_record("userwallet", $wallet);
                }
                $wallet = self::get_wallet();
                $this->sendResponse(array("status"=>1, "message"=>"Successfully updated Wallet", "balance"=>$wallet->ballance));
            } else {
                $this->sendError("Failed to update Wallet", "request failed");
            }
        } else {
            $this->sendError("Failed to update Wallet", "Invalid Transection type");
        }
    }
    public function getWalletDetails($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $wallethistory = self::get_wallethistory();
        $wallet = self::get_wallet();
        $this->sendResponse(array("walletHistory"=>$wallethistory, "balance"=>(!empty($wallet)?$wallet->ballance:0)));
    }
    private function get_wallet($userid=0){
        global $DB, $USER, $CFG, $PARENTUSER;
        if(empty($userid)){
            $userid = $USER->id;
        }
        if(!$DB->record_exists("userwallet", array("userid"=>$userid))){
            $user_wallet=new stdClass();
            $user_wallet->userid=$userid;
            $user_wallet->ballance=0;
            $user_wallet->user_remove_banner_ads=0;
            $user_wallet->user_remove_interstitial_ads=0;
            $user_wallet->user_hints=0;
            $user_wallet->user_explanations=0;
            $user_wallet->createddate=time();
            $user_wallet->id=$DB->insert_record("userwallet",$user_wallet);
            return $user_wallet;
        }
        return $DB->get_record("userwallet", array("userid"=>$userid));
        
    }
    private function get_wallethistory($userid=0){
        global $DB, $USER, $CFG, $PARENTUSER;
        if(empty($userid)){
            $userid = $USER->id;
        }
        $alltypedata = array("spinnwheel"=>"Daily rewards", "spinnwheelads"=>"Daily rewards and watched ads", "ads"=>"Watched ads", "transferfrom"=>"Transfered Amout");
        $wallethistory = array();
        $history = $DB->get_records_sql("select w.*, u.alternatename from {userwallethistory} w left join {user} u on u.id = w.fromuser where u.id=:userid", array("userid"=>$userid));
        foreach ($history as $key => $h) {
            $h->name = $alltypedata[$h->type];
            if($h->type == "transferfrom"){
                $h->name .= " to ".$h->alternatename;
            }
            unset($h->fromuser);
            array_push($wallethistory, $h);
        }
        return $wallethistory;
    }
    public function getOneTimeKey(){
        global $DB, $USER, $CFG, $PARENTUSER;
        $uniquekey = self::getnerateuniquekey();
        $insert = new stdClass();
        $insert->userid = $USER->id;
        $insert->uniquekey = $uniquekey;
        $insert->coins = 0;
        $insert->active = 1;
        $insert->createddate = time();
        if($DB->insert_record("useronetimekey", $insert)){
            $this->sendResponse(array("uniqueKey"=>$uniquekey));
        } else {
            $this->sendError("error", "Failed to create key");
        }
    }
    private function getnerateuniquekey(){
        global $DB, $USER, $CFG, $PARENTUSER;
        $uniquekey = rand(1000000, 9999999);
        if($DB->record_exists_sql("select * from {useronetimekey} where uniquekey=? and active=?", array($uniquekey, 1))){
            $uniquekey = self::getnerateuniquekey();
        }
        return $uniquekey;
    }
    public function transferBalance($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $uniquekey = $args['uniquekey'];
        $amount = intval($args['coins']);
        $wallet = self::get_wallet();
        if(empty($wallet) || $wallet->ballance < $amount){
            $this->sendError("error", "Your wallet balance is less then ".$amount);
        } else {
            $uniquekey = self::get_uniquekeyuser($uniquekey, "cointransfer");
            if(empty($uniquekey)){
                $this->sendError("error", "Unique key is invalid or expired");
            } else {
                $wallet1 = self::get_wallet($uniquekey->userid);
                $history = new stdClass();
                $history->userid = $uniquekey->userid;
                $history->type= "transferfrom";
                $history->amount = intval($amount);
                if(!empty($wallet1)){
                    $history->oldbalance= intval($wallet1->ballance);
                } else {
                    $history->oldbalance= 0;
                }
                $history->newbalance = $history->oldbalance + $history->amount;
                $history->fromuser = $USER->id;
                $history->createddate = time();
                if($DB->insert_record("userwallethistory", $history)){
                    if(!empty($wallet1)){
                        $wallet1->ballance = $history->newbalance;
                        $wallet1->modifieddate = time();
                        $DB->update_record("userwallet", $wallet1);
                    } else {
                        $wallet1 = new stdClass();
                        $wallet1->userid = $uniquekey->userid;
                        $wallet1->ballance = $history->newbalance;
                        $wallet1->createddate = time();
                        $wallet1->modifieddate = time();
                        $DB->insert_record("userwallet", $wallet1);
                    }
                    $wallet->balance = $wallet->balance - $history->amount;
                    $wallet->modifieddate = time();
                    $DB->update_record("userwallet", $wallet);
                    $history1 = new stdClass();
                    $history1->userid = $USER->id;
                    $history1->type= "transferto";
                    $history1->amount = intval($amount);
                    $history1->oldbalance= intval($wallet->ballance);
                    $history1->newbalance = $history1->oldbalance - $history1->amount;
                    $history1->fromuser = $uniquekey->userid;
                    $history1->createddate = time();
                    $DB->insert_record("userwallethistory", $history1);
                    $uniquekey->active = 0;
                    $uniquekey->useddate = time();
                    $DB->update_record("useronetimekey", $uniquekey);
                    
                    $this->sendResponse(array("status"=>1, "message"=>"Successfully updated Wallet"));
                } else {
                    $this->sendError("error", "Failed to transfer coins, please try after some time");
                }
            }
        }
    }
    public function getAmountReferenceNo($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $wallet = self::get_wallet();
        $coins = $args["coins"];
        if($wallet){
            if($wallet->ballance < $coins){
                $this->sendError("error", "You don't have enough balance in your wallet");
            } else {
                $uniquekey = self::getnerateuniquekey();
                $insert = new stdClass();
                $insert->userid = $USER->id;
                $insert->uniquekey = $uniquekey;
                $insert->keytype = "cointransfer";
                $insert->expiry = (24*3600);
                $insert->coins=$coins;
                $insert->active = 1;
                $insert->createddate = time();
                if($DB->insert_record("useronetimekey", $insert)){
                    $this->sendResponse(array("refno"=>$uniquekey, "coins"=>$coins));
                } else {
                    $this->sendError("error", "Failed to create key");
                }
            }
        } else {
            $this->sendError("error", "You don't have enough balance in your wallet");
        }
    }
    public function redeemAmountReferenceNo($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $uniquekeycode = $args['refno'];
        $sqlref="SELECT * FROM {useronetimekey} WHERE uniquekey=?";
        $refdata=$DB->get_record_sql($sqlref,array($uniquekeycode));
        $refuserid=$refdata->userid;
        $userdevicesql1="SELECT * FROM {userlogindevices} WHERE userid=?";
        $userdevicedata1=$DB->get_record_sql($userdevicesql1,array($USER->id));
        $userdevicesql2="SELECT * FROM {userlogindevices} WHERE userid=?";
        $userdevicedata2=$DB->get_record_sql($userdevicesql1,array($refuserid));
        $amount = intval($args['coins']);
        if($userdevicedata1->token!==$userdevicedata2->token){
            if($uniquekey = self::get_uniquekeyuser($uniquekeycode, "cointransfer")){
           
                $amount = $uniquekey->coins;
                $wallet = self::get_wallet($uniquekey->userid);
                if(empty($wallet) || $wallet->ballance < $amount){
                    $this->sendError("error", "Sender don't have enough balance");
                } else {
                    $wallet1 = self::get_wallet();
                    $history = new stdClass();
                    $history->userid = $USER->id;
                    $history->type= "transferfrom";
                    $history->amount = intval($amount);
                    if(!empty($wallet1)){
                        $history->oldbalance= intval($wallet1->ballance);
                    } else {
                        $history->oldbalance= 0;
                    }
                    $history->newbalance = $history->oldbalance + $history->amount;
                    $history->fromuser = $uniquekey->userid;
                    $history->createddate = time();
                    if($DB->insert_record("userwallethistory", $history)){
                        if(!empty($wallet1)){
                            $wallet1->ballance = $history->newbalance;
                            $wallet1->modifieddate = time();
                            $DB->update_record("userwallet", $wallet1);
                        } else {
                            $wallet1 = new stdClass();
                            $wallet1->userid = $USER->id;
                            $wallet1->ballance = $history->newbalance;
                            $wallet1->createddate = time();
                            $wallet1->modifieddate = time();
                            $DB->insert_record("userwallet", $wallet1);
                        }
                        $wallet->ballance = $wallet->ballance - $amount;
                        $wallet->modifieddate = time();
                        $DB->update_record("userwallet", $wallet);
                        $history1 = new stdClass();
                        $history1->userid = $uniquekey->userid;
                        $history1->type= "transferto";
                        $history1->amount = intval($amount);
                        $history1->oldbalance= intval($wallet->ballance);
                        $history1->newbalance = $history1->oldbalance - $history1->amount;
                        $history1->fromuser = $USER->id;
                        $history1->createddate = time();
                        $DB->insert_record("userwallethistory", $history1);
                        $uniquekey->active = 0;
                        $uniquekey->useddate = time();
                        $DB->update_record("useronetimekey", $uniquekey);
                        
                        $this->sendResponse(array("status"=>1, "message"=>"Successfully updated Wallet"));
                    } else {
                        $this->sendError("error", "Failed to transfer coins, please try after some time");
                    }
                }
            } else {
                $this->sendError("error", "La clé unique est invalide ou a expiré");
            }
        }else{
              $this->sendError("error", "Tu ne peux pas envoyer des pièces à un personnage sur le MÊME appareil.");
        }
    }
    private function get_uniquekeyuser($uniquekey, $keytype=null){
        global $DB, $USER, $CFG, $PARENTUSER;
        $keydata = $DB->get_record_sql("select * from {useronetimekey}  where uniquekey = ? and active =? and keytype = ?", array($uniquekey, 1, $keytype));
        if($keydata){
            if(empty($keydata->expiry)){
                $keydata->expiry = 5;
            }
            if($keydata->createddate+($keydata->expiry * 60) < strtotime()){
                return null;
            }
            return $keydata;
        }
    }
    public function getRewardDetails($args){
        global $DB,$CFG;

        $wallet = self::get_wallet();
        $xp = self::get_xp();
        $responsedata = array();
        $responsedata['walletBalance'] =(!empty($wallet->ballance)?$wallet->ballance:0);
        $responsedata['hints'] =(!empty($wallet->user_hints)?$wallet->user_hints:0);
        $responsedata['explanation'] =(!empty($wallet->user_explanations)?$wallet->user_explanations:0);
        $responsedata['xpLevel'] =$xp->level;
        $responsedata['maxXpLevel'] =$xp->maxlevel;
        $responsedata['maxXp'] =$xp->maxxp;
        $responsedata['currentXp'] =$xp->currentxp;
        $responsedata['dailyRewardConsummed'] =self::dailyRewardConsummed();
        // $responsedata['leaderBoard'] =self::get_leaderboard();
        $responsedata['removeBannerAds'] = (!empty($wallet->user_remove_banner_ads)?$wallet->user_remove_banner_ads:0);
        $responsedata['removeInterstitailAds'] =(!empty($wallet->user_remove_interstitial_ads)?$wallet->user_remove_interstitial_ads:0);
        $this->sendResponse($responsedata);
    }
    private function get_xp($userid = 0){
        global $DB,$CFG, $USER;
        if(empty($userid)){
            $userid = $USER->id;
        }
        $currentxp = 0;
        $level = 0;
        $maxxp = 10000;
        $maxlevel = 100;
        if($xph = $DB->get_record_sql("select sum(gotgrade) as currentxp from {xphistory} where userid=?", array($userid))){
            $currentxp = $xph->currentxp;
        }
        if($leveldata = $DB->get_record_sql("select * from {xplevel} where points > ? order by points asc", array($currentxp))){
            $level = $leveldata->level;
            $maxxp = $leveldata->points;
        }
        if($maxlaveldata = $DB->get_field_sql("select max(level) from {xplevel}", array())){
            $maxlevel = $maxlaveldata+1;
        }
        $xp = new stdClass();
        $xp->level = $level;
        $xp->maxlevel = strval($maxlevel);
        $xp->maxxp = $maxxp;
        $xp->currentxp = $currentxp;
        return $xp;
    }
    private function dailyRewardConsummed($userid = 0){
        global $DB,$CFG, $USER;
        if(empty($userid)){
            $userid = $USER->id;
        }
        $rewardstype = array("spinnwheel", "spinnwheelads");
        $starttime = strtotime(date("d F Y"));
        $endtime = strtotime(date("d F Y 23:59:59"));
        return $DB->record_exists_sql("select id from {userwallethistory} where userid=? and (type=? or type=?) and createddate > ? and createddate < ?", array($userid, "spinnwheel", "spinnwheelads", $starttime, $endtime));
    }
    private function get_leaderboard($userid = 0){
        global $DB,$CFG, $USER;
        if(empty($userid)){
            $userid = $USER->id;
        }
        $alldata = array();
        return $alldata;
    }
    //get package list api
    public function getPackages(){
        global $DB, $USER, $PARENTUSER;
        $allpackage = array();
        $noadd=array();
        $buy_coins=array();
        $packs=array();
        $subspacks=array();
        $allpackages = $DB->get_records_sql("select * from {packages} where gradeid=? or gradeid =?", array(0, $PARENTUSER->currentChild->grade));
        foreach ($allpackages as $key => $packagesData) {
              if($packagesData->remove_banner_ads ==1 || $packagesData->remove_interstitial_ads==1){
                array_push($noadd,$packagesData );
              }
              else if( $packagesData->hints >0 || $packagesData->explanations>0 ){
                array_push($packs, $packagesData);
              }
              else{
                array_push($buy_coins, $packagesData);
              }
        }
        $subspacks = array();
        $allsubspackages = $DB->get_records_sql("select * from {subspackages} where FIND_IN_SET(?, gradeids) AND status=? and mobile=? ", array($PARENTUSER->currentChild->grade,1,1));
        foreach ($allsubspackages as $key => $subspackage) {
            $subspackage->startdate = time();
            $subspackage->enddate = strtotime("+".$subspackage->duration." month", $subspackage->startdate);
            $subspackage->duration .= ' mois';

            array_push($subspacks, $subspackage);
        }

        $this->sendResponse(array("no_ads"=>$noadd,"buy_coins"=>$buy_coins,"packs"=>$packs,"subsPacks"=>$subspacks));
    }
    //
    //package purched api
    public function purchasedPackage($args){
        global $DB, $USER, $PARENTUSER;
        $user_id=$USER->id;
        $package_id=$args['package_id'];
        $transection_id=$args['transection_id'];
        $version=$args['version'];
        $arr=array();
        if($version==1){
            if(!empty($package_id) && !empty($transection_id)){
                $package_count=$DB->count_records('packages',array("id"=>$package_id));
                if($package_count){
                    $pack_data=$DB->get_record('packages',array('id'=>$package_id));
                    $pack_name='purchased_'.$pack_data->id;
                    $remove_banner_ads=$pack_data->remove_banner_ads;
                    $remove_interstitial_ads=$pack_data->remove_interstitial_ads;
                    $price=$pack_data->price;
                    $coins=$pack_data->coins;
                    $hints=$pack_data->hints;
                    $explanations=$pack_data->explanations;
                    $purchased_data=new stdClass();
                    $purchased_data->package_id=$package_id;
                    $purchased_data->user_id=$user_id;
                    $purchased_data->amount_paid=$price;
                    $purchased_data->transection_id=$transection_id;
                    $purchased_data->remove_interstitial_ads=$remove_interstitial_ads;
                    $purchased_data->remove_banner_ads=$remove_banner_ads;
                    $purchased_data->coins=$coins;
                    $purchased_data->hints=$hints;
                    $purchased_data->explanations=$explanations;
                    $purchased_data->createddate=time();
                    $ids=$DB->insert_record('purchased_package',$purchased_data);
                    if($ids){
                        $user_count=$DB->count_records('userwallet',array("userid"=>$user_id));
                        if($user_count){
                            $userwallet_data=$DB->get_record('userwallet',array('userid'=>$user_id));
                            $user_ballance=$userwallet_data->ballance;
                            $user_hints=$userwallet_data->user_hints;
                            $user_remove_banner_ads=$userwallet_data->user_remove_banner_ads;
                            $user_remove_interstitial_ads=$userwallet_data->user_remove_interstitial_ads;
                            $user_explanations=$userwallet_data->user_explanations;
                            $userdata=new stdClass();
                            $user_data_coints=$user_ballance+$coins;
                            $userdata->ballance=$user_data_coints;
                            $user_h=$user_hints+$hints;
                            $userdata->user_hints=$user_h;
                            $userdata->user_remove_banner_ads=$remove_banner_ads==1 ? 1:$user_remove_banner_ads;
                            $userdata->user_remove_interstitial_ads=$remove_interstitial_ads==1 ? 1:$user_remove_interstitial_ads;
                            $user_exp=$explanations+$user_explanations;
                            $userdata->user_explanations=$user_exp;
                            
                            $userdata->modifieddate=time();
                            $userdata->id=$userwallet_data->id;
                            $DB->update_record('userwallet',$userdata);

                            $wallet_his = new stdClass();
                            $wallet_his->userid=$USER->id;
                            $wallet_his->oldbalance=$user_ballance;
                            $wallet_his->newbalance=$coins;
                            $wallet_his->createddate=time();
                            $wallet_his->fromuser=0;
                            $wallet_his->amount=$price;
                            $wallet_his->type=$pack_name;
                            $DB->insert_record('userwallethistory',$wallet_his);
                        } else {
                            $userdata=new stdClass();
                            $userdata->ballance=$coins;
                            $userdata->user_hints=$hints;
                            $userdata->user_remove_banner_ads=$remove_banner_ads;
                            $userdata->user_remove_interstitial_ads=$remove_interstitial_ads;
                            $userdata->user_explanations=$explanations;
                            $userdata->createddate=time();
                            $DB->insert_record('userwallet',$userdata);
                            $wallet_his=new stdClass();
                            $wallet_his->userid=$USER->id;
                            $wallet_his->oldbalance=0;
                            $wallet_his->newbalance=$coins;
                            $wallet_his->createddate=time();
                            $wallet_his->fromuser=0;
                            $wallet_his->amount=$price;
                            $wallet_his->type=$pack_name;
                            $DB->insert_record('userwallethistory',$wallet_his);
                        }
                    }
                    $msg['msg']="User  wallet updated successfully";
                    array_push($arr,$msg);
                    $this->sendResponse($arr);
                } else {
                    $this->sendError("invalid package", "package not avl");
                }
            } else {
                 $this->sendError("something wrong", "Something Wrong Please try again!");
            }
        } else if($version==2){
            if(!empty($package_id) && !empty($transection_id)){
                if($package = $DB->get_record("subspackages", array("shortname"=>$package_id))){
                    if($DB->record_exists("subscriptiontransection", array("transectionid" => $transection_id))){
                        $this->sendError("something wrong", "Duplicate transectionid:".$transection_id);
                    } else {
                        $trans = new stdClass();
                        $trans->institutionid = 0;
                        $trans->gradeid = $PARENTUSER->currentChild->grade;
                        $trans->groupid = 0;
                        $trans->userid = $USER->id;
                        $trans->packageid = $package->id;
                        $trans->pakage = $package->shortname;
                        $trans->duration = $package->duration;
                        $trans->transectionid = $transection_id;
                        $trans->tranectiondate = time();
                        $trans->source = 0;
                        $trans->createdby = $USER->id;
                        $trans->paymentstatus = 1;
                        $trans->paymentdate = time();
                        $trans->paymentnotes = "From APP";
                        if($trans->id = $DB->insert_record("subscriptiontransection", $trans)){
                            if($this->prccessTrans($trans)){
                                $this->sendResponse("Success");
                            } else {
                                $this->sendError("something wrong", "Failed to update your rtansection");
                            }
                        } else {
                            $this->sendError("something wrong", "Failed to update your transection");
                        }
                    }
                } else {
                    $this->sendError("something wrong", "Something Wrong Please try again!");
                }
            } else {
                 $this->sendError("something wrong", "Something Wrong Please try again!");
            }
        } else {
            $this->sendError("something wrong", "Invalid purchase type");
        }
    }
    private function prccessTrans($trans){
        global $DB, $USER, $PARENTUSER;
        if($DB->record_exists("subscriptiondetails", array("subscriptiontransectionid" => $trans->id))){
            return false;
        } else {
            $oldsubs = $DB->get_record_sql("select * from {subscriptiondetails} where schoolyear=? and userid=? and gradeid=? order by id desc", array($this->currentschoolyear, $trans->userid, $trans->gradeid));
            $newsubs->schoolyear = $this->currentschoolyear;
            $newsubs->institutionid = 0;
            $newsubs->userid = $trans->userid;
            $newsubs->gradeid = $trans->gradeid;
            $newsubs->groupid = 0;
            $newsubs->packageid = $trans->packageid;
            $newsubs->startdate = time();
            $newsubs->subscriptiontransectionid = $trans->id;
            if(!empty($oldsubs) && $oldsubs->enddate > time()){
                $newsubs->startdate = $oldsubs->enddate;
            }
            $newsubs->enddate = strtotime("+".$trans->duration." month");
            if($newsubs->id = $DB->insert_record("subscriptiondetails", $newsubs)){
                $statusupdate = new stdClass();
                $statusupdate->id = $trans->id;
                $statusupdate->subscriptionid = $newsubs->id;
                $statusupdate->processedtime = time();
                $DB->update_record("subscriptiontransection", $statusupdate);
                return true;
            } else {
                return false;
            }
        }
    }
    //get request hints and explations api
    public function requestedQuizComponents($args){
        global $DB,$USER,$PARENTUSER;
        $type=$args['type'];
        $question_id=$args['question_id'];
        $quiz_id=$args['quiz_id'];
        $coinsToDeduct=$args['coins_to_deduct'];
        $freeComponent=$args['isFreeComponent'];
        $tournament_id=($args['tournament_id'])?$args['tournament_id']:0;
        $user_wallet=self::get_wallet();
        /*Temprarory code*/
        if($freeComponent){
            if($user_wallet->ballance <= $coinsToDeduct){
                $type  = "freecoins";
                $value = $coinsToDeduct;
                $wallet = self::get_wallet();
                $history = new stdClass();
                $history->userid = $USER->id;
                $history->type= $type;
                $history->amount = intval($value);
                if(!empty($wallet)){
                    $history->oldbalance= intval($wallet->ballance);
                } else {
                    $history->oldbalance= 0;
                }
                $history->newbalance = $history->oldbalance + $history->amount;
                $history->fromuser = 0;
                $history->createddate = time();
                if($DB->insert_record("userwallethistory", $history)){
                    if(!empty($wallet)){
                        $wallet->ballance = $history->newbalance;
                        $wallet->modifieddate = time();
                        $DB->update_record("userwallet", $wallet);
                    } else {
                        $wallet = new stdClass();
                        $wallet->userid = $USER->id;
                        $wallet->ballance = $history->newbalance;
                        $wallet->createddate = time();
                        $wallet->modifieddate = time();
                        $DB->insert_record("userwallet", $wallet);
                    }
                }                
            }
        }
        /*Temprarory code*/
        $type=$args['type'];
        $user_wallet=self::get_wallet();
        $hints=$user_wallet->user_hints;
        $explanations=$user_wallet->user_explanations;
        $coins=$user_wallet->ballance;
        $arr=array();
        array_push($arr,$user_wallet);
        $userwalletdata=new stdClass();
        if(!empty($type) && !empty($question_id) && !empty($quiz_id)){
            if($type=="hint"){
                if($hints){
                    $hints=$hints-1;
                    $userwalletdata->user_hints=$hints;
                    $userwalletdata->id=$user_wallet->id;
                    $DB->update_record("userwallet",$userwalletdata);
                    self::insentive_xp($type,$quiz_id,$tournament_id);
                    $this->sendResponse(array("status"=>1, "balance"=>$coins));
                }else{
                    if($coins>=$coinsToDeduct){
                        //$this->sendResponse($arr);
                        $old_coins=$coins;
                         $coins=$coins-$coinsToDeduct;
                         //$explanations=$explanations-1;
                         $userwalletdata->ballance=$coins;
                         $userwalletdata->id=$user_wallet->id;
                         $DB->update_record('userwallet',$userwalletdata);
                         $user_history=new stdClass();
                         $user_history->oldbalance=$old_coins;
                         $user_history->newbalance=$coins;
                         $user_history->type=$type;
                         $user_history->createddate=time();
                         $user_history->userid=$user_wallet->userid;
                         $msgdata=array("question_id"=>$question_id,"quiz_id"=>$quiz_id);
                         $user_history->message=json_encode($msgdata);
                         $DB->insert_record("userwallethistory",$user_history);
                         self::insentive_xp($type,$quiz_id,$tournament_id);
                        $this->sendResponse(array("status"=>1, "balance"=>$coins));
                    }else{
                        //$this->sendResponse($arr);
                         $this->sendError('Error',"Your wallet is insufficient ");
                    }
                }
            }else if($type=="explanation"){
                if($explanations){
                    $explanations=$explanations-1;
                    $userwalletdata->user_explanations=$explanations;
                    $userwalletdata->id=$user_wallet->id;
                    $DB->update_record("userwallet",$userwalletdata);
                    self::insentive_xp($type,$quiz_id,$tournament_id);
                    $this->sendResponse(array("status"=>1, "balance"=>$coins));
                    //$res=true;
                }else{
                    if($coins>=$coinsToDeduct){
                        $old_coins=$coins;
                        $coins=$coins-$coinsToDeduct;
                        $userwalletdata->ballance=$coins;
                        $userwalletdata->id=$user_wallet->id;
                        $DB->update_record('userwallet',$userwalletdata);
                        $user_history=new stdClass();
                        $user_history->oldbalance=$old_coins;
                        $user_history->newbalance=$coins;
                        $user_history->type=$type;
                        $user_history->createddate=time();
                        $user_history->userid=$user_wallet->userid;
                        $msgdata=array("question_id"=>$question_id,"quiz_id"=>$quiz_id);
                        $user_history->message=json_encode($msgdata);
                        $DB->insert_record("userwallethistory",$user_history);
                        self::insentive_xp($type,$quiz_id,$tournament_id);
                        $this->sendResponse(array("status"=>1, "balance"=>$coins));
                    }else{
                        $this->sendError('Error',"Your wallet is insufficient ");
                    }
                }
            }else if($type=="translation"){
                if($coins>=$coinsToDeduct){
                    $old_coins=$coins;
                    $coins=$coins-$coinsToDeduct;
                    //$explanations=$explanations-1;
                    $userwalletdata->ballance=$coins;
                    $userwalletdata->id=$user_wallet->id;
                    $DB->update_record('userwallet',$userwalletdata);
                    $user_history=new stdClass();
                    $user_history->oldbalance=$old_coins;
                    $user_history->newbalance=$coins;
                    $user_history->type=$type;
                    $user_history->createddate=time();
                    $user_history->userid=$user_wallet->userid;
                    $msgdata=array("question_id"=>$question_id,"quiz_id"=>$quiz_id);
                    $user_history->message=json_encode($msgdata);
                    $DB->insert_record("userwallethistory",$user_history);
                    $this->sendResponse(array("status"=>1, "balance"=>$coins));
                }else{
                    $this->sendError('Error',"Your wallet is insufficient ");
                }
              }else{
                   $this->sendError('Error',"Something Wrong");
              } 
         }else {
              $this->sendError('Error',"Something Wrong");
         }
    }
    private function insentive_xp($type,$quiz_id,$tournament_id=0){//insentive_xp($type,$quiz_id)
        global $DB,$USER,$PARENTUSER;
        $currentChild=$PARENTUSER->currentChild;
        $gradeid=$currentChild->grade;
        // $this->sendResponse("Ok");
        $quiz_id=$args['quiz_id'];
        $sql="SELECT c.id as cid,cc.id as ccid,cm.id as cmid FROM {course} c JOIN {course_categories} cc ON cc.id=c.category JOIN {course_modules} cm ON cm.course=c.id JOIN {quiz} q ON q.course=c.id WHERE q.id=?";
        $qdata=$DB->get_record_sql($sql,array($quiz_id));

          $sql2="SELECT * FROM {xpsetting} WHERE gradeid IN(?,0) ORDER BY id DESC LIMIT 1";
        $data=$DB->get_records_sql($sql2,array($gradeid));
        $arr=array();
        $this->sendResponse($qdata);
        $std=new stdClass();
        $std->userid=$USER->id;
        $std->tournament_id=$tournament_id;
        $std->xp_type=$type;
        $std->gradegradeid=0;
        $std->categoryid=$qdata->ccid;
        $std->courseid=$qdata->cid;
        $std->moduleid=$qdata->cmid;
        $std->grade=0;
        $std->maxgrade=0;
        $std->gotgrademax=0;
        $std->gradedate=time();
        $std->createddate=time();
        $std->modifieddate=time();
        if($type=="hint"){
             foreach($data as $dialog){
                if($dialog->insentive_hints>0){
                    $std->gotgrade=$dialog->insentive_hints;
                    $DB->insert_record("xphistory",$std);
                }
            }
        }
         if($type=="explanation"){
             foreach($data as $dialog){
                if($dialog->insentive_explanation>0){
                    $std->gotgrade=$dialog->insentive_explanation;
                    $DB->insert_record("xphistory",$std);
                }
            }
        }
    }
    private function get_current_semester(){
        global $DB;
        // $date=strtotime(date('15-10-2023'));
        $date=time();
        $current_time=$date;//1664649000;//time();
        $current_semester=$DB->get_record_sql("SELECT * FROM {currentsemestersetting} LIMIT 1");
        $start_date=$current_semester->start_date;
        $end_date=$current_semester->end_date;
        if($start_date>$current_time && $end_date<$current_time){
            return $current_semester;
        }else {
            $start_date=strtotime(date('d F ',$start_date).date('Y',$date));
            $end_date=strtotime(date('d F ',$end_date).date('Y',$date));
            if($start_date<$current_time && $end_date < $current_time ){
               $start_date=strtotime('+1 year',$start_date);
               $end_date=strtotime('+1 year',$end_date);
            }
            if($start_date>$current_time){
               $start_date=strtotime('-1 year',$start_date);
            }
            if($end_date<$current_time){                
                $end_date1=$end_date;
                $end_date=strtotime('+1 year',$start_date);
                $start_date=$end_date1;
            }
            $current_semester->start_date=$start_date;
            $current_semester->end_date=$end_date;
            return $current_semester;
        }
    }

    //xphistory api
    public function requestLeaderboard($args){
        global $DB,$USER, $PARENTUSER;
        $type=$args['timeFrame'];
        $location=$args['boundary'];
        $alllocation = array("national", "region", "province");
        $tournament_id=($args['tournament_id'])?$args['tournament_id']:0;
        //$gradeid=$args['gradeid'];
        $user_id=$USER->id;
        $myrecord = null;
        $user_child_data_sql="SELECT * FROM {childusers} WHERE userid='".$user_id."'";
        $user_child_data=$DB->get_record_sql($user_child_data_sql);
        $region=$user_child_data->region;
        $gradeid=($args['gradeid'])?$args['gradeid']:$user_child_data->grade;
        //$this->sendResponse($user_child_data)
        $provinces=$user_child_data->provinces;
        $nation=$USER->country;
        $arr=array();
        $query="";
        if($location=="national"){
            // $column_name="u.country=";
           // $query=$nation;
                $column_name="1=1";
                //$query="";
        }
        if($location=="region"){
           // $column_name="1=";
           // $query="1";
            $column_name="cu.region='".$region."'";
            $query=$region;
           // $query="Tangier-Tetouan, Al Hoceima";

        }
        if($location=="province"){
           /* $column_name="1=";
            $query="1";*/
              $column_name="cu.provinces='".$provinces."'";
            //$query=$provinces;
        }
        if(!empty($type) && !empty($location) && in_array($location, $alllocation)){
            if($type=="current_month"){
                $start_date=strtotime(date("01-F-Y",time()));
                $end_date=strtotime("+1 month", intval($start_date));
                $sql="SELECT 
                    cu.userid, 
                    IF(sum(xh.gotgrade) > 0, sum(xh.gotgrade),0) as final,
                    u.alternatename as firstname,
                    u.lastname,
                    u.alternatename as charname,
                    cu.image,
                    cu.portraitimage,
                    cu.region
                    FROM {childusers} cu
                    inner join {user} u on u.id=cu.userid
                    LEFT join {xphistory} xh on cu.userid=xh.userid AND u.id= cu.userid and xh.categoryid = cu.grade AND xh.tournament_id='".$tournament_id."' AND (xh.createddate between '".$start_date."' AND '".$end_date."')
                    WHERE ($column_name) AND cu.grade = $gradeid
                    group by cu.userid
                    order by sum(xh.gotgrade) desc, cu.id";
                $this->query = $sql;
            } else if($type=="current_semester"){
                $get_current_semester=self::get_current_semester();
                $start_date=$get_current_semester->start_date;
                $end_date=$get_current_semester->end_date;

                $sql="SELECT 
                    cu.userid, 
                    IF(sum(xh.gotgrade) > 0, sum(xh.gotgrade),0) as final,
                    u.alternatename as firstname,
                    u.lastname,
                    u.alternatename as charname,
                    cu.image,
                    cu.portraitimage,
                    cu.region
                    FROM {childusers} cu
                    inner join {user} u on u.id=cu.userid
                    LEFT join {xphistory} xh on cu.userid=xh.userid AND u.id= cu.userid and xh.categoryid = cu.grade AND xh.tournament_id='".$tournament_id."' AND (xh.createddate between '".$start_date."' AND '".$end_date."')
                    WHERE ($column_name) AND cu.grade = $gradeid
                    group by cu.userid
                    order by sum(xh.gotgrade) desc, cu.id";
            }else if($type=="all"){
                $sql="SELECT 
                    cu.userid, 
                    IF(sum(xh.gotgrade) > 0, sum(xh.gotgrade),0) as final,
                    u.alternatename as firstname,
                    u.lastname,
                    u.alternatename as charname,
                    cu.image,
                    cu.portraitimage,
                    cu.region
                    FROM {childusers} cu
                    inner join {user} u on u.id=cu.userid
                    LEFT join {xphistory} xh on cu.userid=xh.userid AND u.id= cu.userid and xh.categoryid = cu.grade AND xh.tournament_id='".$tournament_id."' 
                    WHERE ($column_name) AND cu.grade = $gradeid
                    group by cu.userid
                    order by sum(xh.gotgrade) desc, cu.id";
            }
            if(!empty($sql)){
                $rank = 0;
                 $d=0;
                $data=$DB->get_records_sql($sql, array());
                foreach($data as $list){
                    //unset($list->portraitimage);
                    $rank ++;
                    $list->rank = $rank;
                    if($rank <=200){
                        $list->portraitImage = self::getmyportraitimages($list);
                        array_push($arr,$list);
                    }
                    if($list->userid == $USER->id){
                        $list->portraitImage = self::getmyportraitimages($list);
                        $myrecord = $list;
                    }
                    if($rank >=200 && !empty($myrecord)){
                        break;
                    }
                }//mygroup
                $allgroups = $DB->get_records_sql("select ig.id, ig.name FROM {institution_group} ig INNER JOIN {institution_group_member} igm on (igm.groupid = ig.id and igm.userid = ? and igm.schoolyear=?) or ( ig.createdby = ? and igm.schoolyear=?) group by ig.id order by ig.name", array($USER->id, $this->currentschoolyear, $USER->id, $this->currentschoolyear));
                $allgroup = array();
                foreach ($allgroups as $key => $group) {
                    $group->groupName = $group->name;
                    unset($group->groupname);
                    array_push($allgroup, $group);
                }
                if(empty($myrecord)){
                    $myrecord = new stdClass();
                    $myrecord->userid = $USER->id;
                    $myrecord->final = 0;
                    $myrecord->firstname = $USER->firstname;
                    $myrecord->lastname = $USER->lastname;
                    $myrecord->charname = $USER->alternatename;
                    $myrecord->image = $PARENTUSER->currentChild->charImage;;
                    $myrecord->region = $PARENTUSER->currentChild->region;
                    $myrecord->provinces = $PARENTUSER->currentChild->provinces;
                    $myrecord->rank = $rank+1;
                    $myrecord->portraitimage = null;
                }
                $this->sendResponse(array("allRecord"=>$arr, "myRecord"=>$myrecord, "myGroup"=>$allgroup));
            } else {
                $this->sendError("Error","Invalid ggRequest");
            }
        }else{
            $this->sendError("Error","Invalid rewwrwewrwerRequest");
        }
    }
    //getcointransection api
    public function getCoinTransactions(){
        global $DB,$USER;
        $arrincome=array();
        $outcome=array();
        $sql="SELECT uwh.id,u.alternatename AS name,u.firstname,uwh.createddate,uwh.amount,uwh.type FROM {userwallethistory} uwh JOIN {user} u On u.id=uwh.fromuser WHERE uwh.type='transferfrom' AND uwh.userid=?";
        $allincomedata=$DB->get_records_sql($sql,array($USER->id));
        if(sizeof($allincomedata)>0){
            foreach($allincomedata as $incomelist){
                $incomelist->type="income";
                 array_push($arrincome,$incomelist);
            }
           
        }
         $sql2="SELECT  uwh.id,u.alternatename AS name,u.firstname,uwh.createddate,uwh.amount,uwh.type FROM {userwallethistory} uwh JOIN {user} u On u.id=uwh.fromuser WHERE uwh.type='transferto' AND uwh.userid=?";
        $alloutcomedata=$DB->get_records_sql($sql2,array($USER->id));
        if(sizeof($alloutcomedata)>0){
            foreach($alloutcomedata as $outcomelist){
                $outcomelist->type="outcome";
                 array_push($outcome,$outcomelist);
            }
           
        }
        $this->sendResponse(array("income"=>$arrincome,"outcome"=>$outcome));

    }
    //get potraits active records api based userpotrait data
    public function getPortraits($args){
        global $DB, $USER;
        $monthstartdate = strtotime(date("01 F Y"));
        $starttime = strtotime(date("d F Y"));
        $monthenddate = strtotime("+1 month", $startdate);
        $potraitListData=array();
        $userchilddata=$DB->get_record("childusers",array("userid"=>$USER->id));
        $gender=strtolower($userchilddata->gender);
        // $groups = $DB->get_records_sql("select id, name, gender, start_time as startDate, end_time as endDate,background_color,mainportraitname AS portraitname from {potraitgroup} where start_time < ? and end_time > ? and (gender=? or gender=?)", array(time(), time(), $gender, "0"));
        $groups1 = $DB->get_records_sql("select id, name, gender, start_time as startDate, end_time as endDate,background_color,mainportraitname AS portraitname from {potraitgroup} where (gender=? or gender=?) and start_time <= ? and end_time >= ? order by start_time asc, status desc, name", array($gender, "0", $starttime, $starttime));
        $ggg = array_keys($groups1);
        if(empty($ggg)){
            $ggg = array(0);
        }
        $groups2 = $DB->get_records_sql("select id, name, gender, start_time as startDate, end_time as endDate,background_color,mainportraitname AS portraitname from {potraitgroup} where (gender=? or gender=?) and id not in (?) order by  start_time asc, status desc, name", array($gender, "0", implode(", ", $ggg)));
        // $groups2 = array();
        $groups = $groups1 + $groups2;
        foreach ($groups as $key => $group) {

                $group->backgroundimage=self::get_deginersfiles("potraitgroup", "backgroundimage", $group->id);
                $group->backgroundimagesmall=self::get_deginersfiles("potraitgroup", "backgroundimagesmall", $group->id);
                $group->backgroundimagemedium=self::get_deginersfiles("potraitgroup", "backgroundimagemedium", $group->id);
                $group->backgroundimagelarge=self::get_deginersfiles("potraitgroup", "backgroundimagelarge", $group->id);
                if(strpos($group->backgroundimagesmall, "local/designer/images/default-background.jpg") !== true){$group->backgroundimagesmall = $group->backgroundimage;}
                if(strpos($group->backgroundimagemedium, "local/designer/images/default-background.jpg") !== true){$group->backgroundimagemedium = $group->backgroundimage;}
                if(strpos($group->backgroundimagelarge, "local/designer/images/default-background.jpg") !== true){$group->backgroundimagelarge = $group->backgroundimage;}
            $allportraits = array();
           /* $portraits = $DB->get_records_sql("select p.id, p.name, p.ratio, up.id as portraitid, up.completed, up.modifieddate from {potrait} p left join {userpotrait} up on up.potraitid = p.id and up.userid=? where p.group_id = ? ORDER BY up.id ASC", array($USER->id, $group->id));*/

            $portraits = $DB->get_records_sql("select p.id, p.name, p.ratio, up.id as portraitid, up.completed, up.modifieddate from {potrait} p left join {userpotrait} up on up.potraitid = p.id and up.userid=? where p.group_id = ? ORDER BY p.id ASC", array($USER->id, $group->id));
            $group->requireCompleted=count($portraits);
            $completed_potrait=0;
            foreach ($portraits as $key => $portrait) {
                if($portrait->ratio == $portrait->completed){
                    $completed_potrait=$completed_potrait+1;
                }
                /*$group->no_of_potrait=count($portraits)*/
                $portrait->requireCompleted = $portrait->ratio;
                $portrait->portraitId = empty($portrait->portraitid)?0:$portrait->portraitid;
                $portrait->completed = empty($portrait->completed)?0:$portrait->completed;
                $portrait->isCompleted = ($portrait->requireCompleted == $portrait->completed);
                $portrait->completedPercent = ($portrait->completed/$portrait->requireCompleted);
                $portrait->completedDate = ($portrait->completed?$portrait->modifieddate:0);
                $portrait->backgroundimage=self::get_deginersfiles("potrait", "backgroundimage", $portrait->id);
                $portrait->backgroundimagesmall=self::get_deginersfiles("potrait", "backgroundimagesmall", $portrait->id);
                $portrait->backgroundimagemedium=self::get_deginersfiles("potrait", "backgroundimagemedium", $portrait->id);
                $portrait->backgroundimagelarge=self::get_deginersfiles("potrait", "backgroundimagelarge", $portrait->id);
                if(strpos($portrait->backgroundimagesmall, "local/designer/images/default-background.jpg") !== true){$portrait->backgroundimagesmall = $portrait->backgroundimage;}
                if(strpos($portrait->backgroundimagemedium, "local/designer/images/default-background.jpg") !== true){$portrait->backgroundimagemedium = $portrait->backgroundimage;}
                if(strpos($portrait->backgroundimagelarge, "local/designer/images/default-background.jpg") !== true){$portrait->backgroundimagelarge = $portrait->backgroundimage;}
                unset($portrait->portraitid);
                unset($portrait->ratio);
                unset($portrait->modifieddate);
                array_push($allportraits, $portrait);
            }
            
                $group->completedPercent=$completed_potrait!=0 && $group->requireCompleted!==0 ? floatval(($completed_potrait*100)/$group->requireCompleted):0;
                $group->isCompleted=($completed_potrait==$group->requireCompleted && $group->requireCompleted!==0);
                $group->completedDate= $group->isCompleted ? $portrait->completedDate:0;

            $group->completed=$completed_potrait;
            
            $group->portraits = $allportraits;
            array_push($potraitListData, $group);
        }
        $this->sendResponse(array("portraitGroups"=>$potraitListData));
    }
    //user complete potrait list api
    public function getUserPotrait(){
        global $DB,$USER;

        //self::get_deginersfiles("potrait", "backgroundimage", $portraitid)
        //self::get_deginersfiles("potrait", "backgroundimage", $portraitid);
        //self::get_deginersfiles("potrait", "backgroundimagelarge", $portraitid);
        //self::get_deginersfiles("potrait", "backgroundimagemedium", $portraitid);
        //self::get_deginersfiles("potrait", "backgroundimagesmall", $portraitid);
        $arr=array();
        $sql="SELECT p.id,p.name,pg.name AS potraitgroupname,p.ratio,up.completed,pg.gender,pg.background_color FROM {potrait} p JOIN {userpotrait} up ON p.id=up.potraitid AND up.ratio=up.completed JOIN {potraitgroup} pg ON pg.id=p.group_id WHERE userid=?";
        $data=$DB->get_records_sql($sql,array($USER->id));
        foreach($data as $list){
           // $list->backgroundimage->backgroundimagesmall=self::get_deginersfiles("potrait", "backgroundimagesmall", $list->id);
            $list->backgroundimage=self::get_deginersfiles("potrait", "backgroundimage", $list->id);
            $list->backgroundimagesmall=self::get_deginersfiles("potrait", "backgroundimagesmall", $list->id);
            $list->backgroundimagemedium=self::get_deginersfiles("potrait", "backgroundimagemedium", $list->id);
            $list->backgroundimagelarge=self::get_deginersfiles("potrait", "backgroundimagelarge", $list->id);
            array_push($arr,$list);
        }
        $this->sendResponse($arr);
    }
    public function test(){
        global $USER,$PARENTUSER;
        $this->sendResponse($PARENTUSER);
    }
    public function updateCharImageApi($args){
        global $DB,$USER,$PARENTUSER;
        $url=$args['url'];
        $type=$args['type'];
        if(!empty($url)){
            $fieldtype = "character";
            switch ($type) {
                case 'portrait':
                    $fieldtype = "portraitimage";
                    break;
                case 'character':
                    $fieldtype = "image";
                    break;
                default:
                    $fieldtype = "image";
                    break;
            }
            $getdata=$DB->get_record_sql("SELECT * FROM {childusers} WHERE userid=?",array($USER->id));
            $id=$getdata->id;
            $userid=$getdata->userid;
            $obj=new stdClass();
            $obj->id=$id;
            $obj->$fieldtype=$url;
            $obj->modifieddate=time();
            $DB->update_record("childusers",$obj);
            $msg['msg']="Character Image Updated successfully";
            $msg['status']="success";
            $this->sendResponse($msg);
        }
        else{
            
            $msg['msg']="url is empty";
            $msg['status']="Error";
            $this->sendError("Error",$msg);
           // $this->sendError("Invalid","Not ")
        }
    }
    public function createGroupReference($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $groupname = $args["groupName"];
        if(!empty($groupname)){
            if($DB->record_exists("childgroup", array("groupname"=>$groupname, "createdby"=>$USER->id))){
                $this->sendError("error", "Group with \"".$groupname."\" is already exists, Please try some other group name.");
            } else {
                $uniquekey = self::getnerateuniquekey();
                $insert = new stdClass();
                $insert->userid = $USER->id;
                $insert->uniquekey = $uniquekey;
                $insert->keytype = "newgroup";
                $insert->expiry = (365*24*3600);
                $insert->active = 1;
                $insert->createddate = time();
                if($DB->insert_record("useronetimekey", $insert)){
                    $group = new stdClass();
                    $group->groupname = $groupname;
                    $group->createdby = $USER->id;
                    $group->createddate  = time();
                    $group->refkey  = $uniquekey;
                    if($DB->insert_record("childgroup", $group)){
                        $this->sendResponse(array("refno"=>$uniquekey, "groupName"=>$groupname));
                    } else {
                        $this->sendError("error", "Failed to new group");
                    }
                } else {
                    $this->sendError("error", "Failed to create key");
                }
            }
        } else {
            $this->sendError("error", "Group name is required");
        }
    }
    public function addMeToGroupReference($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $refno = $args["refno"];
        if(!empty($refno)){
            if(!$DB->record_exists("childgroup", array("refkey"=>$refno))){
                $this->sendError("error", "Invalid Request");
            } else {
                $group = $DB->get_record("childgroup", array("refkey"=>$refno));
                if(!$DB->record_exists("childgroupmember", array("groupid"=>$group->id, "userid"=>$USER->id))){
                    $groupmember = new stdClass();
                    $groupmember->groupid = $group->id;
                    $groupmember->userid = $USER->id;
                    $groupmember->createdby = $USER->id;
                    $groupmember->createddate  = time();
                    if($DB->insert_record("childgroupmember", $groupmember)){
                        $this->sendResponse("Success");
                    } else {
                        $this->sendError("error", "failed to add you in group");
                    }
                } else {
                    $this->sendError("error", "You are already member of this group");
                }
            }
        } else {
            $this->sendError("error", "Group reference is required");
        }
    }
    public function getGroupList($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $allgroups = $DB->get_records_sql("select cg.id, cg.groupname FROM mdl_childgroup cg INNER JOIN mdl_childgroupmember cgm on (cgm.id = cg.id and cgm.userid = ?) or ( cg.createdby = ?) group by cg.id", array($USER->id, $USER->id));
        $allgroup = array();
        foreach ($allgroups as $key => $group) {
            $group->groupName = $group->groupname;
            unset($group->groupname);
            array_push($allgroup, $group);
        }
        $this->sendResponse(array(
            "status"=>(sizeof($allgroup))?1:0,
            "groups"=>$allgroup
        ));
    }
    //request group leaderboard user list
    public function getGroupLeaderBoardList($args){
        global $DB,$USER;
        $group_id=$args['groupid'];
        $rank = 0;
        $tournament_id = 0;
        $myrecord=null;
        $arr=array();
        $sql="SELECT cg.*,cgm.userid FROM {childgroup} cg JOIN {childgroupmember} cgm ON cg.id=cgm.groupid WHERE cg.id=? AND cgm.userid=?";
        //$data=$DB->get_records_sql($sql,array($group_id,$USER->id));
        $data=$DB->get_records_sql($sql,array($group_id,$USER->id));
        if($data){
            //$gmsql="SELECT cg.*,cgm.userid  FROM {childgroup} cg JOIN {childgroupmember} cgm ON cg.id=cgm.groupid WHERE cg.id=?";
            
            $gmsql="SELECT 
                    xh.userid, 
                    sum(xh.gotgrade) as final,
                    u.firstname,
                    u.lastname,
                    u.alternatename as charname,
                    u.picture as image,
                    cu.region
                    FROM {xphistory} xh
                    inner join {user} u on u.id=xh.userid
                    inner join {childusers} cu on cu.userid=xh.userid AND u.id= cu.userid JOIN {childgroupmember} cgm ON cgm.userid=cu.userid AND cgm.userid=u.id AND cgm.userid= xh.userid JOIN {childgroup} cg ON cg.id=cgm.groupid WHERE cg.id=? and xh.tournament_id=?
                    group by xh.userid
                    order by sum(xh.gotgrade) desc";
                    $gmdata=$DB->get_records_sql($gmsql,array($group_id, $tournament_id));
                    foreach($gmdata as $list){
                    $rank ++;
                    $list->rank = $rank;
                    if($list->userid ==$USER->id){
                        $myrecord = $list;
                    }
                    array_push($arr,$list);
                }
            $this->sendResponse(array("allRecord"=>$arr,"myRecord"=>$myrecord));
        }else{
            $this->sendError("Error","Sorry! You not belogs to this group");
        }
    }
    //generate api for account migratin key
    public function accountMigrationLink($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $args = (object) $args;
        $user = $DB->get_record("user", array("id"=>$USER->id));
        if(empty($args->password)){
            $this->sendError("error", "Invalid request");
        } else if(validate_internal_user_password($user, $args->password)) {
            $uniquekey = self::getnerateuniquekey();
            $insert = new stdClass();
            $insert->userid = $USER->id;
            $insert->uniquekey = $uniquekey;
            $insert->keytype="migrationCode";
            $insert->coins=0;
            $insert->active = 1;
            $insert->createddate = time();
            $DB->set_field("useronetimekey", "active", 0, array("keytype"=>"migrationCode", "userid" => $USER->id));
            if($DB->insert_record("useronetimekey", $insert)){
                $this->sendResponse(array("migrationCode"=>$uniquekey));
            } else{
                $this->sendError("error", "Failed to create key");
            } 
        } else {
            $this->sendError("error", "Invalid Password");
        }
    }
    public function restoreAccount($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $devicetoken=$args['devicetoken'];
        $migrationKey=$args['migrationCode'];
        $devicename=$args['devicename'];
        $sql="SELECT * FROM {useronetimekey} WHERE uniquekey=? AND active=? AND keytype=?";
        if($DB->record_exists_sql($sql, array($migrationKey,1, 'migrationCode'))){
            $userdata=$DB->get_record_sql($sql,array($migrationKey,1, 'migrationCode'));

            if($token = self::get_usertoken($userdata->userid)){
                self::validatetoken($token);
                self::successlogindevices($userdata->userid, $token, $devicetoken, $devicename, 1);
                $responsedata = new stdClass();
                $responsedata->status = 1;
                $responsedata->message = "Adeventure migrated";
                $responsedata->token = $token;
                $responsedata->id = $userdata->userid;
                $responsedata->userDetails = $PARENTUSER;
                $userdata->active = 0;
                $userdata->useddate = time();
                $DB->update_record("useronetimekey", $userdata);
                $this->sendResponse($responsedata);
            }else{
                $this->sendError("Failed to migrate Adventure", "Failed to migrate Adventure");
            }
        } else {
            $this->sendError("Error","Your Migration Key is not valid");
        }
    }
    public function changeloginusertofirstchild($migrateduserid)
    {
        global $DB, $USER, $CFG, $PARENTUSER;
        if($migratedchild = $DB->get_record_sql("SELECT * FROM {childusers} WHERE userid = ?", array($migrateduserid))){
            if($olddevice = $DB->get_record_sql("select ul.* from mdl_userlogindevices ul INNER JOIN mdl_childusers cu on cu.userid = ul.userid where cu.parentid = ? && cu.userid != ?  order by ul.id desc limit 0, 1", array($migratedchild->parentid, $migrateduserid))){
                $olddevice->loginstatus = 0;
                $olddevice->modifieddate = time();
                $DB->update_record("userlogindevices", $olddevice);
                $DB->set_field("userlogindevices", "loginstatus", 0, array("userid"=>$migrateduserid));
                unset($olddevice->id);
                unset($olddevice->modifieddate);
                $olddevice->createddate = time();
                $olddevice->loginstatus = 1;
                $DB->insert_record("userlogindevices", $olddevice);
            }
        }
    }
    public function migrateAccount($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $devicetoken=$args['devicetoken'];
        $migrationKey=$args['migrationCode'];
        $devicename=$args['devicename'];
        $sql="SELECT * FROM {useronetimekey} WHERE uniquekey=? AND active=? AND keytype=?";
        if($DB->record_exists_sql($sql, array($migrationKey,1, 'migrationCode'))){
            $userdata=$DB->get_record_sql($sql,array($migrationKey,1, 'migrationCode'));
            if($token = self::get_usertoken($userdata->userid)){
                $transaction = $DB->start_delegated_transaction();
                self::changeloginusertofirstchild($userdata->userid);
                $DB->set_field("childusers", "parentid", $PARENTUSER->id, array("userid"=>$userdata->userid));
                self::validatetoken($token);
                self::successlogindevices($userdata->userid, $token, $devicetoken, $devicename, 1);
                $responsedata = new stdClass();
                $responsedata->status = 1;
                $responsedata->message = "Adeventure migrated";
                $responsedata->token = $token;
                $responsedata->id = $userdata->userid;
                $responsedata->userDetails = $PARENTUSER;
                $userdata->active = 0;
                $userdata->useddate = time();
                $DB->update_record("useronetimekey", $userdata);
                $transaction->allow_commit();
                $this->sendResponse($responsedata);
            }else{
                $this->sendError("Failed to migrate Adventure", "Failed to migrate Adventure");
            }
        } else {
            $this->sendError("Error","Your Migration Key is not valid");
        }
    }
    public function updatesLog($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $action=$args['action'];
        if(empty($action)){
            $this->sendError("Error","action is required");
        } else {
            $this->generatelog($action, $USER->id);
            $this->sendResponse(array("status"=>1));
        }
    }
    public function generatelog($action, $userid, $grade=0){
        global $DB, $USER, $CFG, $PARENTUSER;
        $log = new stdClass();
        $log->userid = $userid;
        $log->grade = $grade;
        $log->action = $action;
        $log->timecreated = time();
        $DB->insert_record("manualapplog", $log);
    }
    public function checknewdevice($devicetoken, $userid, $grade){
        global $DB, $USER, $CFG, $PARENTUSER;
        if(!$DB->record_exists("userlogindevices", array("token"=>$devicetoken))){
            $this->generatelog("newdevice", $userid, $grade);
        }
    }
    public function getWinnerAddress(){
        global $DB,$USER;
        $arr=array();
        $sql="SELECT * FROM {tourament_winner_address}";
        $data=$DB->get_records_sql($sql);
        if($data){
            foreach($data as $val){
                array_push($arr,$val);
            }
            $this->sendResponse(array("address"=>$arr));
        }else{
           $this->sendError("Error","No Address Found");
        }
    }
    public function getTournamentDetails(){
        global $DB,$USER,$PARENTUSER;
        $currentChild = $PARENTUSER->currentChild;
        $gradeid = $currentChild->grade;
        $rulesdata=self::getTournamentRules();
        $this->sendResponse(array(
            "prevTouranment"=>self::get_tournament(-1),
            "currentTouranment"=>self::get_tournament(0),
            "nextTouranment"=>self::get_tournament(1),
            "rules"=>$rulesdata
        ));
    }
    private function get_tournament($status){
        global $DB,$USER,$PARENTUSER;
        $gradeid = $PARENTUSER->currentChild->grade;
        $currenttime = time();
        $WHEREPARAM = array();
        switch ($status) {
            case '-1':
                $WHERE = "where (FIND_IN_SET(?, gradeid) or gradeid=0) and enddate < ? and status=1 order by enddate desc limit 0,1";
                $WHEREPARAM = array($gradeid, $currenttime);
                break;
            case '0':
                $WHERE = "where (FIND_IN_SET(?, gradeid) or gradeid=0) and startdate < ? and enddate > ? and status=1 order by enddate asc limit 0,1";
                $WHEREPARAM = array($gradeid, $currenttime, $currenttime);
                break;
            case '1':
                $WHERE = "where (FIND_IN_SET(?, gradeid) or gradeid=0) and startdate > ? and enddate > ? and status=1 order by enddate asc limit 0,1";
                $WHEREPARAM = array($gradeid, $currenttime, $currenttime);
                break;
        }
        $tournament = $DB->get_record_sql("select id, name, gradeid, startdate, enddate, status from {tournament_settings} ".$WHERE, $WHEREPARAM);
        if($tournament){
            $tournament->quizes = array();
            $tournament->finished = false;
            $tournament->isWinner = false;
            $tournament->promoted = false;
            if($tournament->enddate < time()){ $tournament->finished = true; }
            if($winner = $DB->get_record("tournament_winner", array("tournament_id"=>$tournament->id, "userid"=>$USER->id))){
                $tournament->isWinner = true;
                if(!$tournament->winner){
                    $tournament->promoted = true;
                }
            }
            if($status==1){
                $quizes = self::getquizesfromcat($gradeid);
                $tournament->quizes = $quizes;
            }
        }
        return $tournament;
    }
    private function getquizesfromcat($gradeid){
        global $DB,$USER,$PARENTUSER;
        $gradeid = $PARENTUSER->currentChild->grade;
        require_once($CFG->dirroot."/course/lib.php");
        $allquizes = array();
        $courses = $DB->get_records("course", array("category"=>$gradeid, "visible"=>1));
        foreach ($courses as $course) {
                                    
        }
        // $allmodules = get_array_of_activities($topic->course);
        // $quizdata = self::get_quiz($course, $topic, $module);
        //     if(!empty($quizdata)){
        //         $quizdata['quizno'] = "Quiz n<sup><small>o</small></sup>".(sizeof($allquizzes)+1);
        //         array_push($allquizzes, $quizdata);
        //     }
        return $allquizes;
    }
    

    private function getTournamentRules(){
        global $DB,$USER;
        $arr=array();
        $sql="SELECT * FROM {tournament_rules} WHERE status=?";
        $data=$DB->get_records_sql($sql,array(1));
        foreach($data as $val){
            array_push($arr,$val);
        }
        return $arr;
    }
    public function submitWinnerAddress($args){
        global $DB,$USER,$PARENTUSER;
        $gradeid=$PARENTUSER->currentChild->grade;
        $tournamentid=$args['tournamentid'];
        $firstname=$args['firstname'];
        $lastname=$args['lastname'];
        $phone=$args['phone'];
        $address=$args['address'];
        $city=$args['city'];
        $region=$args['region'];
        $province=$args['province'];
        $errors=array();
        if(empty($tournamentid)){ 
            array_push($errors, "Tournament is required");
        }
        if(empty($firstname)){
            array_push($errors, "Firstname is required");
        }
        if(empty($lastname)){
            array_push($errors, "Lastname is required");
        }
        if(empty($phone)){
            array_push($errors, "Phone is required");
        }
        if(empty($address)){
            array_push($errors, "Address No is required");
        }
        if(empty($city)){
            array_push($errors, "City is required");
        }
        if(empty($region)){
            $region = $PARENTUSER->currentChild->region;
        }
        if(empty($province)){
            $province = $PARENTUSER->currentChild->provinces;
        }

        if(sizeof($errors)>0){
            $this->sendError("Error", implode(", ", $errors));
        }else{
            $std=new stdClass();
            $std->gradeid=$gradeid;
            $std->tournamentid=$tournamentid;
            $std->userid=$USER->id;
            $std->firstname=$firstname;
            $std->lastname=$lastname;
            $std->phone=$phone;
            $std->address=$address;
            $std->city=$city;
            $std->region=$region;
            $std->province=$province;
            if($DB->insert_record("tourament_winner_address",$std)){
                 $this->sendResponse("Successfull adddred address");
            }else{
                 $this->sendError("Error", "Something wrong");
            }
        }
    }
    public function getTournamentLeaderboard($args){
        global $DB,$USER, $PARENTUSER;
        $user_id=$USER->id;
        $tournament_id=$args['tournament_id'];
        $myrecord = null;
        $arr=array();
        $tournamnet = $DB->get_record("tournament_settings", array("id"=>$tournament_id));
        if($tournamnet){
            $sql="SELECT 
                xh.userid, 
                sum(xh.gotgrade) as final,
                u.alternatename as firstname,
                u.lastname,
                u.alternatename as charname,
                cu.image,
                cu.portraitimage,
                cu.region,
                cu.provinces
                FROM {xphistory} xh
                inner join {user} u on u.id=xh.userid
                inner join {childusers} cu on cu.userid=xh.userid AND u.id= cu.userid
                WHERE xh.tournament_id=$tournament_id
                group by xh.userid
                order by sum(xh.gotgrade) desc";

                   $rank = 0;
            $d=0;
            $data=$DB->get_records_sql($sql, array());
            foreach($data as $list){
                $rank ++;
                $list->rank = $rank;
                if($rank <=200){
                    $list->portraitImage = self::getmyportraitimages($list);
                    array_push($arr,$list);
                }
                if($list->userid == $USER->id){
                    $list->portraitImage = self::getmyportraitimages($list);
                    $myrecord = $list;
                }
                if($rank >=200 && !empty($myrecord)){
                    break;
                }
            }
            $allgroup = array();
            if(empty($myrecord)){
                $myrecord = new stdClass();
                $myrecord->userid = $USER->id;
                $myrecord->final = 0;
                $myrecord->firstname = $USER->firstname;
                $myrecord->lastname = $USER->lastname;
                $myrecord->charname = $USER->alternatename;
                $myrecord->image = $PARENTUSER->currentChild->charImage;;
                $myrecord->region = $PARENTUSER->currentChild->region;
                $myrecord->provinces = $PARENTUSER->currentChild->provinces;
                $myrecord->rank = $rank+1;
                $myrecord->portraitimage = null;
            }
            if(sizeof($arr) == 0 && $myrecord->final==0){
                $this->sendError("Error","Tournament not started");
            } else {
                $this->sendResponse(array("allRecord"=>$arr, "myRecord"=>$myrecord));
            }
        }else{
            $this->sendError("Error","Invalid Tournament");
        }
    }
    public function addMeToGroup($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $groupid = $args["groupid"];
        if(!empty($groupid)){
            if(!$DB->record_exists_sql("select k.* from {useronetimekey} k inner join {institution_group} ig on ig.id=k.coins where k.active=1 and (k.keytype=? or k.keytype=? or k.keytype=?) and k.uniquekey=?", array("linkjoingroup", "directjoingroup", "linkjoinexam", $groupid))){
                $this->sendResponse("Code invalide. Veuillez vérifier le code et essayer de nouveau.");
                $this->msg = "FAILED";
            } else {
                $group = $DB->get_record_sql("select ig.*, k.id as onekeyid, k.keytype from {useronetimekey} k inner join {institution_group} ig on ig.id=k.coins where k.active=1 and (k.keytype=? or k.keytype=? or k.keytype=?) and k.uniquekey=?", array("linkjoingroup","directjoingroup","linkjoinexam",$groupid));
                self::addtoinstitutegrup($group, $USER->id);
                $ddd->id = $group->onekeyid;
                $ddd->active = 0;
                $ddd->useddate = time();
                $DB->update_record("useronetimekey", $ddd);
                if($group->keytype == "linkjoinexam"){
                    self::enableExamMode($group, $USER->id);
                }
                self::update_mygrade();
            }
        } else {
            $this->sendResponse("Code invalide. Veuillez vérifier le code et essayer de nouveau.");
            $this->msg = "FAILED";
        }
    }
    private function enableExamMode($group, $userid){
        global $DB;
        if($member = $DB->get_record("institution_group_member", array("institutionid"=>$group->institutionid,"groupid"=>$group->id, "userid"=>$userid, "schoolyear"=>$this->currentschoolyear))){
            $DB->set_field('institution_group_member', 'exammode', 1, array('id' => $member->id));
            $DB->set_field('institution_group_member', 'status', 1, array('id' => $member->id));
            $this->sendResponse("SUCCESS");
            $this->msg = "SUCCESS";
        } else {
            $this->sendResponse("Code invalide. Vous n'êtes pas autorisé à utiliser ce code.");
            $this->msg = "FAILED";
        }
    }
    private function addtoinstitutegrup($group, $userid){
        global $DB;
        if($group->id && !$DB->record_exists("institution_group_member", array("institutionid"=>$group->institutionid,"groupid"=>$group->id, "userid"=>$userid, "schoolyear"=>$this->currentschoolyear))){
            $groupmember = new stdClass();
            $groupmember->institutionid = $group->institutionid;
            $groupmember->groupid = $group->id;
            $groupmember->userid = $userid;
            $groupmember->schoolyear = $this->currentschoolyear;
            $groupmember->tutor = 0;
            $groupmember->status = 0;
            if($group->keytype == 'directjoingroup'){
                $groupmember->status = 1;
                $this->update_mygrade();
            }
            $groupmember->createddate  = time();
            if($DB->insert_record("institution_group_member", $groupmember)){
                $this->sendResponse("SUCCESS");
                $this->msg = "SUCCESS";
            } else {
                $this->sendResponse("Problème de connexion. Veuillez essayer de nouveau.");
                $this->msg = "FAILED";
            }
        } else {
            $member = $DB->get_record("institution_group_member", array("institutionid"=>$group->institutionid,"groupid"=>$group->id, "userid"=>$userid, "schoolyear"=>$this->currentschoolyear));
            switch ($member->status) {
                case 0:
                    $this->sendResponse("Votre demande d'adhésion n'a pas été encore acceptée par votre établissement. Veuillez patienter.");
                    $this->msg = "FAILED";
                    break;
                case 1:
                    $this->sendResponse("Tu es déjà inscrit dans ce groupe.");
                    $this->msg = "SUCCESS";
                    break;
                case 2:
                    $member->status=0;
                    if($group->keytype == 'directjoingroup'){
                        $member->status = 1;
                        $this->update_mygrade();
                    }
                    if($DB->update_record("institution_group_member", $member)){
                        $this->sendResponse("Ta demande a été envoyée à ton école. Tu recevras un message lorsqu'elle sera traitée et tu pourras accéder aux devoirs.");
                        $this->msg = "SUCCESS";
                    } else {
                        $this->sendResponse("Problème de connexion. Veuillez essayer de nouveau.");
                        $this->msg = "FAILED";
                    }                
                    // $this->sendError("error", "Your request is not rejected already");
                    break;
                case 3:
                    $member->status=0;
                    if($group->keytype == 'directjoingroup'){
                        $member->status = 1;
                        $this->update_mygrade();
                    }
                    if($DB->update_record("institution_group_member", $member)){
                        $this->sendResponse("Ta demande a été envoyée à ton école. Tu recevras un message lorsqu'elle sera traitée et tu pourras accéder aux devoirs.");
                        $this->msg = "SUCCESS";
                    } else {
                        $this->sendResponse("Problème de connexion. Veuillez essayer de nouveau.");
                        $this->msg = "FAILED";
                    }                
                    // $this->sendError("error", "Your request is not rejected already");
                    break;
                default:
                    $this->sendResponse("Tu es déjà inscrit dans ce groupe.");
                    $this->msg = "FAILED";
                    break;
            }
        }
        $allgroups = explode(",", $group->groupid);
        foreach ($allgroups as $key => $groupid) {
            if($groupid && !$DB->record_exists("groups_members", array("groupid"=>$groupid, "userid"=>$userid))){
                $groupmember = new stdClass();
                $groupmember->groupid = $groupid;
                $groupmember->userid = $userid;
                $groupmember->timeadded  = time();
                $groupmember->component = "";
                $groupmember->itemid = 0;
                $DB->insert_record("groups_members", $groupmember);
            }
        }
    }
    public function getMyGroupList($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $allgroups = $DB->get_records_sql("select ig.id, ig.name FROM {institution_group} ig INNER JOIN {institution_group_member} igm on (igm.groupid = ig.id and igm.userid = ? and igm.schoolyear=?) or ( ig.createdby = ? and igm.schoolyear=?) group by ig.id order by ig.name", array($USER->id, $this->currentschoolyear, $USER->id, $this->currentschoolyear));
        $allgroup = array();
        foreach ($allgroups as $key => $group) {
            $group->groupName = $group->name;
            unset($group->groupname);
            array_push($allgroup, $group);
        }
        $this->sendResponse(array(
            "status"=>(sizeof($allgroup))?1:0,
            "groups"=>$allgroup
        ));
    }
    public function getMyGroupLeaderBoard($args){
        global $DB,$USER;
        $group_id=$args['groupid'];
        $rank = 0;
        $tournament_id = 0;
        $myrecord=null;
        $arr=array();
        $sql="SELECT ig.*,igm.userid FROM {institution_group} ig JOIN {institution_group_member} igm ON igm.groupid = ig.id and igm.schoolyear=? WHERE ig.id=? AND igm.userid=?";
        //$data=$DB->get_records_sql($sql,array($group_id,$USER->id));
        $data=$DB->get_records_sql($sql,array($this->currentschoolyear, $group_id,$USER->id));
        if($data){
            //$gmsql="SELECT cg.*,cgm.userid  FROM {childgroup} cg JOIN {childgroupmember} cgm ON cg.id=cgm.groupid WHERE cg.id=?";
            
            $gmsql="SELECT 
                    xh.userid, 
                    sum(xh.gotgrade) as final,
                    u.alternatename as firstname,
                    u.lastname,
                    u.alternatename as charname,
                    cu.image,
                    cu.portraitimage,
                    cu.region,
                    cu.provinces
                    FROM {xphistory} xh
                    inner join {user} u on u.id=xh.userid
                    inner join {childusers} cu on cu.userid=xh.userid AND u.id= cu.userid and xh.categoryid = cu.grade JOIN {institution_group_member} igm ON igm.userid=cu.userid AND igm.userid=u.id AND igm.userid= xh.userid and igm.schoolyear=? JOIN {institution_group} ig ON ig.id=igm.groupid WHERE ig.id=? and xh.tournament_id=?
                    group by xh.userid
                    order by sum(xh.gotgrade) desc";
                    $gmdata=$DB->get_records_sql($gmsql,array($this->currentschoolyear, $group_id, $tournament_id));
                foreach($gmdata as $list){
                    $rank ++;
                    $list->rank = $rank;
                    if($list->userid ==$USER->id){
                        $myrecord = $list;
                    }
                    array_push($arr,$list);
                }
                if(empty($myrecord)){
                    $myrecord = new stdClass();
                    $myrecord->userid = $USER->id;
                    $myrecord->final = 0;
                    $myrecord->firstname = $USER->firstname;
                    $myrecord->lastname = $USER->lastname;
                    $myrecord->charname = $USER->alternatename;
                    $myrecord->image = $PARENTUSER->currentChild->charImage;;
                    $myrecord->region = $PARENTUSER->currentChild->region;
                    $myrecord->provinces = $PARENTUSER->currentChild->provinces;
                    $myrecord->rank = $rank+1;
                    $myrecord->portraitimage = null;
                }


            $this->sendResponse(array("allRecord"=>$arr,"myRecord"=>$myrecord));
        }else{
            $this->sendError("Error","Sorry! You not belogs to this group");
        }
    }
    public function getMyHomework($args){
        global $DB, $USER, $CFG, $PARENTUSER, $XPSETTING, $INSTITUTION;
        require_once($CFG->dirroot."/mod/quiz/lib.php");
        // $currentdate = strtotime(date("d F Y"));
       /* print_r($USER->id);
        echo "SELECT h.*, cs1.name as semester, cs2.name as lesson, cm.instance, q.name as quizname, cs1.type as gamemode from {homework} h INNER JOIN {course_sections} cs1 on cs1.id=h.topic INNER JOIN {course_sections} cs2 on cs2.id=h.subtopic INNER JOIN {course_modules} cm on cm.id= h.quiz and cm.visible=1 INNER JOIN {modules} m on m.id=cm.module and m.name='quiz' INNER JOIN {quiz} q on q.id=cm.instance INNER JOIN {institution_group} ig on ig.id = h.groupid INNER JOIN {institution_group_member} igm on (igm.groupid = ig.id and igm.userid = ? and igm.schoolyear=? and igm.status = 1) or ( ig.createdby = ? and igm.schoolyear=? and igm.status = 1) INNER JOIN {childusers} cu on (cu.userid = igm.userid or cu.userid = ig.createdby) and h.categoryid = cu.grade where h.status = ? and h.type=0 order by homeworkdate desc ";*/
        $currentdate = time();
        $returndata = array(
            "status"=>1, 
            "message"=>"success",
            "currentTime"=>time(),
            "homework"=>array("notCompleted"=>array(), "ongoing"=>array(), "completed"=>array(), "retry"=>array())
        );
        $alltask = $DB->get_records_sql("SELECT h.*, cs1.name as semester, cs2.name as lesson, cm.instance, q.name as quizname, cs1.type as gamemode from {homework} h INNER JOIN {course_sections} cs1 on cs1.id=h.topic INNER JOIN {course_sections} cs2 on cs2.id=h.subtopic INNER JOIN {course_modules} cm on cm.id= h.quiz and cm.visible=1 INNER JOIN {modules} m on m.id=cm.module and m.name='quiz' INNER JOIN {quiz} q on q.id=cm.instance INNER JOIN {institution_group} ig on ig.id = h.groupid INNER JOIN {institution_group_member} igm on (igm.groupid = ig.id and igm.userid = ? and h.schoolyear=? and igm.status = 1) or ( ig.createdby = ? and h.schoolyear=? and igm.status = 1) INNER JOIN {childusers} cu on (cu.userid = igm.userid or cu.userid = ig.createdby) and h.categoryid = cu.grade where h.status = ? and (h.type IN(0,2) OR ig.istraining=1) order by homeworkdate desc ", array($USER->id, $this->currentschoolyear, $USER->id, $this->currentschoolyear, 1));

        $gradeId = $PARENTUSER->currentChild->grade;
        $enableCourses = unserialize($this->INSTITUTION->enablecourses);
        if(isset($enableCourses)){
            $new_array = [];
            if (isset($enableCourses[$gradeId])) {
                $new_array = $enableCourses[$gradeId];
            }
        }

        foreach ($alltask as $key => $task) {
            if(!empty($new_array) && !in_array($task->courseid, $new_array)){continue;}else
            if(empty($task->gamemode)){continue;}
            $task->homeworkStudents = explode(",", $task->students);
            if($task->filterarea == 1 && !in_array($USER->id, $task->homeworkStudents)){continue;}
            if($task->gamemode == 'assessment'){$task->gamemode='grade'; }
            if($task->homeworkdate > $currentdate){ $task->isFuture = true; } else { $task->isFuture = false; }
            $task->currentTime = time();
            $task->dueDate = $task->duedate;
            $task->subtopicId = $task->subtopic;
            $task->topicId = $task->topic;
            $task->gameMode = $task->gamemode;
            $task->quizId = $task->quiz;
            $course  =$DB->get_record("course", array("id"=>$task->courseid));
            $cm = get_fast_modinfo($course, $USER->id)->get_cm($task->quiz);
            $task->locked = !$cm->uservisible;
            if($task->locked){continue;}
            $XPSETTING = $DB->get_record("xpsetting", array("gradeid"=>$task->categoryid));
            // $task->maxScore = number_format(self::get_categorygrade($task->categoryid), 2);
            $task->maxScore = intval(self::get_categorygrade($task->categoryid));
            $task->attemptcompleted = $DB->record_exists_sql("select * from {quizattempts} where moduleid = ? and schoolyear=? and userid=? and finishtime IS NOT NULL", array($task->quiz, $this->currentschoolyear, $USER->id));
            $task->completed = $DB->record_exists_sql("SELECT qa.sumgrades from {quiz_attempts} qa
                INNER JOIN {quizattempts} qas on qas.attemptid = qa.id AND qas.schoolyear = ?
                INNER JOIN {quiz} q on q.id = qa.quiz
                INNER JOIN {grade_items} gi on gi.itemtype='mod' and gi.itemmodule='quiz' and gi.iteminstance= q.id  
                where ((qa.sumgrades/q.sumgrades)*gi.grademax >= gi.gradepass) AND q.id=? and qa.userid=?", array($this->currentschoolyear, $task->instance, $USER->id));
            $quiz = $DB->get_record("quiz", array("id"=>$task->instance));
            $bestgrade = quiz_get_best_grade($quiz, $USER->id);
            $task->bestGrade=(empty($bestgrade)?"":number_format($bestgrade, 2));
            // print_r(array("select qa.* from {quiz_attempts} qa inner join {quizattempts} qa1 on qa1.attemptid = qa.id where qa.timefinish != 0 and qa.quiz = ? and qa.userid=? and qa1.schoolyear=?", array($quiz->id, $USER->id, $this->currentschoolyear)));
            $attemptedAfterExpiry = false;
            $firstattempt = $DB->get_record_sql("select qa.* from {quiz_attempts} qa inner join {quizattempts} qa1 on qa1.attemptid = qa.id where qa.timefinish != 0 and qa.quiz = ? and qa.userid=? and qa1.schoolyear=?", array($quiz->id, $USER->id, $this->currentschoolyear));
            $task->completedDate = 0;
            $task->attemptedAfterExpiry =false;
            if($firstattempt){ $task->attemptedAfterExpiry = ($firstattempt->timestart > $task->dueDate);}
            if(!empty($firstattempt->timefinish)){ $task->completedDate = $firstattempt->timefinish; $task->attemptedAfterExpiry = ($firstattempt->timefinish > $task->dueDate);}
            if(!$task->completed){ $task->attemptedAfterExpiry = (time() > $task->dueDate); }
            $task->started = $DB->record_exists_sql("select * from {quiz_attempts} qa inner join {quizattempts} qa1 on qa1.attemptid = qa.id where qa.quiz = ? and qa.userid=? and qa1.schoolyear=?", array($quiz->id, $USER->id, $this->currentschoolyear));
            if(!self::isInternalUser($course->id, $USER->id) && !$this->premiumAccount && $cm->uservisible && $quiz->accessibility){
                $task->locked = true;
            }
            $task->status = self::getMyStatus($task->locked, $task->completed, $task->started, $task->bestGrade);
            $tasktype = "homework";
            // var_dump($task->attemptcompleted);
            // die;
            $xp = 0;
            if($xp_userData=$DB->get_record_sql("SELECT * FROM {xphistory} WHERE userid=? AND moduleid=? order by gotgrade desc limit 0, 1",array($USER->id,$task->quiz))){
                $xp=$xp_userData->gotgrade;
                $maxxp=$xp_userData->gotgrademax;
            } else {
                $maxxp= intval($XPSETTING->roundon)*$XPSETTING->scoremultiplier;
            }
            $task->xp = $xp;
            $task->maxxp = $maxxp;
            if($task->completed){
                array_push($returndata[$tasktype]['completed'], $task);
            } else if($task->duedate < $currentdate){
                array_push($returndata[$tasktype]['notCompleted'], $task);
            } else if($DB->record_exists_sql("select * from {quizattempts} where moduleid = ? and schoolyear=? and userid=? and finishtime IS NOT NULL", array($task->quiz, $this->currentschoolyear, $USER->id))){
                array_push($returndata[$tasktype]['retry'], $task);
            } else {
                array_push($returndata[$tasktype]['ongoing'], $task);
            }

        }
        $this->sendResponse($returndata);
    }
    public function getMyExam($args){
        global $DB, $USER, $CFG, $PARENTUSER, $XPSETTING;
        require_once($CFG->dirroot."/mod/quiz/lib.php");
        $currentdate = time();
        $returndata = array(
            "status"=>1, 
            "message"=>"success",
            "currentTime"=>time(),
            "exam"=>array("notCompleted"=>array(), "ongoing"=>array(), "completed"=>array(), "retry"=>array())
        );
        $alltask = $DB->get_records_sql("SELECT h.*, cs1.name as semester, cs2.name as lesson, cm.instance, q.name as quizname, cs1.type as gamemode from {homework} h INNER JOIN {course_sections} cs1 on cs1.id=h.topic INNER JOIN {course_sections} cs2 on cs2.id=h.subtopic INNER JOIN {course_modules} cm on cm.id= h.quiz and cm.visible=1 INNER JOIN {modules} m on m.id=cm.module and m.name='quiz' INNER JOIN {quiz} q on q.id=cm.instance INNER JOIN {institution_group} ig on ig.id = h.groupid INNER JOIN {institution_group_member} igm on (igm.groupid = ig.id and igm.userid = ? and h.schoolyear=? and igm.status = 1) or ( ig.createdby = ? and h.schoolyear=? and igm.status = 1) INNER JOIN {childusers} cu on (cu.userid = igm.userid or cu.userid = ig.createdby) and h.categoryid = cu.grade where h.status = ? and h.type=1 AND ig.istraining=0 order by homeworkdate desc ", array($USER->id, $this->currentschoolyear, $USER->id, $this->currentschoolyear, 1));


        $gradeId = $PARENTUSER->currentChild->grade;
        $enableCourses = unserialize($this->INSTITUTION->enablecourses);
        if(isset($enableCourses) ){
            $new_array = [];
            if (isset($enableCourses[$gradeId])) {
                $new_array = $enableCourses[$gradeId];
            }
        }


        foreach ($alltask as $key => $task) {
            if(!empty($new_array) && !in_array($task->courseid, $new_array)){continue;}else
            if(empty($task->gamemode)){continue;}
            $task->homeworkStudents = explode(",", $task->students);
            if($task->filterarea == 1 && !in_array($USER->id, $task->homeworkStudents)){continue;}
            if($task->gamemode == 'assessment'){$task->gamemode='grade'; }
            if($task->homeworkdate > $currentdate){ $task->isFuture = true; } else { $task->isFuture = false; }
            $task->currentTime = time();
            $task->dueDate = $task->duedate;
            $task->subtopicId = $task->subtopic;
            $task->topicId = $task->topic;
            $task->gameMode = $task->gamemode;
            $task->quizId = $task->quiz;
            $course  =$DB->get_record("course", array("id"=>$task->courseid));
            $cm = get_fast_modinfo($course, $USER->id)->get_cm($task->quiz);
            $task->locked = !$cm->uservisible;
            if($task->locked){continue;}
            $XPSETTING = $DB->get_record("xpsetting", array("gradeid"=>$task->categoryid));
            // $task->maxScore = number_format(self::get_categorygrade($task->categoryid), 2);
            $task->maxScore = intval(self::get_categorygrade($task->categoryid));
            $task->attemptcompleted = $DB->record_exists_sql("select * from {quizattempts} where moduleid = ? and schoolyear=? and userid=? and finishtime IS NOT NULL", array($task->quiz, $this->currentschoolyear, $USER->id));
            $task->completed = $DB->record_exists_sql("select * from {course_modules_completion} where coursemoduleid = ? and userid=? and completionstate != ?", array($task->quiz, $USER->id,0));
            $quiz = $DB->get_record("quiz", array("id"=>$task->instance));
            $bestgrade = quiz_get_best_grade($quiz, $USER->id);
            $task->attemptedAfterExpiry =false;
            $task->bestGrade=(empty($bestgrade)?"":number_format($bestgrade, 2));
            $firstattempt = $DB->get_record_sql("select qa.* from {quiz_attempts} qa inner join {quizattempts} qa1 on qa1.attemptid = qa.id where qa.timefinish != 0 and qa.quiz = ? and qa.userid=? and qa1.schoolyear=?", array($quiz->id, $USER->id, $this->currentschoolyear));
            $task->completedDate = 0;
            if($firstattempt){ $task->attemptedAfterExpiry = ($firstattempt->timestart > $task->dueDate);}
            if(!empty($firstattempt->timefinish)){ $task->completedDate = $firstattempt->timefinish; $task->attemptedAfterExpiry = ($firstattempt->timefinish > $task->dueDate);}
            if(!$task->completed){ $task->attemptedAfterExpiry = (time() > $task->dueDate); }
            $task->started = $DB->record_exists_sql("select * from {quiz_attempts} qa inner join {quizattempts} qa1 on qa1.attemptid = qa.id where qa.quiz = ? and qa.userid=? and qa1.schoolyear=?", array($quiz->id, $USER->id, $this->currentschoolyear));

            if(!self::isInternalUser($course->id, $USER->id) && !$this->premiumAccount && $cm->uservisible && $quiz->accessibility){
                $task->locked = true;
            }
            $task->status = self::getMyStatus($task->locked, $task->completed, $task->started, $task->bestGrade);
            $tasktype = "exam";
            $xp = 0;
            if($xp_userData=$DB->get_record_sql("SELECT * FROM {xphistory} WHERE userid=? AND moduleid=? order by gotgrade desc limit 0, 1",array($USER->id,$task->quiz))){
                $xp=$xp_userData->gotgrade;
                $maxxp=$xp_userData->gotgrademax;
            } else {
                $maxxp= intval($XPSETTING->roundon)*$XPSETTING->scoremultiplier;
            }
            $task->xp = $xp;
            $task->maxxp = $maxxp;
            if($task->attemptcompleted){
                array_push($returndata[$tasktype]['completed'], $task);
            } else if($task->duedate < $currentdate){
                array_push($returndata[$tasktype]['notCompleted'], $task);
            } else {
                array_push($returndata[$tasktype]['ongoing'], $task);
            }

        }
        $this->sendResponse($returndata);
    }
    private function get_current_schoolyear(){
        global $DB;
        if($currentYear = $DB->get_record("schoolyear", array("current"=>1))){
            $this->currentYear = $currentYear;
            return $currentYear->id;
        } else if($currentYear = $DB->get_record_sql("select * from {schoolyear} where startdate < ? and enddate > ? ", array(time(), time()))){
            $currentYear->current=1;
            $DB->update_record("schoolyear", $currentYear);
            $this->currentYear = $currentYear;
            return $currentYear->id;
        } else {
            $DB->execute("update {schoolyear} set current=0 where current=1");
            $yearstart = strtotime("01 September ".date("Y"));
            $yearend = strtotime("31 July ".date("Y")+1);
            if($startyear>time()){
                $startyear = strtotime("-1 year", $yearstart);
                $yearend = strtotime("-1 year", $yearend);
            }
            $currentYear= new stdClass();
            $currentYear->name = date("Y")."-".date("Y");
            $currentYear->startdate = $yearstart;
            $currentYear->enddate = $yearend;
            $currentYear->current = 1;
            $currentYear->id = $DB->insert_record("schoolyear", $currentYear);
            $this->currentYear = $currentYear;
            return $currentYear->id;
        }
    }
    private function get_curreentsubscription($userid, $gradeid){
        global $DB, $USER, $PARENTUSER, $PAGE, $childid;
        if(empty($userid)){
            $userid = $USER->id;
        }
        // print_r("select * from {subscriptiondetails} where schoolyear=? userid=? and gradeid=? and enddate > ?", array($this->currentschoolyear, $userid, $PARENTUSER->currentChild->grade, time()));
        return $DB->get_record_sql("select * from {subscriptiondetails} where schoolyear=? and userid=? and gradeid=? and enddate > ?", array($this->currentschoolyear, $userid, $gradeid, time()));        
    }
    private function date_diff($fromdate, $todate){
        $date1=date_create(date("Y-m-d", $fromdate));
        $date2=date_create(date("Y-m-d", $todate));
        $diff=date_diff($date1,$date2);
        return $diff->format("%a");
    }
    public function getServerTime($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $returndata = array(
            "status"=>1, 
            "message"=>"success",
            "currentTime"=>time()
        );
        $this->sendResponse($returndata);
    }
    public function resetMyHomeworks($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $allrecords = $DB->get_records("quizattempts",array("userid"=>$USER->id));
        if(sizeof($allrecords)>0){
            $arra = array();
            foreach ($allrecords as $value) {
                // print_r($value);
                $value->qid = $value->id;
                $value->deleted = time();
                unset($value->id);
                // $value1 = (array) $value;
                // print_r($value1);
                // $DB->insert_record("quizattemptsbackup", $value);
                array_push($arra, $value);
                // die;
            }
            $DB->insert_records("quizattemptsbackup", $arra);
            $DB->delete_records("quizattempts",array("userid"=>$USER->id));
        }
        $returndata = array(
            "status"=>1, 
            "message"=>"success",
            "currentTime"=>time()
        );
        $this->sendResponse($returndata);
    }
    public function isUpdatedGrade($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $currentChild = $PARENTUSER->currentChild;
        $gradeid = $currentChild->grade;
        $this->gradeid = $gradeid;
        $this->userid = $USER->id;
        if($DB->record_exists_sql("select id from {childdataupdate} where lastupdated != lastfetched and userid=:userid and gradeid=:gradeid", array("userid"=>$USER->id, "gradeid"=>$gradeid))){
            $this->sendResponse(1);
        } else {
            $this->sendResponse(0);
        }
    }
    public function update_gradeupdate($gradeid = 0, $userid = 0, $updatedby=0){
        global $DB, $USER, $CFG, $PARENTUSER;
        if(!empty($USER) && empty($updatedby)){
            $updatedby = $USER->id;
        }
        $query = "UPDATE {childdataupdate} set lastupdated=:lastupdated, updatedby=:updatedby";
        if(!empty($gradeid)){
            $query = "UPDATE {childdataupdate} set lastupdated=:lastupdated, updatedby=:updatedby WHERE gradeid=:gradeid";
        }
        if(!empty($userid)){
            $query = "UPDATE {childdataupdate} set lastupdated=:lastupdated, updatedby=:updatedby WHERE userid=:userid ";
            if(!empty($gradeid)){
                $query = "UPDATE {childdataupdate} set lastupdated=:lastupdated, updatedby=:updatedby WHERE userid=:userid AND gradeid=:gradeid";
            }
        }
        $updatedata = array(
            "lastupdated"=>time(),
            "updatedby"=>$updatedby,
            "gradeid"=>$gradeid,
            "userid"=>$userid
        );

        return $DB->execute($query, $updatedata);
    }
    private function update_mygrade(){
        global $DB, $USER, $CFG, $PARENTUSER;
        $currentChild = $PARENTUSER->currentChild;
        $userid = $USER->id;
        $query = "UPDATE {childdataupdate} set lastupdated=:lastupdated, updatedby=:updatedby WHERE userid=:userid ";
        $updatedata = array(
            "lastupdated"=>time(),
            "updatedby"=>$USER->id,
            "userid"=>$userid
        );
        return $DB->execute($query, $updatedata);
    }
    private function generatescore($attempt){
        global $DB;
        $allteachers = $DB->get_records_sql("SELECT igm.*, h.id as homeworkid, h.categoryid FROM {institution_group_member} igm INNER JOIN {homework} h ON igm.groupid = h.groupid AND igm.tutor=1 AND igm.schoolyear=h.schoolyear INNER JOIN {institution_group_member} igm1 ON igm1.groupid = h.groupid AND igm1.tutor=0 AND igm1.schoolyear=h.schoolyear AND igm1.status=1 WHERE h.quiz = ? AND igm1.userid=?", array($attempt->moduleid, $attempt->userid));
        foreach ($allteachers as $key => $teacher) {

            $this->updateteacherscore($attempt, $teacher);
        }
    }
    private function updateteacherscore($attempt, $teacher){
        global $DB;
        $currentscore = $DB->get_field_sql("SELECT sum(score) from {scorecardteacher} WHERE institutionid=:institutionid AND schoolyear=:schoolyear AND gradeid=:gradeid AND teacherid=:teacherid ", array("institutionid"=>$teacher->institutionid, "schoolyear"=>$teacher->schoolyear, "gradeid"=>$teacher->institutionid, "homeworkid"=>$teacher->homeworkid, "teacherid"=>$teacher->userid, "studentid"=>$attempt->userid));
        if(empty($currentscore)){$currentscore=0;}
        $scoretoassign = $DB->get_record_sql("SELECT * FROM {scorecardlevel} WHERE (gradeid=0 or gradeid = ?) AND minpoints <= ? AND (maxpoints > ? OR maxpoints IS NULL) ORDER BY gradeid DESC, maxpoints DESC", array($attempt->categoryid, $currentscore, $currentscore));
        $score = $scoretoassign->oncompleted;
        if($attempt->passed == 1){$score += $scoretoassign->onsuccess;}
        $scorecardteacher= $DB->get_record("scorecardteacher", array("institutionid"=>$teacher->institutionid, "schoolyear"=>$teacher->schoolyear, "gradeid"=>$teacher->institutionid, "homeworkid"=>$teacher->homeworkid, "teacherid"=>$teacher->userid, "studentid"=>$attempt->userid));
        if(empty($scorecardteacher)){ $scorecardteacher = new stdClass(); }
        $scorecardteacher->institutionid=$teacher->institutionid;
        $scorecardteacher->schoolyear=$teacher->schoolyear;
        $scorecardteacher->gradeid=$attempt->categoryid;
        $scorecardteacher->teacherid=$teacher->userid;
        $scorecardteacher->homeworkid=$teacher->homeworkid;
        $scorecardteacher->attemptid=$attempt->attemptid;
        $scorecardteacher->studentid=$attempt->userid; 
        $scorecardteacher->score=$score;
        $scorecardteacher->scoredate=$attempt->timefinish;
        $scorecardteacher->modifieddate=time();
        if(!empty($scorecardteacher->id)){
            $DB->update_record("scorecardteacher", $scorecardteacher);
        } else {
            $DB->insert_record("scorecardteacher", $scorecardteacher);
        }
        print_r($scorecardteacher);
    }    
    public function isSchoolPayMember($userid)
    {
        global $DB;
        if($membership =  $DB->get_record_sql("SELECT igm.* FROM mdl_institution_group_member igm inner join mdl_institutions i on i.id=igm.institutionid where igm.schoolyear=? and igm.status=1 and igm.userid=?  and i.paymenttype=0", array($this->currentschoolyear, $userid))){
            return true;
        } else {
            return false;
        }
    }
    public function browserNotificationSubscription($args){
        global $DB, $USER;
        $status = $args['status'];
        $subscription = $args['subscription'];
        $oldrecord = $DB->get_record("push_subscribers", array("endpoint"=> $subscription['endpoint'], 'status'=>1));
        switch ($status) {
            case 'subscribe':
                if($oldrecord){
                    if($oldrecord->userid != $USER->id){
                        $oldrecord->status = 0;
                        $oldrecord->modifiedby = $USER->id;
                        $oldrecord->modifieddate = time();
                        $DB->update_record("push_subscribers", $oldrecord);
                        $newrecord = new stdClass();
                        $newrecord->userid = $USER->id;
                        $newrecord->endpoint = $subscription['endpoint'];
                        $newrecord->expirationtime = $subscription['expirationTime'];
                        $newrecord->p256dh = $subscription['keys']['p256dh'];
                        $newrecord->authkey = $subscription['keys']['auth'];
                        $newrecord->status = 1;
                        $newrecord->createdby = $USER->id;
                        $newrecord->createddate = time();
                        if($DB->insert_record("push_subscribers", $newrecord)){
                            $this->sendResponse(['status'=>'ok', 'message'=>'Subscribed']);
                        } else {
                            $this->sendResponse(['status'=>'error', 'message'=>'Try Again']);
                        }
                    } else {
                        $this->sendResponse(['status'=>'ok', 'message'=>'Already Subscribed']);
                    }
                } else {
                    $newrecord = new stdClass();
                    $newrecord->userid = $USER->id;
                    $newrecord->endpoint = $subscription['endpoint'];
                    $newrecord->expirationtime = $subscription['expirationTime'];
                    $newrecord->p256dh = $subscription['keys']['p256dh'];
                    $newrecord->authkey = $subscription['keys']['auth'];
                    $newrecord->status = 1;
                    $newrecord->createdby = $USER->id;
                    $newrecord->createddate = time();
                    if($DB->insert_record("push_subscribers", $newrecord)){
                        $this->sendResponse(['status'=>'ok', 'message'=>'Subscribed']);
                    } else {
                        $this->sendResponse(['status'=>'error', 'message'=>'Try Again']);
                    }
                }
                break;
            
            case 'unsubscribe':
                if($oldrecord){
                    $oldrecord->status = 0;
                    $oldrecord->modifiedby = $USER->id;
                    $oldrecord->modifieddate = time();
                    if($DB->update_record("push_subscribers", $oldrecord)){
                        $this->sendResponse(['status'=>'ok', 'message'=>'Unsubscribed']);
                    } else {
                        $this->sendResponse(['status'=>'error', 'message'=>'Try Again']);
                    }
                } else {
                    $this->sendResponse(['status'=>'ok', 'message'=>'Not Subscribed']);
                }
                break;
            
            default:
                $this->sendResponse(['status'=>'error', 'message'=>'Invalid Status']);
                break;
        }
    }
    public function getMyReport($args){
        global $DB, $USER, $CFG;
        $finalreport = array();
        $allreports = $DB->get_records("monthlyreport", array("userid"=> $USER->id, 'nodata'=>0));
        foreach ($allreports as $rkey => $report) {
            $data = json_decode($report->reportdata);
            unset($report->reportdata);
            foreach ($data->reportdata as $tkey => $topic) {
                $totalsubtopic =array();
                foreach ($topic->subtopic as $skey => $subtopic) {
                    $subtopicdata = new stdClass();
                    $subtopicdata->name = $subtopic->name;
                    $subtopicdata->type = $subtopic->type;
                    $subtopicdata->lang = $subtopic->lang;
                    $subtopicdata->total = $subtopic->ques_att->total_q;
                    $subtopicdata->fraction1 = number_format($subtopic->ques_att->fraction, 2);
                    $subtopicdata->maxfraction1 = number_format($subtopic->ques_att->maxfraction, 2);
                    $subtopicdata->maxfraction = number_format($data->xpsetting->roundon, 2);
                    $subtopicdata->fraction = (($subtopicdata->fraction1 > 0)?($subtopicdata->fraction1/$subtopicdata->maxfraction1)*$subtopicdata->maxfraction:0);
                    $subtopicdata->fraction = number_format($subtopicdata->fraction, 2);
                    $subtopicdata->maxmark = number_format($subtopic->ques_att->maxmark, 2);
                    $subtopicdata->percent = ($subtopicdata->fraction/$subtopicdata->maxfraction)*100;
                    $topic->subtopic->$skey = $subtopicdata;
                    array_push($totalsubtopic, $subtopicdata);
                }
                $topic->subtopic = $totalsubtopic;
                $topicdata = new stdClass();
                $topicdata->name = $topic->name;
                $topicdata->type = $topic->type;
                $topicdata->lang = $topic->lang;
                $topicdata->subtopic = $topic->subtopic;
                $data->reportdata[$tkey] = $topicdata;
            }
            $report->finaldata = $data;
            $report->month = $this->dateToFrench($report->reportmonth, "d F Y");
            $report->todate = $this->dateToFrench($report->reportmonth, "d F Y");
            $report->fromdate = $this->dateToFrench($USER->timecreated, "d F Y");
            $report->reportlink = $CFG->wwwroot."/local/designer/monthlyreport.php?id=".$report->id."&token=".$this->token;

            if($args['detailed'] != 1){
                unset($report->finaldata);
            }
            unset($report->nodata);
            unset($report->generatedby);
            unset($report->updateddate);
            unset($report->updatedby);
            unset($report->reportmonth);
            unset($report->generateddate);
            unset($report->userid);
            unset($report->grade);
            // $report->fromdate1 = (!empty($USER->timecreated)?($this->schoolyear->startdate > $USER->timecreated?:$this->schoolyear->startdate) :$this->schoolyear->startdate);
            // $report->fromdate2 = $this->schoolyear->startdate;
            // $report->fromdate3 = $USER->timecreated;
            array_push($finalreport, $report);
        }
        $this->sendResponse(array("allreports"=>$finalreport));
    }
    public function getReportDetails($id, $userid){
        global $DB, $USER, $CFG;
        $reportdata = null;
        if($report = $DB->get_record("monthlyreport", array("userid"=> $userid, 'id'=>$id, 'nodata'=>0))){
            $data = json_decode($report->reportdata);
            unset($report->reportdata);
            foreach ($data->reportdata as $tkey => $topic) {
                $totalsubtopic =array();
                foreach ($topic->subtopic as $skey => $subtopic) {
                    $subtopicdata = new stdClass();
                    $subtopicdata->name = $subtopic->name;
                    $subtopicdata->type = $subtopic->type;
                    $subtopicdata->lang = $subtopic->lang;
                    $subtopicdata->total = $subtopic->ques_att->total_q;
                    $subtopicdata->fraction1 = number_format($subtopic->ques_att->fraction, 2);
                    $subtopicdata->maxfraction1 = number_format($subtopic->ques_att->maxfraction, 2);
                    $subtopicdata->maxfraction = number_format($data->xpsetting->roundon, 2);
                    $subtopicdata->fraction = (($subtopicdata->fraction1 > 0)?($subtopicdata->fraction1/$subtopicdata->maxfraction1)*$subtopicdata->maxfraction:0);
                    $subtopicdata->fraction = number_format($subtopicdata->fraction, 2);
                    $subtopicdata->maxmark = number_format($subtopic->ques_att->maxmark, 2);
                    $subtopicdata->percent = ($subtopicdata->fraction/$subtopicdata->maxfraction)*100;
                    $topic->subtopic->$skey = $subtopicdata;
                    array_push($totalsubtopic, $subtopicdata);
                }
                $topic->subtopic = $totalsubtopic;
                $topicdata = new stdClass();
                $topicdata->name = $topic->name;
                $topicdata->type = $topic->type;
                $topicdata->lang = $topic->lang;
                $topicdata->subtopic = $topic->subtopic;
                $data->reportdata[$tkey] = $topicdata;
            }
            $report->finaldata = $data;
            $report->month = $this->dateToFrench($report->reportmonth, "d F Y");
            $report->todate = $this->dateToFrench($report->reportmonth, "d F Y");
            $report->fromdate1 = (!empty($USER->timecreated)?($this->schoolyear->startdate > $USER->timecreated?:$this->schoolyear->startdate) :$this->schoolyear->startdate);
            $report->fromdate2 = $this->schoolyear->startdate;
            $report->fromdate3 = $USER->timecreated;
            $report->fromdate = $this->dateToFrench($USER->timecreated, "d F Y");
            $report->reportlink = $CFG->wwwroot."/local/designer/monthlyreport.php?id=".$report->id."&token=".$this->token;
            $reportdata = $report;
        }
        return $reportdata;
    }
    function dateToFrench($date, $format = 'd F Y H:i') 
    {
        if (empty($date)) { return '';}
        if (empty($format)) { $format = 'd F Y H:i';}
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $french_months = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, $date ) ) );
    }
    public function getTopicSubtopic($args){
        global $DB, $CFG, $USER, $PARENTUSER, $XPSETTING, $ISINTERNAL;
        $topictype = "quest";
        if(!empty($PARENTUSER->currentChild)){
            $currentChild = $PARENTUSER->currentChild;
            $gradeid = $currentChild->grade;
            $message = array();
            $userlang = $args['lang'];
            // $alllangs = get_string_manager()->get_list_of_translations();
            $alllangs = json_decode('{"en":"English \u200e(en)\u200e","fr":"English \u200e(fr)\u200e","fr_old":"Fran\u00e7ais \u200e(fr_old)\u200e","ar":"\u0627\u0644\u0639\u0631\u0628\u064a\u0629 \u200e(ar)\u200e"}');
            if(!empty($userlang) && array_key_exists($userlang, $alllangs)){
                if(in_array($gradeid, array(25,26,4,5))){
                    $this->userlangauge = $userlang;
                } else {
                    $this->userlangauge = 'fr';
                }
            }
            $subscriptionstatus = $currentChild->currentSubscription;
            $currentyeadending = strtotime("30 June ".date("Y"));
            if($currentyeadending < time()){
                $currentyeadending = strtotime("30 June ".date("Y", strtotime("+1 year")));
            }
            $this->internal = $DB->record_exists_sql("SELECT ue.*, e.enrol, c.category from mdl_user_enrolments ue INNER JOIN mdl_enrol e on e.id=ue.enrolid INNER JOIN mdl_course c on c.id=e.courseid WHERE ue.userid = ? and e.enrol != 'self' and c.category = ?", array($USER->id, $gradeid));
            // self::enrolluserincourses($currentChild->id, $currentChild->grade, "self", $currentyeadending);
            $XPSETTING = $DB->get_record("xpsetting", array("gradeid"=>$gradeid));
            if(empty($XPSETTING)){
                $XPSETTING=new stdClass();
                $XPSETTING->scoremultiplier = 100;
                $XPSETTING->roundon = 10;
                $XPSETTING->bonus_score = 10;
            }
            $topic = $DB->get_record_sql("SELECT cs.* FROM {view_course_sections} cs WHERE cs.id = ?", array($args['topicid']));
            if($topic->lang && $userlang != $topic->lang){
                $topic = $DB->get_record_sql("SELECT cs.* FROM {view_course_sections} cs WHERE cs.clonetopics = ?", array($topic->id));
            }
            $course = $DB->get_record_sql("SELECT cs.* FROM {course} cs WHERE cs.id = ?", array($topic->course));

            $maintopicdata = self::get_topic($course, $topic, $topictype);
            $this->setstatesfortopics($course->id);
            $alltopics = $DB->get_records_sql("SELECT cs.* FROM {view_course_sections} cs WHERE cs.course = ? and cs.parent=? and (cs.lang IS NULL or cs.lang=? or cs.lang=?) and (cs.type IS NULL or cs.type='' or cs.type=?) AND (cs.forgrades = 0 OR FIND_IN_SET(?,cs.forgrades)) and cs.isattempt = 0 order by section asc", array($course->id, $topic->section, "", $this->userlangauge,$topic->type, $gradeid));
            $alltopicdata = array();
            foreach ($alltopics as $key => $topic) {
                $restrictiontype = "topic";
                if(!empty($parent)){
                    $restrictiontype = "subtopic";
                    $topic->type = $parent->type;
                }
                $sectioninfo = get_fast_modinfo($course, $USER->id)->get_section_info($topic->section);
                if(empty($topic->name)){
                    $topic->name=get_section_name($course, $sectioninfo);
                }
                $topic->uservisible = !$sectioninfo->uservisible;
                $topic->restricted = self::isrestricted($restrictiontype, $topic->id);

                $topicdata = self::get_topic($course, $topic, $topictype);
                if(!empty($topicdata)){
                    $topicdata['position'] = sizeof($alltopicdata)+1;
                    array_push($alltopicdata, $topicdata); 
                }
            }
            if(!empty($alltopicdata)){
                $data = new stdClass();
                $data->subtopicdata = $alltopicdata;
                $data->maintopicdata = $maintopicdata;
                // $data->lastUpdated = self::get_lastupdated($gradeid, 1);
                $this->sendResponse($data);
            } else {
                $this->sendError("Invalid topicid", "Child elements not Found");
            }
        } else {
            $this->sendError("Failed", "Please try through Child Account");
        }
    }
    public function getTopicQuizes($args){
        global $DB, $CFG, $USER, $PARENTUSER, $XPSETTING, $ISINTERNAL;
        if(!empty($PARENTUSER->currentChild)){
            $currentChild = $PARENTUSER->currentChild;
            $gradeid = $currentChild->grade;
            $message = array();
            $userlang = $args['lang'];
            // $alllangs = get_string_manager()->get_list_of_translations();
            $alllangs = json_decode('{"en":"English \u200e(en)\u200e","fr":"English \u200e(fr)\u200e","fr_old":"Fran\u00e7ais \u200e(fr_old)\u200e","ar":"\u0627\u0644\u0639\u0631\u0628\u064a\u0629 \u200e(ar)\u200e"}');
            if(!empty($userlang) && array_key_exists($userlang, $alllangs)){
                if(in_array($gradeid, array(25,26,4,5))){
                    $this->userlangauge = $userlang;
                } else {
                    $this->userlangauge = 'fr';
                }
            }
            $subscriptionstatus = $currentChild->currentSubscription;
            $currentyeadending = strtotime("30 June ".date("Y"));
            if($currentyeadending < time()){
                $currentyeadending = strtotime("30 June ".date("Y", strtotime("+1 year")));
            }
            $this->internal = $DB->record_exists_sql("SELECT ue.*, e.enrol, c.category from mdl_user_enrolments ue INNER JOIN mdl_enrol e on e.id=ue.enrolid INNER JOIN mdl_course c on c.id=e.courseid WHERE ue.userid = ? and e.enrol != 'self' and c.category = ?", array($USER->id, $gradeid));
            // self::enrolluserincourses($currentChild->id, $currentChild->grade, "self", $currentyeadending);
            $XPSETTING = $DB->get_record("xpsetting", array("gradeid"=>$gradeid));
            if(empty($XPSETTING)){
                $XPSETTING=new stdClass();
                $XPSETTING->scoremultiplier = 100;
                $XPSETTING->roundon = 10;
                $XPSETTING->bonus_score = 10;
            }
            $topic = $DB->get_record_sql("SELECT cs.* FROM {view_course_sections} cs WHERE cs.id = ?", array($args['topicid']));

            $course = $DB->get_record_sql("SELECT cs.* FROM {view_course} cs WHERE cs.id = ?", array($topic->course));
            $quizes =  self::get_quizes($course, $topic);
            if(!empty($quizes)){
                $data = new stdClass();
                $data->quizes = $quizes;
                // $data->lastUpdated = self::get_lastupdated($gradeid, 1);
                $this->sendResponse($data);
            } else {
                $this->sendError("Invalid topic", "Quiz not Found");
            }
        } else {
            $this->sendError("Failed", "Please try through Child Account");
        }
    }
    public function deleteAccount($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid,$free, $gradeid;
        if($child = $DB->get_record("childusers", array("userid"=>$USER->id))){
            $child->deleted = 1;
            if($DB->update_record("childusers", $child)){
                $DB->set_field('userlogindevices', 'loginstatus', 0, array('userid' => $USER->id));
                $this->sendResponse(array("message"=>"Successfuly deleted"));
            } else {
                $this->sendError("Failed to delete Account", "Failed to delete Account");
            }
        } else {
            $this->sendError("Unable to get your account", "Unable to get your account");
        }
    }
    public function getdeletedAccount($args){
        global $DB, $USER, $CFG, $PARENTUSER, $childid,$free, $gradeid;
        $devicetoken = $args['devicetoken'];
        $child = array_values($DB->get_records_sql("select u.id, c.parentid, c.userid, c.image, u.alternatename as charname
from mdl_userlogindevices ud 
inner join mdl_user u on u.id=ud.userid and u.deleted = 0 and u.confirmed=1 
inner join mdl_childusers c on c.userid = u.id AND c.deleted=1 
where ud.token=:devicetoken
and ud.status=1 
group by c.userid
order by ud.id desc", array("devicetoken"=>$devicetoken)));
        if(sizeof($child) > 0){
            $this->sendResponse(array("users"=>$child));
        } else {
            $this->sendError("No account found", "No account found");
        }
    }
    public function recoverAccount($args){
        global $DB;
        $devicetoken = $args['devicetoken'];
        $userid = $args['userid'];
        if($logindevice = $DB->get_record_sql("select ud.*
from mdl_userlogindevices ud 
inner join mdl_user u on u.id=ud.userid and u.deleted = 0 and u.confirmed=1 
inner join mdl_childusers c on c.userid = u.id AND c.deleted=1 
where ud.token=:devicetoken
and ud.status=1 
and ud.userid=:userid 
order by ud.id desc limit 0, 1", array("devicetoken"=>$devicetoken, "userid"=>$userid))){
            
            $DB->set_field('childusers', 'deleted', 0, array('userid' => $userid));
            $logindevice->loginstatus = 1;
            unset($logindevice->id);
            if($DB->insert_record("userlogindevices", $logindevice)){
                $this->sendResponse(array("message"=>"Account recovered successfully"));
            } else {
                $this->sendError("Failed to delete Account", "Failed to delete Account");
            }
        } else {
            $this->sendError("Unable to get your account", "Unable to get your account");
        }
    }
    public function questionIsBlocked($args){
        global $DB, $USER;
        $args = (object)$args;
        if($args->homeworkid && $args->questionid && $DB->get_record("homework_blockings", array("homeworkid"=>$args->homeworkid, "questionid"=>$args->questionid))){
            $this->sendResponse(array("sucess"=>true, "blocked"=>1, "message"=>"Please wait for your tutor to enable next question", "questionid"=>$args->questionid, "homeworkid"=>$args->homeworkid));
        } else {
            $this->sendResponse(array("sucess"=>true, "blocked"=>0, "message"=>"Question is enable, you can use next question", "questionid"=>$args->questionid, "homeworkid"=>$args->homeworkid));
        }
    }
    private function get_blockedquestions($homeworkid) {
        global $DB, $USER;
        return $DB->get_fieldset_sql("SELECT questionid FROM {homework_blockings} WHERE homeworkid=:homeworkid", array("homeworkid"=>$homeworkid));
    }
    public function viewDiagnostic($args){
        global $DB, $USER, $CFG, $PARENTUSER;
        $args = (object)$args;
        if($args->courseid){
            $data = new stdClass();
            $data->userid = $USER->id;
            $data->courseid = $args->courseid;
            $data->viewtime = time();
            if($DB->insert_record("tarldiagnosticviewed", $data)){
                $responsedata['status'] = 1;
                $responsedata['message'] = "Updated Successfuly";
                $this->sendResponse($responsedata);
            } else {
                $this->sendError("failed to update status", "failed to update status, please try again");
            }
        } else {
            $this->sendError("Error", "Invalid course ID");
        }
    }
    public function get_parenttopics($sectionid){
        global $DB;
        $parents = array();
        $topic = $DB->get_record_sql("SELECT cs.id, cs.name, cfo.value as parent, cs1.id as parentid, cs.section, cs.course, cs.forgrades from {course_sections} cs INNER JOIN {course_format_options} cfo ON cfo.courseid = cs.course AND cfo.sectionid =cs.id AND cfo.name = 'parent' LEFT JOIN {course_sections} cs1 on cs1.course = cfo.courseid AND cs1.section = cfo.value where cs.id=:sectionid", array("sectionid"=>$sectionid));
        if($topic){
            if(!empty($topic->parent)){
                $t1 = self::get_parenttopics($topic->parentid);
                // print_r($t1);
                if(sizeof($t1)>0){
                    $parents = array_merge($parents, $t1);
                }
            }
            array_push($parents, $topic);
        }
        return $parents;
    }
    public function marktarlstatus($topicid, $status=3)
    {
        global $DB, $USER;
        if($parent=$DB->get_record("course_sections", array("id"=>$topicid))){
            if($status == 1){
                $childs = array_values($DB->get_records_sql("SELECT cs1.*, cfo1.value as parent, tc.status FROM {course_sections} cs1 left join {course_format_options} cfo1 on cfo1.name='parent' AND cfo1.courseid=cs1.course AND cs1.id = cfo1.sectionid LEFT JOIN {tarltopics_completion} tc on cs1.course=tc.courseid AND cs1.id=tc.topicid AND tc.userid=:userid WHERE  cs1.isattempt=0 AND cs1.course = :course and cfo1.value=:section", array("course"=>$parent->course, "section"=>$parent->section, "userid"=>$USER->id)));
                if(sizeof($childs) > 0){
                    $totalstatus = array_column($childs, 'status');
                    $statuscount = array_count_values($totalstatus);
                    if(!in_array(1, $totalstatus)){
                        foreach ($childs as $key => $child) {
                            $exist = $DB->get_record("tarltopics_completion", array("userid"=>$USER->id, "topicid"=>$child->id));
                            if($exist){
                                if($exist->status != 2){
                                    $exist->status = $status;
                                    $DB->update_record("tarltopics_completion", $exist);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            $exist = $DB->get_record("tarltopics_completion", array("userid"=>$USER->id, "topicid"=>$topicid));
            if($exist){
                if($exist->status== 3 || ($exist->status < $status)){
                    $exist->status = $status;
                    $DB->update_record("tarltopics_completion", $exist);
                }
                $parents = array_reverse($this->get_parenttopics($topicid));
                foreach ($parents as $key => $parent) {
                    if($parent->id == $topicid){continue;}
                    $this->calculateparentstatus($parent);
                }
            } else {

            }
        }
    }
    private function calculateparentstatus($parent)
    {
        global $DB, $USER, $PARENTUSER;
        $gradeid=0;
        if($currentChild = $PARENTUSER->currentChild){
            $gradeid = $currentChild->grade;
        }
        $alltopics = $DB->get_records_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE cs.isattempt=0 AND  course = ? and cfo.value=? AND (cs.forgrades = 0 OR FIND_IN_SET(?,cs.forgrades)) AND cs.isdiagnostic=0 order by section asc", array("parent", $parent->course, $parent->section, $gradeid));

        // $alltopics = $DB->get_records_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE course = ? and cfo.value=? order by section asc", array("parent", $parent->course, $parent->section));
        $allchilds = array_keys($alltopics);
        
        if(sizeof($allchilds) > 0){
            $childids = implode(",", $allchilds);
            $qry="select tc.* FROM {tarltopics_completion} tc WHERE tc.topicid in({$childids}) AND tc.userid=:userid";
            $totalchild = sizeof($allchilds);
            $allstatus = $DB->get_records_sql($qry,array("userid"=>$USER->id));

            // if($parent->id==5662){
            //     print_r($parent);
            //     print_r($alltopics);
            //     print_r($allstatus);
            // }
            // print_r($allstatus);
            $none = 0;
            $started = 0;
            $completed = 0;
            $locked = 0;
            foreach ($allstatus as $s) {
                switch ($s->status) {
                    case 0:
                        $none++;
                        break;
                    case 1:
                        $started++;
                        break;
                    case 2:
                        $completed++;
                        break;
                    case 3:
                        $locked++;
                        break;
                }
            }
            $finalstatus = 3;
            if($completed > 0){$finalstatus = 1;}
            if($completed == $totalchild){
                $finalstatus = 2;
            } else if($started) {
                $finalstatus = 1;
            }
            // array_push($this->parentstatus, array($parent->id, $parent->name, $totalchild, $allstatus, $finalstatus, array($none, $started,$completed, $locked)));            
            $exist = $DB->get_record("tarltopics_completion", array("userid"=>$USER->id, "topicid"=>$parent->id));
            if($exist){
                if($exist->status== 3 || ($exist->status < $finalstatus)){
                    array_push($this->parentstatus, array($parent->id, $parent->name, "updated", $finalstatus));            
                    $exist->status = $finalstatus;
                    $DB->update_record("tarltopics_completion", $exist);
                }
            } else {
                array_push($this->parentstatus, array($parent->id, $parent->name, "inserted", $finalstatus));            
                $exist = new stdClass();
                $exist->userid = $USER->id;
                $exist->courseid = $parent->course;
                $exist->topicid = $parent->id;
                $exist->status = $finalstatus;
                $exist->updatedtime = time();
                $DB->insert_record("tarltopics_completion", $exist);
            }
        }
    }
    private function finishTarl($attempt){
        global $DB, $USER;
        $allrecords = $DB->get_records("tarldiagnostic", array("quiz"=>$attempt->cmid, "userid"=>$USER->id));
        foreach ($allrecords as $key => $record) {
            $update = false;
            if(empty($record->timefinish)){
                $record->ispassed = $attempt->ispassed;
                $record->nextquiz = $attempt->nextquiz;
                $record->ispassed = $attempt->ispassed;
                $record->component = $attempt->component;
            }
            if(empty($record->timestart)){
                $record->timestart = $attempt->timestart;
                $update = true;
            }
            if(empty($record->timefinish)){
                $record->timefinish = $attempt->timefinish;
                $update = true;
                if($attempt->ispassed){
                    $this->marktarlstatus($record->topic, 2);
                }
            }
            $DB->update_record("tarldiagnostic", $record);
        }
    }
    public function get_coursetopicstree($course, $topictype, $parent = null){
        global $DB, $CFG, $PARENTUSER;
        $gradeid=0;
        if($currentChild = $PARENTUSER->currentChild){
            $gradeid = $currentChild->grade;
        }
        require_once($CFG->dirroot."/course/lib.php");
        $alltopicdata = array();

        $langfilter = "";
        if($course->coursetype != 6){
            $langfilter = "and (cs.lang IS NULL or cs.lang=? or cs.lang=?)";
        }
        if(empty($parent)){
            $alltopics = $DB->get_recordset_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE cs.isattempt=0 AND  course = ? and (cfo.value IS NULL OR cfo.value=0) and cs.type = ? {$langfilter} order by section asc", array("parent", $course->id, $topictype, "", $this->userlangauge));
            // print_r(array("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE course = ? and (cfo.value IS NULL OR cfo.value=0) and cs.type = ? {$langfilter} order by section asc", array("parent", $course->id, $topictype, "", $this->userlangauge), $alltopics));
        } else if(!empty($parent->section)) {
            $alltopics = $DB->get_recordset_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE cs.isattempt=0 AND  course = ? and cfo.value=? AND (cs.forgrades = 0 OR FIND_IN_SET(?,cs.forgrades)) AND (cs.type = NULL OR cs.type = '' OR  cs.type = ?) {$langfilter} order by section asc", array("parent", $course->id, $parent->section, $gradeid, $topictype, "", $this->userlangauge));
        } else {
            // echo "3";die;
            $alltopics = array();
        }
        foreach ($alltopics as $topic) {
            $restrictiontype = "topic";
            if($topic->section==0){continue;}
            if(!empty($parent)){
                $restrictiontype = "subtopic";
                $topic->type = $parent->type;
            }
            $topic->childs = $this->get_coursetopicstree($course, $topictype, $topic);
            array_push($alltopicdata, $topic);
        }
        return $alltopicdata;
    }
    public function get_coursetopicstill($course, $topictype, $parent = null){
        global $DB, $CFG, $PARENTUSER;
        $gradeid=0;
        if($currentChild = $PARENTUSER->currentChild){
            $gradeid = $currentChild->grade;
        }
        require_once($CFG->dirroot."/course/lib.php");
        $alltopicdata = array();

        $langfilter = "";
        if($course->coursetype != 6){
            $langfilter = "and (cs.lang IS NULL or cs.lang=? or cs.lang=?)";
        }
        if(empty($parent)){
            $alltopics = $DB->get_recordset_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE cs.isattempt=0 AND  course = ? and (cfo.value IS NULL OR cfo.value=0) and cs.type = ? {$langfilter} order by section asc", array("parent", $course->id, $topictype, "", $this->userlangauge));
            // print_r(array("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE course = ? and (cfo.value IS NULL OR cfo.value=0) and cs.type = ? {$langfilter} order by section asc", array("parent", $course->id, $topictype, "", $this->userlangauge), $alltopics));
        } else if(!empty($parent->section)) {
            $alltopics = $DB->get_recordset_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE cs.isattempt=0 AND course = ? and cfo.value=? AND (cs.forgrades = 0 OR FIND_IN_SET(?,cs.forgrades)) AND (cs.type = NULL OR cs.type = '' OR  cs.type = ?) {$langfilter} order by section asc", array("parent", $course->id, $parent->section, $gradeid, $topictype, "", $this->userlangauge));
        } else {
            // echo "3";die;
            $alltopics = array();
        }
        foreach ($alltopics as $topic) {
            $restrictiontype = "topic";
            if($topic->section==0){continue;}
            if(!empty($parent)){
                $restrictiontype = "subtopic";
                $topic->type = $parent->type;
            }
            $exist = $DB->get_record("tarltopics_completion", array("userid"=>$USER->id, "topicid"=>$t->id));
            if($exist && $exist->status == 2){
                $topic->havechild=false;
                array_push($alltopicdata, $topic);
                continue;
            } else {
                $childs = $this->get_coursetopicstill($course, $topictype, $topic);
                if(sizeof($childs) > 0){
                    $topic->havechild=true;
                    array_push($alltopicdata, $topic);
                    $alltopicdata = array_merge($alltopicdata, $childs);
                } else {
                    $topic->havechild=false;
                    array_push($alltopicdata, $topic);
                }
            }
            // $childs = $this->get_coursetopicstill($course, $topictype, $topic);
            // if(sizeof($childs) > 0){
            //     $topic->havechild=true;
            //     array_push($alltopicdata, $topic);
            //     $alltopicdata = array_merge($alltopicdata, $childs);
            // } else {
            //     $topic->havechild=false;
            //     array_push($alltopicdata, $topic);
            // }
        }
        return $alltopicdata;
    }
    public function assign_component($topicid) {
        global $DB, $USER, $PARENTUSER;
        $topic = $DB->get_record("course_sections", array("id"=>$topicid));
        if($topic){
            if($topic->lang && $topic->lang != $this->userlangauge){
                $this->userlangauge = $topic->lang;
            }
            $course = $DB->get_record("course", array("id"=>$topic->course));
            $topics = $this->get_coursetopicstill($course, 'quest', null);
            // print_r($topics);
            $ids = array();
            $activeid = 0;
            $checknext = false;
            foreach ($topics as $key => $element) {
                if(!$checknext){
                    if(!empty($element->sequence) || !$element->havechild){
                        array_push($ids, $element);
                    }
                } else {
                    if(!empty($element->sequence)){
                        $activeid = $element;
                        break;
                    }
                }
                if($element->id == $topicid){
                    $checknext =true;
                }
            }
            $this->followingassignedcompleted = $ids;
            foreach ($ids as $key => $t) {
                // print_r($t);
                $exist = $DB->get_record("tarltopics_completion", array("userid"=>$USER->id, "topicid"=>$t->id));
                // print_r($exist);
                $status = 2;
                if($exist){
                    if($exist->status == 3 || $exist->status < $status){
                        $exist->status = $status;
                        $DB->update_record("tarltopics_completion", $exist);
                        $parents = array_reverse($this->get_parenttopics($t->id));
                        // print_r($parents);
                        foreach ($parents as $key => $parent) {
                            if($parent->id == $t->id){continue;}
                            $this->calculateparentstatus($parent);
                        }
                    }
                }
            }
        }
        return array($topicid, $DB->get_field_sql("SELECT count(id) FROM {tarltopics_completion} WHERE userid=:userid and status=2", array("userid"=>$USER->id)),$USER->id, $PARENTUSER);
    }
    public function setstatesfortopics($courseid)
    {
        global $DB, $CFG, $TOPICTYPE, $PARENTUSER, $USER;
        if($course = $DB->get_record("course", array("id"=>$courseid))){
            $missingtopics = $DB->get_recordset_sql("SELECT cs.* FROM {course_sections} cs LEFT JOIN {tarltopics_completion} tc ON tc.userid=:userid AND tc.courseid=cs.course AND tc.topicid=cs.id WHERE tc.id IS NULL AND cs.course=:course", array("userid"=>$USER->id, "course"=>$courseid));
            $tobeadded = array();
            foreach ($missingtopics as  $missingtopic) {
                $mdata = new stdClass();
                $mdata->userid = $USER->id;
                $mdata->catrgoryid = $course->category;
                $mdata->courseid = $course->id;
                $mdata->topicid = $missingtopic->id;
                $mdata->status = 3;
                $mdata->updatedtime = time();
                array_push($tobeadded, $mdata);
                // $DB->insert_record("tarltopics_completion", $mdata);
            }
            if(sizeof($tobeadded) > 0){
                $DB->insert_records("tarltopics_completion", $tobeadded);
            }
        }
    }
    private function get_tarlquizid($topic)
    {
        global $DB, $CFG, $TOPICTYPE, $PARENTUSER, $USER;
        $topic = (object)$topic;
        $startedindex = array_search(1, array_column($topic->childs, 'status'));
        $compltedindex = array_search(2, array_column($topic->childs, 'status'));
        $lockedindex = array_search(3, array_column($topic->childs, 'status'));
        if($startedindex !== false && $child = $topic->childs[$startedindex]){
            return $child->id;
        }
        if($lockedindex !== false && $child = $topic->childs[$lockedindex]){
            $DB->set_field('tarltopics_completion', 'status', 1, array('userid' => $USER->id, 'topicid' => $child->id));
            return $child->id;
        }
        return 0;
    }
    public function assignComponent($args){
        global $DB;
        $topicid = $args['topicid'];
        $this->userlangauge = "ar";
        if($topicid){
            $d = $this->assign_component($topicid);
            $this->sendResponse($d);
        } else {
            $this->sendError("Failed", "Try Later");
        }
    }
    public function get_nexttopic($section)
    {
        global $DB, $CFG, $TOPICTYPE, $PARENTUSER, $USER;
        $currentChild = $PARENTUSER->currentChild;
        $gradeid = $currentChild->grade;
        $allsiblings = array_values($DB->get_records_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs INNER JOIN {course_format_options} cfo ON cfo.sectionid=cs.id AND cfo.courseid=cs.course AND cfo.name='parent' WHERE cs.isattempt=:isattempt AND cfo.value=:parent AND cs.course=:course AND (cs.forgrades = 0 OR FIND_IN_SET(:csforgrades,cs.forgrades)) order by cs.section", array("parent"=>$section->parent, "isattempt"=>$section->isattempt, "course"=>$section->course, "csforgrades"=>$gradeid)));
        // print_r($allsiblings);
        $pos = array_search($section->id, array_column($allsiblings, 'id'));
        if($pos+1 >= sizeof($allsiblings)){
            if($section->parent != 0){
                $section = $DB->get_record_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs LEFT JOIN {course_format_options} cfo ON cfo.sectionid=cs.id AND cfo.name='parent' WHERE cs.section=:section AND cs.course=:course", array("course"=>$section->course, "section"=>$section->parent));
                $nexttopic= $this->get_nexttopic($section);
                if($nexttopic){
                    $childs = array_values($DB->get_records_sql("SELECT cs1.*, cfo1.value as parent, tc.status FROM {course_sections} cs1 left join {course_format_options} cfo1 on cfo1.name='parent' AND cfo1.courseid=cs1.course AND cs1.id = cfo1.sectionid LEFT JOIN {tarltopics_completion} tc on cs1.course=tc.courseid AND cs1.id=tc.topicid AND tc.userid=:userid WHERE cs1.isattempt=:isattempt AND cs1.course = :course and cfo1.value=:section order by cs1.section asc", array("isattempt"=>$nexttopic->isattempt, "course"=>$nexttopic->course, "section"=>$nexttopic->section, "userid"=>$USER->id)));
                    $seqs = array_filter(array_column($topicdata["childs"], 'sequence'));
                    if(!empty($seqs)){
                        return $nexttopic;
                    } else {
                        return $this->getfirstchild($nexttopic);
                    }
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            $nextsection = $allsiblings[$pos+1];
            return $nextsection;
        }
    }
    public function getfirstchild($topic, $type="quest")
    {
        global $DB, $CFG, $TOPICTYPE, $PARENTUSER, $USER;
        $currentChild = $PARENTUSER->currentChild;
        $gradeid = $currentChild->grade;
        $langfilter = "and (cs.lang IS NULL or cs.lang=? or cs.lang=?)";
        $childs = array_values($DB->get_records_sql("SELECT cs1.*, cfo1.value as parent, tc.status FROM {course_sections} cs1 left join {course_format_options} cfo1 on cfo1.name='parent' AND cfo1.courseid=cs1.course AND cs1.id = cfo1.sectionid LEFT JOIN {tarltopics_completion} tc on cs1.course=tc.courseid AND cs1.id=tc.topicid AND tc.userid=:userid WHERE cs1.isattempt = :isattempt and cs1.course = :course and cfo1.value=:section AND (cs1.forgrades = 0 OR FIND_IN_SET(:gradeid,cs1.forgrades)) order by cs1.section asc", array("isattempt"=>$topic->isattempt,"course"=>$topic->course, "section"=>$topic->section, "userid"=>$USER->id, "gradeid"=>$gradeid)));
        $seqs = array_filter(array_column($childs, 'sequence'));
        if(!empty($seqs)){
            return $topic;
        } else {
            $langfilter = "and (cs.lang IS NULL or cs.lang=? or cs.lang=?)";
            $alltopics = array_values($DB->get_records_sql("SELECT cs.*, cfo.value as parent FROM {course_sections} cs left join {course_format_options} cfo on cs.id = cfo.sectionid and cfo.name=? WHERE cs.isattempt = ? and course = ? and cfo.value=? AND (cs.forgrades = 0 OR FIND_IN_SET(?,cs.forgrades)) AND (cs.type = NULL OR cs.type = '' OR  cs.type = ?) {$langfilter} order by cs.section asc", array("parent", $topic->isattempt, $topic->course, $topic->section, $gradeid, $type, "", $this->userlangauge)));
            if(sizeof($alltopics) > 0){
                $firstsubtopic= $alltopics[0];
                return $this->getfirstchild($firstsubtopic, $type);
            } else {
                return $topic;
            }
        }
    }
    public function getTarlTopicStatus($topic)
    {
        global $DB, $USER;
        $ids = array($topic->id);
        $othertopic = null;
        if($topic->clonetopics){
            $othertopic = $DB->get_record_sql("SELECT * FROM {view_course_sections} WHERE id=:id", array("id"=>$topic->clonetopics));
            array_push($ids, $othertopic->id);
        } else {
            $othertopic = $DB->get_record_sql("SELECT * FROM {view_course_sections} WHERE clonetopics=:clonetopics", array("clonetopics"=>$topic->id));
            array_push($ids, $othertopic->id);
        }
        $ids = array_values(array_filter($ids));
        $allstatusdata = $DB->get_records_sql("SELECT * FROM {tarltopics_completion} WHERE userid=:userid AND courseid=:courseid AND topicid IN(".implode(",", $ids).")", array("userid"=>$USER->id, "courseid"=>$topic->course, "topicid"=>$topic->id));
        $allstatus = array_column($allstatusdata, "status");
        if (in_array(2, $allstatus)) {
            return 2;
        } else if (in_array(1, $allstatus)) {
            return 1;
        } else if (in_array(3, $allstatus)) {
            return 3;
        } else {
            return 0;
        }
    }
    public function getOtherTopic($topicid)
    {
        global $DB;
        $othertopic = null;
        if($topic = $DB->get_record("course_sections", array("id"=>$topicid))){
            if($topic->clonetopics){
                $othertopic = $DB->get_record_sql("SELECT * FROM {view_course_sections} WHERE id=:id", array("id"=>$topic->clonetopics));
                array_push($ids, $othertopic->id);
            } else {
                $othertopic = $DB->get_record_sql("SELECT * FROM {view_course_sections} WHERE clonetopics=:clonetopics", array("clonetopics"=>$topic->id));
                array_push($ids, $othertopic->id);
            }
        }
        return $othertopic;
    }
    private function autoexecutionscript($userid, $functionname, $priority=10, $args1=null, $args2=null, $args3=null, $args4=null){
        global $DB;
        $std=new stdClass();
        $std->userid=$userid;
        $std->functionname=$functionname;
        $std->priority=$priority;
        $std->args1=$args1;
        $std->args2=$args2;
        $std->args3=$args3;
        $std->args4=$args4;
        $std->creaetedtime=time();
        $DB->insert_record("autoexecutionscript",$std);
    }
    private function getattemptno($i){
        $attempt= $i;
        if($i > 5){
            $div = intval($i/5);
            $attempt= $i-(4*$div);
            if($attempt > 5){
                $attempt = $this->getattemptno($attempt);
            }
        }
        return $attempt;
    }
    private function getQtypeVideo($question){
        global $CFG;
        $typevideo = '';
        switch ($question['type']) {
            case 'ddwtos':
                $typevideo = "{$CFG->wwwroot}/local/designer/qtypevideos/Vidéo_drag and drop_text.mp4";
                break;
            case 'ddimageortext':
                $typevideo = "{$CFG->wwwroot}/local/designer/qtypevideos/Video_drag and drop_ope.mp4";
                break;
            case 'multichoice':
                $typevideo = "{$CFG->wwwroot}/local/designer/qtypevideos/Vidéo_Multiple choices.mp4";
                break;
            case 'multiselect':
                $typevideo = "{$CFG->wwwroot}/local/designer/qtypevideos/Vidéo_single choice.mp4";
                break;
            case 'shortanswer':
            case 'numerical':
            case 'calculated':
                $typevideo = "{$CFG->wwwroot}/local/designer/qtypevideos/Vidéo_value.mp4";
                break;
            case 'multianswer':
                if(strpos($question['questiontext'],'<table') !== false){
                    $typevideo = "{$CFG->wwwroot}/local/designer/qtypevideos/Vidéo_table.mp4";
                } else {
                    $subquestiontypes = array_column($question['subQuestion'], 'type');
                    if(in_array("dropdown", $subquestiontypes)){
                        $typevideo = "{$CFG->wwwroot}/local/designer/qtypevideos/Video_value_drop down.mp4";
                    } else if(in_array("multichoice", $subquestiontypes) || in_array("multichoiceh", $subquestiontypes) || in_array("multichoicev", $subquestiontypes)){
                        $typevideo = "{$CFG->wwwroot}/local/designer/qtypevideos/Video_single choice_value.mp4";
                    } else {
                        $typevideo = "{$CFG->wwwroot}/local/designer/qtypevideos/Vidéo_value.mp4";
                    }
                }
                break;
            default:
                break;
        }
        return $typevideo;
    }
    public function initialsetupuser($userid){
        global $USER, $DB, $CFG;
        $moduleid = 11719;
        $courseid = 62;
        $currentyeadending = strtotime("30 June ".date("Y"));
        if($currentyeadending < time()){
            $currentyeadending = strtotime("30 June ".date("Y", strtotime("+1 year")));
        }
        if($currentChild = $DB->get_record("childusers", array("userid" => $userid))){
            require_once($CFG->dirroot."/mod/quiz/classes/external.php");
            require_once($CFG->dirroot.'/mod/quiz/locallib.php');
            require_once($CFG->dirroot.'/mod/quiz/lib.php');
            self::enrolCourse($courseid, $userid, "self", $currentyeadending);
            self::enrolluserincourses($currentChild->id, $currentChild->grade, "self", $currentyeadending);
            $forcenew = false;
            $timenow = time();
            $cm = get_coursemodule_from_id('quiz', $moduleid);
            $quizobj = quiz::create($cm->instance, $userid);
            $accessmanager = $quizobj->get_access_manager($timenow);
            list($currentattemptid, $attemptnumber, $lastattempt, $messages, $page) = quiz_validate_new_attempt($quizobj, $accessmanager, $forcenew, -1, false);
            if ($currentattemptid) {
                $localattempt = $DB->get_record("quiz_attempts", array("id"=>$currentattemptid));
            } else {
                $localattempt = quiz_prepare_and_start_new_attempt($quizobj, $attemptnumber, $lastattempt);
            }
            $QuizEX = new mod_quiz_external();
            $data = $QuizEX->process_attempt($localattempt->id, array(), 1, 0, array());
            print_r($data); 
        }
    }
}