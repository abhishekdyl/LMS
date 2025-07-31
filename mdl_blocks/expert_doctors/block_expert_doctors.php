<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course summary block
 *
 * @package    block_expert_doctors
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_expert_doctors extends block_base {

    /**
     * @var bool Flag to indicate whether the header should be hidden or not.
     */
    private $headerhidden = true;

    function init() {
        $this->title = get_string('pluginname', 'block_expert_doctors');
    }
    
    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'tag' => false, 'my' => false);
    }

    public function instance_config_save($data, $nolongerused = false) {
        if (isset($data->config_userselect1) && is_array($data->config_userselect1)) {
            $data->config_userselect1 = json_encode($data->config_userselect1);
        }

        if (isset($data->config_userselect2) && is_array($data->config_userselect2)) {
            $data->config_userselect2 = json_encode($data->config_userselect2);
        }

        return parent::instance_config_save($data, $nolongerused);
    }
    

    function get_content() {
        global $DB, $CFG,$PAGE ,$OUTPUT;
        require_once($CFG->dirroot . "/course/externallib.php");

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;

        // Get config values safely.
        $title = !empty($this->config->title) ? format_string($this->config->title) : 'Default Title';
        // $text = !empty($this->config->text) ? format_text($this->config->text) : '';
        if (!empty($this->config->text) && is_array($this->config->text)) {
            $text = format_text($this->config->text['text'], $this->config->text['format'] ?? FORMAT_HTML);
        } elseif (!empty($this->config->text) && is_string($this->config->text)) {
            $text = format_text($this->config->text, FORMAT_HTML);
        } else {
            $text = '';
        }

        $classes = !empty($this->config->classes) ? $this->config->classes : '';

        // Handle multi-select fields.
        $userselect1 = [];
        if (!empty($this->config->userselect1)) {
            $userselect1 = is_string($this->config->userselect1)
                ? json_decode($this->config->userselect1, true)
                : $this->config->userselect1;
        }

        $userselect2 = [];
        if (!empty($this->config->userselect2)) {
            $userselect2 = is_string($this->config->userselect2)
                ? json_decode($this->config->userselect2, true)
                : $this->config->userselect2;
        }

        $userids = implode(',',$userselect1);
        $doctors = $DB->get_records_sql("SELECT * FROM {user} WHERE id IN(".$userids.") limit 2");
        $userids2 = implode(',',$userselect2);
        $nutritionist = $DB->get_records_sql("SELECT * FROM {user} WHERE id IN(".$userids2.") limit 3");

        // $newarr = [
        //     'title1' => $title,        
        //     'text1' => $text,        
        //     'classes1' => $classes,        
        //     'userselect1' => $userselect1,        
        //     'doctors' => $doctors,        
        //     'userselect2' => $userselect2,    
        //     'nutritionist' => $nutritionist,        
        // ];    
        // $html='<pre>'.print_r($newarr,true).'</pre>';
        
        $html='';


        $html .=' 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>

        ul.tgsdb-links.tgsdb-quicklinks li a{
            text-decoration: none;
        }

        body, .container {
            background-color: #dcebf2;
            font-family: "Segoe UI", sans-serif;
        }

        .section-title {
            font-weight: 500;
            font-size: 3.35rem;
            color: #0d3b5b;
        }

        .section-subtext {
            color: #0d3b5b !important;
            font-size: 1.1rem !important;
            max-width: 450px;
        }

        .btn-custom:hover {
            background-color: #06314e;
            color: white;
        }
        .btn-custom {
            background-color: #0d3b5b;
            color: white;
            border-radius: 30px;
            padding: 16px 37px;
            font-size: 1.20rem;
        }

        .badge-custom {
            background-color: #0c7cc0;
            color: white;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 1.00rem;
            font-weight: 600;
            margin-bottom: 10px;
            display: inline-block;
        }

        .card-custom {
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: none;
        }

        .card-img-top {
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
        }

        .card-body p {
            font-size: 0.85rem;
            color: #555;
        }

        .read-more {
            font-weight: 600;
            font-size: 0.85rem;
            color: #0c7cc0;
        }

        .card-hd{
            padding: 12px;
        }

        .modelhead{
            display: flex;
        }
        .modelhead .img img{
            margin: 10px 25px;
            border-radius: 95px;
            width: 115px;
        }
        .modelhead .name{
            margin: auto;
            margin-left: 5px;
            color: #0c3552;
        }
        .modeldesc{
            padding: 25px;
        }

        .modal-content .btn-close{
            padding-top: 23px;
            padding-right: 25px;
        }
        
        @media (max-width: 446px) {
            .section-title {
                font-size: 2.35rem;
            }
        }
        
        @media (max-width: 766px) {
            .contsection p{
                margin:auto;
            }
            .contsection {
                margin-bottom: 30px;
                text-align: center;
            }
        }

        @media (min-width: 1400px) {
            .container.customblock {
                max-width: 1230px !important;
            }
        }

    </style>


    <div class="container customblock py-5">
        <div class="row align-items-center mb-4">
            <div class="col-md-5 contsection">
                <h2 class="section-title">'.$title.'</h2>
                <p class="section-subtext mt-3">'.$text.'</p>
                <a href="/moodle/blocks/expert_doctors/index.php"><button class="btn btn-custom mt-4">View all experts</button></a>
            </div>

            <div class="col-md-7">
            <div class="mb-2">
                <span class="badge-custom">Doctors</span>
            </div>
            <div class="row g-3">';

            foreach ($doctors as $key => $doctor) {
                $userpicture = new user_picture($doctor);
                $userpicture->size = 1; // Size f1.
                $img = $userpicture->get_url($PAGE)->out(false);
                $plain_desc = strip_tags($doctor->description);
                $plain_text = html_entity_decode($plain_desc);
                $max_length = 50;
                $short_text = mb_substr($plain_text, 0, $max_length);
                if (mb_strlen($plain_text) > $max_length) {
                    $short_text .= '... <span class="read-more">Read more</span>';
                }

        $html .='<div class="col-md-6" data-bs-toggle="modal" data-bs-target="#myModaldoctor'.$doctor->id.'">
                <div class="card card-custom">
                    <div class="card-hd">
                        <img src="'.$img.'" class="card-img-top" alt="Doctor">
                    </div>
                    <div class="card-body">
                    <h6 class="card-title mb-1"><strong>Dr. '.$doctor->firstname.' '.$doctor->lastname.'</strong></h6>
                    <p> '.$short_text.'</p>
                    </div>
                </div>
                </div>';
            
            }

        $html .='
            </div>

            <div class="mt-4 mb-2">
                <span class="badge-custom">Nutritionist</span>
            </div>
            <div class="row g-3">';

            foreach ($nutritionist as $key => $nutrit) {
                $userpicture = new user_picture($nutrit);
                $userpicture->size = 1; // Size f1.
                $img = $userpicture->get_url($PAGE)->out(false);
                $plain_desc = strip_tags($nutrit->description);
                $plain_text = html_entity_decode($plain_desc);
                $max_length = 30;
                $short_text = mb_substr($plain_text, 0, $max_length);
                if (mb_strlen($plain_text) > $max_length) {
                    $short_text .= '... <span class="read-more">Read more</span>';
                }

        $html .='<div class="col-md-4" data-bs-toggle="modal" data-bs-target="#myModalnutrit'.$nutrit->id.'">
                <div class="card card-custom h-100">
                    <div class="card-hd">    
                        <img src="'.$img.'" class="card-img-top" alt="Nutritionist">
                    </div>
                    <div class="card-body">
                    <h6 class="card-title mb-1"><strong>'.$nutrit->firstname.' '.$nutrit->lastname.'</strong></h6>
                    <p>'.$short_text.'</p>
                    </div>
                </div>
                </div>';
            }

        $html .='
            </div>
            </div>
        </div>
    </div>';
            
        foreach ($nutritionist as $key => $nutrit) {
            $plain_desc = strip_tags($nutrit->description);
            $userpicture = new user_picture($nutrit);
            $userpicture->size = 1; // Size f1.
            $img = $userpicture->get_url($PAGE)->out(false);
    
            $html .=' 
            <!-- The Modal -->
            <div class="modal" id="myModalnutrit'.$nutrit->id.'">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    <div class="modelhead">
                        <div class="img">
                            <img src="'.$img.'" class="card-img-top" alt="Nutritionist">
                            </div>
                            <div class="name">
                                <h4 class="card-title mb-1"><strong>'.$nutrit->firstname.' '.$nutrit->lastname.'</strong></h4>
                            </div>
                    </div>
                    <div class="modeldesc">'.$plain_desc.'</div>

                    </div>
                </div>
            </div>';
        }

        foreach ($doctors as $key => $doctor) {
            $plain_desc = strip_tags($doctor->description);
            $userpicture = new user_picture($doctor);
            $userpicture->size = 1; // Size f1.
            $img = $userpicture->get_url($PAGE)->out(false);
        
            $html .=' 
            <!-- The Modal -->
            <div class="modal" id="myModaldoctor'.$doctor->id.'">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    <div class="modelhead">
                        <div class="img">
                            <img src="'.$img.'" class="card-img-top" alt="Nutritionist">
                            </div>
                            <div class="name">
                                <h4 class="card-title mb-1"><strong>Dr. '.$doctor->firstname.' '.$doctor->lastname.'</strong></h4>
                            </div>
                    </div>
                    <div class="modeldesc">'.$plain_desc.'</div>

                    </div>
                </div>
            </div>';
        }
        
        $this->content->text = $html;
        // $this->content->text = 'block_expert_doctors custom block';

        return $this->content;
    }
}


