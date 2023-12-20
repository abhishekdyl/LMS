<?php
require_once('../../config.php');
global $DB, $CFG, $USER;
require_once("$CFG->libdir/formslib.php");

class events_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $USER, $OUTPUT;
        $mform = $this->_form;
        $newevent = $this->_customdata->eventtype; 

        $mform->addElement('header', 'general', get_string('general'));


        $mform->addElement('text', 'name', get_string('eventname','calendar'), 'size="50"');
        $mform->addRule('name', get_string('required'), 'required');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('date_time_selector', 'timestart', get_string('date'));
        $mform->addRule('timestart', get_string('required'), 'required');

        if($newevent){

            $options = array();
            if(!empty($newevent->users)){
                $options['user'] = get_string('user');
            }
            if(!empty($newevent->groups)){
                $options['group'] = get_string('group');
            }
            $options['course'] = get_string('course');
            $options['site'] = get_string('site');
    

            $mform->addElement('select', 'eventtype', get_string('eventkind', 'calendar'), $options);
            $mform->addRule('eventtype', get_string('required'), 'required');

            $useroptions = array();
            foreach ($newevent->users as $userss) {
                $useroptions[$userss->id] = $userss->firstname." ".$userss->lastname."  ".$userss->email;
            }
            $mform->addElement('select', 'userid', " User ", $useroptions);
            $mform->hideIf('userid', 'eventtype', 'noteq', 'user');
            
            $groupoptions = array();
            foreach ($newevent->groups as $group) {
                $groupoptions[$group->id] = $group->name ;
            }
            $mform->addElement('select', 'groupid', get_string('typegroup', 'calendar'), $groupoptions);
            $mform->hideIf('groupid', 'eventtype', 'noteq', 'group');

        }


        $mform->addElement('editor', 'description', get_string('eventdescription','calendar'), null, $this->_customdata->event->editoroptions);
        $mform->setType('description', PARAM_RAW);

        $mform->addElement('text', 'location', "Location", 'size="50"');
        $mform->setType('location', PARAM_TEXT);


        $mform->addElement('header', 'durationdetails', get_string('eventduration', 'calendar'));

        $group = array();
        $group[] =& $mform->createElement('radio', 'duration', null, get_string('durationnone', 'calendar'), 0);
        $group[] =& $mform->createElement('radio', 'duration', null, get_string('durationuntil', 'calendar'), 1);
        $group[] =& $mform->createElement('date_time_selector', 'timedurationuntil', '');
        $group[] =& $mform->createElement('radio', 'duration', null, get_string('durationminutes', 'calendar'), 2);
        $group[] =& $mform->createElement('text', 'timedurationminutes', get_string('durationminutes', 'calendar'));

        $mform->addGroup($group, 'durationgroup', '', '<br />', false);

        $mform->disabledIf('timedurationuntil',         'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[day]',    'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[month]',  'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[year]',   'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[hour]',   'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[minute]', 'duration', 'noteq', 1);

        $mform->setType('timedurationminutes', PARAM_INT);
        $mform->disabledIf('timedurationminutes','duration','noteq', 2);

        $mform->setDefault('duration', 0);


        if ($newevent) {

            $mform->addElement('header', 'repeatevents', get_string('repeatedevents', 'calendar'));
            $mform->addElement('checkbox', 'repeat', get_string('repeatevent', 'calendar'), null);
            $mform->addElement('text', 'repeats', get_string('repeatweeksl', 'calendar'), 'maxlength="10" size="10"');
            $mform->setType('repeats', PARAM_INT);
            $mform->setDefault('repeats', 1);
            $mform->disabledIf('repeats','repeat','notchecked');

        } else if ($repeatedevents) {

            $mform->addElement('hidden', 'repeatid');
            $mform->setType('repeatid', PARAM_INT);

            $mform->addElement('header', 'repeatedevents', get_string('repeatedevents', 'calendar'));
            $mform->addElement('radio', 'repeateditall', null, get_string('repeateditall', 'calendar', $this->_customdata->event->eventrepeats), 1);
            $mform->addElement('radio', 'repeateditall', null, get_string('repeateditthis', 'calendar'), 0);

            $mform->setDefault('repeateditall', 1);

        }

        $this->add_action_buttons(true, get_string('savechanges'));

 
    }
    //Custom validation should be added here
    function validation($data, $files) {
        global $DB, $CFG;
        
        $errors = parent::validation($data, $files);

        if ($data['duration'] == 1 && $data['timestart'] > $data['timedurationuntil']) {
            $errors['timedurationuntil'] = get_string('invalidtimedurationuntil', 'calendar');
        } else if ($data['duration'] == 2 && (trim($data['timedurationminutes']) == '' || $data['timedurationminutes'] < 1)) {
            $errors['timedurationminutes'] = get_string('invalidtimedurationminutes', 'calendar');
        }

        return $errors;
    }
}


