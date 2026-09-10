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

class authentication_settings_form extends moodleform {

    function definition(){
        global $CFG, $DB;
        $config = get_config('enrol_momentoenroll');

        $mform = $this->_form;

        // Rest connection
        
        $mform->addElement('header', 'enrol_momentoenroll_authenticationtitle', get_string('authenticationtitle', 'enrol_momentoenroll'));
        $mform->setExpanded('enrol_momentoenroll_authenticationtitle');

        $options = array('0' => 'Sin autenticación', '1' => 'POST', '2' => 'HEADERS');
        $mform->addElement('select', 'restmethod', get_string('restmethod', 'enrol_momentoenroll'), $options);
        $mform->setDefault('restmethod', $config->restmethod);

        $options = array('1' => get_string('token', 'enrol_momentoenroll'), '2' => get_string('userpass', 'enrol_momentoenroll'));
        $mform->addElement('select', 'restauth', get_string('restauth', 'enrol_momentoenroll'), $options);
        $mform->hideIf('restauth', 'restmethod', 'eq', '0');
        $mform->setDefault('restauth', $config->restauth);

        $mform->addElement('text', 'token', get_string('token', 'enrol_momentoenroll'));
        $mform->setType('token', PARAM_TEXT);
        $mform->hideIf('token', 'restmethod', 'eq', '0');
        $mform->hideIf('token', 'restauth', 'eq', '2');
        $mform->setDefault('token', $config->token);

        $mform->addElement('text', 'username', get_string('username', 'enrol_momentoenroll'));
        $mform->setType('username', PARAM_TEXT);
        $mform->hideIf('username', 'restmethod', 'eq', '0');
        $mform->hideIf('username', 'restauth', 'eq', '1');
        $mform->setDefault('username', $config->username);

        $mform->addElement('passwordunmask', 'pass', get_string('pass', 'enrol_momentoenroll'));
        $mform->hideIf('pass', 'restmethod', 'eq', '0');
        $mform->hideIf('pass', 'restauth', 'eq', '1');
        $mform->setDefault('pass', $config->pass);
        
        // Endpoint sinchronization
        /*
        $mform->addElement('text', 'endpoint', get_string('endpoint', 'enrol_momentoenroll'));
        $mform->setType('endpoint', PARAM_TEXT);
        $mform->setDefault('endpoint', $config->endpoint);
        */
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