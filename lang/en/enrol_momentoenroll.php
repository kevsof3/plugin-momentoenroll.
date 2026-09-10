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
 * Strings for component 'enrol_momentoenroll', language 'en'.
 *
 * @package   enrol_momentoenroll
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Momento Enroll';
$string['pluginname_desc'] = 'Use this plugin to integrate multiple databases and services with Moodle and automatize users, courses and enrolments creation.';
$string['enabletitle'] = 'Plugin activation';
$string['enabled'] = 'Enable';
$string['enabled_desc'] = 'Enable or disable plugin';
$string['licensetitle'] = 'License settings';
$string['endpointlicense'] = 'License Url';
$string['endpointlicense_desc'] = 'Url in which the license is verified when there is one';
$string['license'] = 'License';
$string['license_desc'] = 'Purchased license obtained with Edu Labs (edu-labs.com)';
$string['authenticationtitle'] = 'REST Authentication';
$string['restauth'] = 'Autenticación por';
$string['restmethod'] = 'Método';
$string['userpass'] = 'Usuario y contraseña';
$string['usetoken'] = 'Use token';
$string['usetoken_desc'] = 'If checked, token authentication will be used , otherwise by username and password';
$string['token'] = 'Token';
$string['token_desc'] = 'Token to authenticate';
$string['username'] = 'Username';
$string['username_desc'] = 'User to authenticate';
$string['pass'] = 'Password';
$string['pass_desc'] = 'Password to authenticate';
$string['usepost'] = 'Use POST';
$string['usepost_desc'] = 'If checked, authentication will be used by POST, otherwise by headers';
$string['endpointuserstitle'] = 'Users sinchronization';
$string['endpointcoursestitle'] = 'Courses sinchronization';
$string['endpointcategoriestitle'] = 'Categories sinchronization';
$string['endpointenrolmentstitle'] = 'Enrolments sinchronization';
$string['endopointusers'] = 'Users action';
$string['endopointusers_desc'] = 'Url to query user information.';
$string['endopointcourses'] = 'Courses action';
$string['endopointcourses_desc'] = 'Url to query categories information.';
$string['endopointcategories'] = 'Categories action';
$string['endopointcategories_desc'] = 'Url to query categories information.';
$string['endopointenrolments'] = 'Enrolments action';
$string['endopointenrolments_desc'] = 'Url to query enrolments information.';
$string['usersenabled'] = 'Enable users sinchronization';
$string['usersenabled_desc'] = 'Enable or disable users sinchronization';
$string['coursesenabled'] = 'Enable courses sinchronization';
$string['coursesenabled_desc'] = 'Enable or disable courses sinchronization';
$string['categoriesenabled'] = 'Enable categories sinchronization';
$string['categoriesenabled_desc'] = 'Enable or disable categories sinchronization';
$string['enrolmentsenabled'] = 'Enable enrolments sinchronization';
$string['enrolmentsenabled_desc'] = 'Enable or disable enrolments sinchronization';
$string['only_create'] = 'Add new only, skip existing';
$string['create_update'] = 'Add new and update existing';
$string['only_update'] = 'Update existing only';
$string['syncusers'] = 'Sinchronize users';
$string['syncusers_desc'] = 'Define how users will sync';
$string['synccourses'] = 'Sinchronize courses';
$string['synccourses_desc'] = 'Define how courses will sync';
$string['synccategories'] = 'Sinchronize categories';
$string['synccategories_desc'] = 'Define how categories will sync';
$string['syncenrolments'] = 'Sinchronize enrolments';
$string['syncenrolments_desc'] = 'Define how enrolments will sync';
$string['loginusers'] = 'Forma de iniciar sesión';
$string['loginusers_desc'] = 'Forma de iniciar sesión de los usuarios';
$string['login_microsoft'] = 'Microsoft';
$string['login_google'] = 'Google';
$string['login_manual'] = 'Manual';
$string['courseformat'] = 'New courses format';
$string['courseformat_desc'] = 'Select the format to new courses';
$string['numsectionscourse'] = 'Number of sections for new courses';
$string['numsectionscourse_desc'] = 'Select the number of sections to create in new courses';
$string['defaultcategories'] = 'Categoría por defecto';
$string['defaultcategories_desc'] = 'Selecciona la categoría para los cursos que no puedan asignarse a una categoría no existente.';
$string['status_ok'] = 'El endpoint responde adecuadamente';
$string['status_error'] = 'El endpoint no responde adecuadamente';
$string['sync_disabled'] = 'La sincronización no está activada';


/****************    CLI STRINGS    *****************/

$string['sync_active'] = ['La sincronización se encuentra activada.'];
$string['sync_inactive'] = ['La sincronización se encuentra activada.'];
$string['no_license'] = ['No posee una licencia activa.'];
$string['free_version'] = ['Continuando con la versión gratuita.'];
$string['finished'] = ['Finalizado.'];
$string['sync_users'] = ['Sincronizando usuarios...'];
$string['sync_users_finished'] = ['Sincronizando usuarios finalizado'];
$string['sync_courses'] = ['Sincronizando cursos...'];
$string['sync_courses_finished'] = ['Sincronizando cursos finalizado'];
$string['sync_enrolments'] = ['Sincronizando matrículas...'];
$string['sync_enrolments'] = ['Sincronizando matriculas finalizado'];
$string['creating'] = ['Creando '];
$string['created'] = ['Creado'];
$string['updating'] = ['Actualizando '];
$string['updated'] = ['Actualizado'];
$string['general_sync_finished'] = ['Sincronización general finalizada'];
