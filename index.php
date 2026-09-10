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
 * momentoenroll conection plugin settings form.
 *
 * @package    enrol_momentoenroll
 * @author     Juan Camilo Marthá Piñeros
 * @copyright  2022 Edu Labs {@link https://edu-labs.co}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG, $OUTPUT;

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot."/enrol/momentoenroll/locallib.php");
require_once($CFG->dirroot."/enrol/momentoenroll/authentication_settings_form.php");
include($CFG->dirroot."/enrol/momentoenroll/menu.php");

$PAGE->set_context(\context_system::instance());
$PAGE->set_url($CFG->wwwroot ."/enrol/momentoenroll/index.php");
$PAGE->set_heading(get_string('pluginname', 'enrol_momentoenroll'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Autenticación');

require_login();

// if not side admin the die
//if (!is_siteadmin){die();}

validate_settings_exists();

$action = optional_param('action', '', PARAM_RAW);
$updated = false;

if ($action == 'update'){
    update_settings();
    $updated = true;
    
    require_once($CFG->libdir.'/clilib.php');
    $options = array("muc"=>1);
    purge_caches(array_filter($options));
}

$mform1 = new authentication_settings_form(null, array());
//admin_externalpage_setup('momentoenroll');
echo $OUTPUT->header();
echo $OUTPUT->heading('Autenticación', 3);
echo html_writer::tag('p', get_string('pluginname_desc', 'enrol_momentoenroll'));

//Menú
echo $items;

echo $updated ? html_writer::tag('div', 'La configuración se ha actualizado correctamente.', array('class' => 'alert alert-success')) : '';

$mform1->display();
echo $OUTPUT->footer();