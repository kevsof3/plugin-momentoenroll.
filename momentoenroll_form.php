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
 * Momento enroll plugin settings form.
 *
 * @package    enrol_momentoenroll
 * @author     Juan Camilo Marthá Piñeros
 * @copyright  2022 Edu Labs {@link https://edu-labs.co}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require_once($CFG->libdir . "/formslib.php");

 class momento_enroll_form extends moodleform {

    function definition(){
        global $CFG, $DB;
        $config = get_config('enrol_momentoenroll');

        $mform = $this->_form;
        
        //Enable this plugin
        
        $mform->addElement('header', 'enrol_momentoenroll_enabledtitle', get_string('enabletitle', 'enrol_momentoenroll'));
        $mform->addElement('checkbox', 'enabled', get_string('enabled', 'enrol_momentoenroll'));
        $mform->setDefault('enabled', $config->enabled);

        // Licence endpoint and Key
        
        $mform->addElement('header', 'enrol_momentoenroll_licensetitle', get_string('licensetitle', 'enrol_momentoenroll'));
        $mform->setExpanded('enrol_momentoenroll_licensetitle');

        $mform->addElement('text', 'endpointlicense', get_string('endpointlicense', 'enrol_momentoenroll'));
        $mform->setType('endpointlicense', PARAM_TEXT);
        $mform->setDefault('endpointlicense', $config->endpointlicense);

        $mform->addElement('textarea', 'license', get_string('license', 'enrol_momentoenroll'));
        $mform->setDefault('license', $config->license);

        // Rest connection
        
        $mform->addElement('header', 'enrol_momentoenroll_restconnectiontitle', get_string('restconnectiontitle', 'enrol_momentoenroll'));
        $mform->setExpanded('enrol_momentoenroll_restconnectiontitle');

        $options = array('1' => 'POST', '2' => 'HEADERS');
        $mform->addElement('select', 'restmethod', get_string('restmethod', 'enrol_momentoenroll'), $options);
        $mform->setDefault('restmethod', $config->restmethod);

        $options = array('1' => get_string('token', 'enrol_momentoenroll'), '2' => get_string('userpass', 'enrol_momentoenroll'));
        $mform->addElement('select', 'restauth', get_string('restauth', 'enrol_momentoenroll'), $options);
        $mform->setDefault('restauth', $config->restauth);

        $mform->addElement('text', 'token', get_string('token', 'enrol_momentoenroll'));
        $mform->setType('token', PARAM_TEXT);
        $mform->hideIf('token', 'restauth', 'eq', '2');
        $mform->setDefault('token', $config->token);

        $mform->addElement('text', 'username', get_string('username', 'enrol_momentoenroll'));
        $mform->setType('username', PARAM_TEXT);
        $mform->hideIf('username', 'restauth', 'eq', '1');
        $mform->setDefault('username', $config->username);

        $mform->addElement('passwordunmask', 'pass', get_string('pass', 'enrol_momentoenroll'));
        $mform->hideIf('pass', 'restauth', 'eq', '1');
        $mform->setDefault('pass', $config->pass);

        // Sinchronization

        $options = array(1=>get_string('only_create', 'enrol_momentoenroll'),2=>get_string('create_update', 'enrol_momentoenroll'),3=>get_string('only_update', 'enrol_momentoenroll'));

        // Users sinchronization

        $mform->addElement('header', 'enrol_momentoenroll_endpointuserstitle', get_string('endpointuserstitle', 'enrol_momentoenroll'));
        $mform->setExpanded('enrol_momentoenroll_endpointuserstitle');

        $mform->addElement('checkbox', 'usersenabled', get_string('usersenabled', 'enrol_momentoenroll'));
        $mform->setDefault('usersenabled', $config->usersenabled);
        
        $mform->addElement('select', 'syncusers', get_string('syncusers', 'enrol_momentoenroll'), $options);
        $mform->setDefault('syncusers', $config->syncusers);

        $mform->addElement('text', 'endopointusers', get_string('endopointusers', 'enrol_momentoenroll'));
        $mform->setType('endopointusers', PARAM_TEXT);
        $mform->setDefault('endopointusers', $config->endopointusers);

        $options_login = array();
        $options_login += core_plugin_manager::standard_plugins_list('auth');
        $mform->addElement('select', 'loginusers', get_string('loginusers', 'enrol_momentoenroll'), $options_login);
        $mform->hideIf('pass', 'restauth', 'eq', '1');
        $mform->setDefault('loginusers', $config->loginusers);

        // Courses sinchronization

        $mform->addElement('header', 'endpointcoursestitle', get_string('endpointcoursestitle', 'enrol_momentoenroll'));
        $mform->setExpanded('endpointcoursestitle');

        $mform->addElement('checkbox', 'coursesenabled', get_string('coursesenabled', 'enrol_momentoenroll'));
        $mform->setDefault('coursesenabled', $config->coursesenabled);
        
        $mform->addElement('select', 'synccourses', get_string('synccourses', 'enrol_momentoenroll'), $options);
        $mform->setDefault('synccourses', $config->synccourses);

        $mform->addElement('text', 'endopointcourses', get_string('endopointcourses', 'enrol_momentoenroll'));
        $mform->setType('endopointcourses', PARAM_TEXT);
        $mform->setDefault('endopointcourses', $config->endopointcourses);

        $categories = array();
        $categories += core_course_category::make_categories_list('moodle/category:manage');
        $mform->addElement('select', 'defaultcategory', get_string('defaultcategories', 'enrol_momentoenroll'), $categories);
        $mform->setDefault('defaultcategory', $config->defaultcategory);

        // Enrolments sinchronization

        $mform->addElement('header', 'endpointenrolmentstitle', get_string('endpointenrolmentstitle', 'enrol_momentoenroll'));
        $mform->setExpanded('endpointenrolmentstitle');

        $mform->addElement('checkbox', 'enrolmentsenabled', get_string('enrolmentsenabled', 'enrol_momentoenroll'));
        $mform->setDefault('enrolmentsenabled', $config->enrolmentsenabled);
        
        $mform->addElement('select', 'syncenrolments', get_string('syncenrolments', 'enrol_momentoenroll'), $options);
        $mform->setDefault('syncenrolments', $config->syncenrolments);

        $mform->addElement('text', 'endopointenrolments', get_string('endopointenrolments', 'enrol_momentoenroll'));
        $mform->setType('endopointenrolments', PARAM_TEXT);
        $mform->setDefault('endopointenrolments', $config->endopointenrolments);

        // Action form

        $mform->addElement('hidden', 'section', 'enrolsettingsmomentoenroll'); 
        $mform->setType('section', PARAM_RAW);
        
        $mform->addElement('hidden', 'action', 'update'); 
        $mform->setType('action', PARAM_RAW);

        // Submit button

        //$mform->addElement('submit', 'intro', 'Enviar');
        $this->add_action_buttons($cancel = false, $submitlabel='Guardar');
    }
 }