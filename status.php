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
 * Momento enrolment plugin status.
 *
 * @package    enrol_momentoenroll
 * @author     Juan Camilo Marthá Piñeros
 * @copyright  2022 Edu Labs {@link https://edu-labs.co}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


global $OUTPUT;

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot."/enrol/momentoenroll/locallib.php");
include($CFG->dirroot."/enrol/momentoenroll/menu.php");

$PAGE->set_context(\context_system::instance());
$PAGE->set_url($CFG->wwwroot ."/enrol/momentoenroll/status.php");
$PAGE->set_heading(get_string('pluginname', 'enrol_momentoenroll'));
$PAGE->set_title('Estado');
$PAGE->requires->js(new moodle_url('/enrol/momentoenroll/amd/angular.min.js'));
$PAGE->requires->js(new moodle_url('/enrol/momentoenroll/amd/controller.js'));
require_login();

//admin_externalpage_setup('momentoenroll');
echo $OUTPUT->header();
echo $OUTPUT->heading('Estado', 3);
echo html_writer::tag('p', get_string('pluginname_desc', 'enrol_momentoenroll'));
echo html_writer::empty_tag('hr');

//Menú
echo $items;

if ($config->enabled == ACTIVE){
    
    echo html_writer::tag('h4', get_string('endopointusers', 'enrol_momentoenroll'));
    if ($config->usersenabled == ACTIVE){
        if (test_webservice_endpoint($config->endopointusers) == CONNECTION_OK){
            echo html_writer::tag('div', get_string('status_ok', 'enrol_momentoenroll') . ' ' . $config->endopointusers, array('class' => 'alert alert-success'));
        } else {
            echo html_writer::tag('div', get_string('status_error', 'enrol_momentoenroll') . ' ' . $config->endopointusers, array('class' => 'alert alert-danger'));
        }
    } else {
        echo html_writer::tag('p', get_string('sync_disabled', 'enrol_momentoenroll'));
    }
    echo html_writer::empty_tag('hr');
    
    echo html_writer::tag('h4', get_string('endopointcategories', 'enrol_momentoenroll'));
    if ($config->categoriesenabled == ACTIVE){
        if (test_webservice_endpoint($config->endopointcategories) == CONNECTION_OK){
            echo html_writer::tag('div', get_string('status_ok', 'enrol_momentoenroll') . ' ' . $config->endopointcategories, array('class' => 'alert alert-success'));
        } else {
            echo html_writer::tag('div', get_string('status_error', 'enrol_momentoenroll') . ' ' . $config->endopointcategories, array('class' => 'alert alert-danger'));
        }
    } else {
        echo html_writer::tag('p', get_string('sync_disabled', 'enrol_momentoenroll'));
    }
    echo html_writer::empty_tag('hr');
    
    echo html_writer::tag('h4', get_string('endopointcourses', 'enrol_momentoenroll'));
    if ($config->coursesenabled == ACTIVE){
        if (test_webservice_endpoint($config->endopointcourses) == CONNECTION_OK){
            echo html_writer::tag('div', get_string('status_ok', 'enrol_momentoenroll') . ' ' . $config->endopointcourses, array('class' => 'alert alert-success'));
        } else {
            echo html_writer::tag('div', get_string('status_error', 'enrol_momentoenroll') . ' ' . $config->endopointcourses, array('class' => 'alert alert-danger'));
        }
    } else {
        echo html_writer::tag('p', get_string('sync_disabled', 'enrol_momentoenroll'));
    }
    echo html_writer::empty_tag('hr');
    
    echo html_writer::tag('h4', get_string('endopointenrolments', 'enrol_momentoenroll'));
    if ($config->enrolmentsenabled == ACTIVE){
        if (test_webservice_endpoint($config->endopointenrolments) == CONNECTION_OK){
            echo html_writer::tag('div', get_string('status_ok', 'enrol_momentoenroll') . ' ' . $config->endopointenrolments, array('class' => 'alert alert-success'));
        } else {
            echo html_writer::tag('div', get_string('status_error', 'enrol_momentoenroll') . ' ' . $config->endopointenrolments, array('class' => 'alert alert-danger'));
        }
    } else {
        echo html_writer::tag('p', get_string('sync_disabled', 'enrol_momentoenroll'));
    }

    echo html_writer::empty_tag('hr');
    echo html_writer::empty_tag('hr');
    echo html_writer::empty_tag('br');
    
    echo html_writer::tag('h4', 'Estado de la última sincronización');

    echo html_writer::start_tag('div', array('ng-app' => 'momentoenroll'));
    echo html_writer::start_tag('div', array('ng-controller' => 'StatusController'));
    
    echo html_writer::empty_tag('hr');
    echo html_writer::tag('h5', 'Sincronización de usuarios');

    echo html_writer::start_tag('div', array('ng-if' => 'users_sync_status.id'));
    echo html_writer::tag('p', '<strong>Estado: </strong> <span>{{users_sync_status.status_name}}</span>');
    echo html_writer::tag('p', '<strong>Progreso: </strong> <span>{{users_sync_status.progress}}</span>%');
    echo html_writer::tag('p', '<strong>Fecha de inicio: </strong> <span>{{users_sync_status.datetime_formated}}</span>');
    echo html_writer::tag('p', '<strong>Fecha de fin: </strong> <span>{{users_sync_status.datetimefinished_formated}}</span>');
    echo html_writer::tag('p', '<strong>Usuarios creados: </strong> <span>{{users_sync_status.userscreated}}</span>');
    echo html_writer::tag('p', '<strong>Actualizaciones sobre usuarios: </strong> <span>{{users_sync_status.usersupdated}}</span>');
    echo html_writer::end_tag('div');

    echo html_writer::tag('div', 'No se ha hecho la primera sincronización', array('ng-if' => '!users_sync_status.id', 'class' => 'alert alert-info'));
    
    echo html_writer::empty_tag('hr');
    echo html_writer::tag('h5', 'Sincronización de categorías');

    echo html_writer::start_tag('div', array('ng-if' => 'categories_sync_status.id'));
    echo html_writer::tag('p', '<strong>Estado: </strong> <span>{{categories_sync_status.status_name}}</span>');
    echo html_writer::tag('p', '<strong>Progreso: </strong> <span>{{categories_sync_status.progress}}</span>%');
    echo html_writer::tag('p', '<strong>Fecha de inicio: </strong> <span>{{categories_sync_status.datetime_formated}}</span>');
    echo html_writer::tag('p', '<strong>Fecha de fin: </strong> <span>{{categories_sync_status.datetimefinished_formated}}</span>');
    echo html_writer::tag('p', '<strong>categorías creados: </strong> <span>{{categories_sync_status.categoriescreated}}</span>');
    echo html_writer::tag('p', '<strong>Actualizaciones sobre categorías: </strong> <span>{{categories_sync_status.categoriesupdated}}</span>');
    echo html_writer::end_tag('div');

    echo html_writer::tag('div', 'No se ha hecho la primera sincronización', array('ng-if' => '!categories_sync_status.id', 'class' => 'alert alert-info'));
    
    echo html_writer::empty_tag('hr');
    echo html_writer::tag('h5', 'Sincronización de cursos');

    echo html_writer::start_tag('div', array('ng-if' => 'courses_sync_status.id'));
    echo html_writer::tag('p', '<strong>Estado: </strong> <span>{{courses_sync_status.status_name}}</span>');
    echo html_writer::tag('p', '<strong>Progreso: </strong> <span>{{courses_sync_status.progress}}</span>%');
    echo html_writer::tag('p', '<strong>Fecha de inicio: </strong> <span>{{courses_sync_status.datetime_formated}}</span>');
    echo html_writer::tag('p', '<strong>Fecha de fin: </strong> <span>{{courses_sync_status.datetimefinished_formated}}</span>');
    echo html_writer::tag('p', '<strong>Cursos creados: </strong> <span>{{courses_sync_status.coursescreated}}</span>');
    echo html_writer::tag('p', '<strong>Actualizaciones sobre cursos: </strong> <span>{{courses_sync_status.coursesupdated}}</span>');
    echo html_writer::end_tag('div');

    echo html_writer::tag('div', 'No se ha hecho la primera sincronización', array('ng-if' => '!courses_sync_status.id', 'class' => 'alert alert-info'));
    
    echo html_writer::empty_tag('hr');
    echo html_writer::tag('h5', 'Sincronización de matriculaciones');

    echo html_writer::start_tag('div', array('ng-if' => 'enrolments_sync_status.id'));
    echo html_writer::tag('p', '<strong>Estado: </strong> <span>{{enrolments_sync_status.status_name}}</span>');
    echo html_writer::tag('p', '<strong>Progreso: </strong> <span>{{enrolments_sync_status.progress}}</span>%');
    echo html_writer::tag('p', '<strong>Fecha de inicio: </strong> <span>{{enrolments_sync_status.datetime_formated}}</span>');
    echo html_writer::tag('p', '<strong>Fecha de fin: </strong> <span>{{enrolments_sync_status.datetimefinished_formated}}</span>');
    echo html_writer::tag('p', '<strong>Matriculaciones creadas: </strong> <span>{{enrolments_sync_status.enrolmentscreated}}</span>');
    echo html_writer::tag('p', '<strong>Matriculaciones suspendidas: </strong> <span>{{enrolments_sync_status.enrolmentssuspended}}</span>');
    echo html_writer::tag('p', '<strong>Desmatriculaciones: </strong> <span>{{enrolments_sync_status.enrolmentsdeleted}}</span>');
    echo html_writer::end_tag('div');

    echo html_writer::tag('div', 'No se ha hecho la primera sincronización', array('ng-if' => '!enrolments_sync_status.id', 'class' => 'alert alert-info'));

    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    echo html_writer::empty_tag('hr');

    //Required for the script.
    echo html_writer::tag('script', 'var baseUrl = "' . $CFG->wwwroot . '/"');
} else {
    echo html_writer::tag('p', get_string('sync_inactive', 'enrol_momentoenroll'));
}

echo $OUTPUT->footer();