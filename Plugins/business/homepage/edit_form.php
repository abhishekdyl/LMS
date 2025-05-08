<?php
require_once('../../../config.php');
require_once("$CFG->libdir/formslib.php");
class homepage_form extends moodleform {
    function definition() {
        global $CFG, $DB, $PAGE, $USER; 
        $userbrand        = $this->_customdata['userbrand']; // this contains the data of this form
        $sql="SELECT * FROM {business_learning_homapage} WHERE cbid=?";
        $olddata=$DB->get_record_sql($sql,array($userbrand->cbid));
        $settingdata=array();
        $introductiontext="";
        if($olddata){
            $settingdata = json_decode($olddata->settingdata);
        }
        $mform = $this->_form; 
        $mform->addElement('html', '<h2>Edit your Home page</h2>');

        $mform->addElement('filemanager', 'bannerimage', 'Header banner image<br/><span class="smalltxt">The header banner image is Home <br>optional. It appears at the top of <br>your private eLearning Home Page</span>', null,array('subdirs' => 0, 'maxbytes' => 50000000, 'areamaxbytes' => 107374182400, 'maxfiles' => 1,'accepted_types' => array('.jpg','.jpeg','.png','.gif'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL));
        $mform->addElement('editor', 'introductiontext', 'Introduction text<br/><span class="smalltxt">Provide a description that instructs <br>your staff on how to use the <br>learning resources and what is <br>expected of them.</span>', array("enable_filemanagement"=>false, 'autosave' => false));
        $mform->setType('introductiontext', PARAM_RAW);
        $mform->setDefault('introductiontext', $introductiontext);

        $htmlbox = '<div class="graybox1">';
        for ($k=0; $k < 3; $k++) { 
            $sdata = $settingdata[$k];
            $htmlbox .= '
            <div class="graybox">
                <h3>Quick Links Column # '.($k+1).'</h3>
                <div class="row">
                    <div class="span1">
                    <strong>Header</strong>
                    </div>
                    <div class="span6">
                        <input name="qlinkinput'.$k.'heading" type="text" value="'.(is_object($sdata)?$sdata->heading:'').'" class="w100"/><br>
                        <small>40 character maximum</small>
                    </div>
                </div>';
            $htmlbox .= '<div class="row"><div class="span4"><strong>Link Name</strong></div><div class="span7"><strong>Destination URL</strong></div></div>';
            $linknames = $sdata->linkname;
            $linkurls = $sdata->url;
            for ($i=0; $i < 10; $i++) { 
                $htmlbox .= '<div class="row"><div class="span4"><input type="text" class="w100" name="qlinkinput'.$k.'name['.$i.']" value="'.(isset($linknames[$i])?$linknames[$i]:'').'" /><br><br></div><div class="span7"><input type="text" class="w100" name="qlinkinput'.$k.'link['.$i.']" value="'.(isset($linkurls[$i])?$linkurls[$i]:'').'" /><br><br></div></div>';
            }
            $htmlbox .= '</div>';
        }
        $htmlbox .= '</div>';
        $mform->addElement('static', 'description', '<label class="col-form-label d-inline " for="id_title1">Quick links</label><br/><span class="smalltxt">Create up to three quick link menus <br>that provide convenient navigation <br>to static learning assets. <br>If you leave the Header empty, the <br>column will not display</span>', $htmlbox);

        $mform->addElement('hidden', 'id');
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', "Save");
        $buttonarray[] = $mform->createElement('cancel', 'cancel',"Cancel");
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false); 
        
    }  

    /**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    function validation($data, $files) {
        global $DB, $USER;
        $errors = array();
        $userbrand = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
        $branding = $DB->get_record('custom_branding', array("id"=>$userbrand->cbid));

        return $errors;
    }
    function display_allprograms(){
        global $DB, $CFG;
        $userbrand        = $this->_customdata['userbrand'];
        $branding        = $this->_customdata['branding'];
        $html='';
        $programs = $DB->get_records("business_learning_program", array("cbid"=>$branding->id));
        $html .= '<table class="table">';
        $html .= '<tr>';
            $html .= '<th> ID </th>';
            $html .= '<th> Program Name </th>';
            $html .= '<th> Copy link </th>';
            $html .= '<th> Stream1 Title </th>';
            $html .= '<th> Stream2 Title </th>';
            $html .= '<th> Stream3 Title </th>';
            $html .= '<th>Action</th>';
        $html .= '</tr>';
        if(sizeof($programs)>0){
            foreach ($programs as $key => $program) {
                $html .= '<tr>';
                    $html .= '<td>'.$program->id.'</td>';
                    $html .= '<td><a href="'.$CFG->wwwroot.'/local/business/learning_program.php?id='.$program->id.'">'.$program->program_name.'</a></td>';
                    $html .= '<td><a onclick="copytoclipboard(\''.$CFG->wwwroot.'/local/business/learning_program.php?id='.$program->id.'\')" href="javascript:void(0);" class="btn">Copy link</a></td>';
                    $html .= '<td>'.$program->stream1title.'</td>';
                    $html .= '<td>'.$program->stream2title.'</td>';
                    $html .= '<td>'.$program->stream3title.'</td>';
                    $html .= '<td><a href="'.$CFG->wwwroot.'/local/business/learning_program/edit.php?id='.$program->id.'">Edit</a></td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr>';
                $html .= '<td colspan="6">No course Available yet</td>';
            $html .= '</tr>';
        }
        $html .= '</table><script>function copytoclipboard(link) {
            navigator.clipboard.writeText(link);
          }
          </script>';
        echo $html;
    }
}    