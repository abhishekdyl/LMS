<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/edit_form.php');

class block_expert_doctors_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG, $DB;

        // Header.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Title.
        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_expert_doctors'));
        $mform->setType('config_title', PARAM_TEXT);

        // Editor for description.
        $editoroptions = [
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => true,
            'context' => $this->block->context
        ];
        $mform->addElement('textarea', 'config_text', get_string('configcontent', 'block_expert_doctors'), null, $editoroptions);
        $mform->addRule('config_text', null, 'required', null, 'client');
        $mform->setType('config_text', PARAM_RAW);

        // Optional CSS classes.
        if (!empty($CFG->block_expert_doctors_allowcssclasses)) {
            $mform->addElement('text', 'config_classes', get_string('configclasses', 'block_expert_doctors'));
            $mform->setType('config_classes', PARAM_TEXT);
            $mform->addHelpButton('config_classes', 'configclasses', 'block_expert_doctors');
        }

        // Custom user multi-selects.
        $users = $DB->get_records_select('user', 'deleted = 0 AND confirmed = 1 AND suspended = 0 AND id != 1', null, 'lastname ASC');
        $useroptions = [];
        foreach ($users as $user) {
            $useroptions[$user->id] = fullname($user) . " ({$user->email})";
        }

        $mform->addElement('autocomplete', 'config_userselect1', get_string('userselect1', 'block_expert_doctors'), $useroptions);
        $mform->getElement('config_userselect1')->setMultiple(true);
        $mform->addHelpButton('config_userselect1', 'userselect1', 'block_expert_doctors');

        $mform->addElement('autocomplete', 'config_userselect2', get_string('userselect2', 'block_expert_doctors'), $useroptions);
        $mform->getElement('config_userselect2')->setMultiple(true);
        $mform->addHelpButton('config_userselect2', 'userselect2', 'block_expert_doctors');
    }

    function set_data($defaults) {
        if (!empty($this->block->config->userselect1)) {
            $defaults->config_userselect1 = is_string($this->block->config->userselect1)
                ? json_decode($this->block->config->userselect1, true)
                : $this->block->config->userselect1;
        }

        if (!empty($this->block->config->userselect2)) {
            $defaults->config_userselect2 = is_string($this->block->config->userselect2)
                ? json_decode($this->block->config->userselect2, true)
                : $this->block->config->userselect2;
        }

        parent::set_data($defaults);
    }


    public static function display_form_when_adding(): bool {
        return true;
    }
}
