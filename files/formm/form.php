<?php
require_once('../config.php');
global $DB, $CFG, $USER;
require_once($CFG->libdir.'/formslib.php');
class narationsetting_form extends moodleform {
    function definition() {
        global $CFG,$DB,$USER, $TEXTAREA_OPTIONS;
        $mform = $this->_form;
        $mform->addElement('header', 'formheader', "Employees Detail");
        $mform->addElement('text', 'employee_name', 'Employee Name:', 'maxlength="250" size="50"');
        
        $mform->addElement('text', 'employee_email', 'Employee Email:', 'maxlength="254" size="50"');
        
        $mform->addElement('text', 'employee_contact', 'Employee Contact:', 'maxlength="254" size="50"pattern="[0-9]+$"');
        
        $mform->addElement('text', 'employee_address', 'Employee Address:',  'maxlength="254" size="50"');
        
        $mform->addElement('text', 'employee_sal', 'Employee Salary:', 'maxlength="254" size="50" pattern="[0-9]+$"');

        // $mform->addElement('filemanager', 'file', 'Login Page Image:', null,
        // array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));  

        $mform->addElement('hidden', 'id');
        $this->add_action_buttons();
        // $buttonarray[] = $mform->createElement('submit', 'submitbutton', "Save");
        // $buttonarray[] =& $mform->createElement('submit', 'cancel', "Back");
        // $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
    public function validation($data, $files) {
        global $DB;
        $errors = array();
        return $errors;
    }
    public function displayreport(){   
        global $DB, $CFG;
     $alluser =$DB->get_records('employee', array ()); //ok
    ?>
     <table class="table">
        <tr>
            <th>id</th>
            <th>employee_name</th>
            <th>employee_email</th>
            <th>employee_contact</th>
            <th>employee_address</th>
            <th>employee_sal</th>
              <th>Activity</th>
              <th>Activity</th>
        </tr>
<?php
foreach ($alluser as $key => $user) {

?>
        <tr>
            <td>  <?php echo $user->id."<br>" ; ?></td>
            <td><?php echo $user->employee_name."<br>" ; ?></td>
            <td><?php echo $user->employee_email."<br>" ; ?></td>
            <td><?php echo $user->employee_contact."<br>" ; ?></td>
            <td><?php echo $user->employee_address."<br>" ; ?></td>
            <td><?php echo $user->employee_sal."<br>" ; ?></td>
            <td><?php echo "<a href=\"".$CFG->wwwroot."/formm/inform.php?id=".$user->id."\">Update</a>" ; ?></td>
            <td><?php echo "<a href=\"".$CFG->wwwroot."/formm/inform.php?delete=".$user->id."\">Delete</a>" ; ?></td>
            <!-- <a href=\"http://175.111.182.37/latestmoodle/formm/inform.php?id=".$user->id."\">Update</a> -->
        </tr> 
     <?php
}
?>
     </table>
     <?php
    }
}
