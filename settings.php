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
 * Momento enrolment plugin settings and presets.
 *
 * @package    enrol_momentoenroll
 * @author     Juan Camilo Marthá Piñeros
 * @copyright  2022 Edu Labs {@link https://edu-labs.co}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

include($CFG->dirroot."/enrol/momentoenroll/menu.php");

$settings->add(new admin_setting_heading('enrol_momentoenroll_settings', '', get_string('pluginname_desc', 'enrol_momentoenroll')));

//Menu items
$settings->add(new admin_setting_heading('enrol_momentoenroll_menuitemsstyle', '', '<style>select {width: 100% !important;}</style>'));
$settings->add(new admin_setting_heading('enrol_momentoenroll_menuitems', '', $items));

//--- general settings -----------------------------------------------------------------------------------
$settings->add(new admin_setting_heading('enrol_momentoenroll_settings', '', get_string('pluginname_desc', 'enrol_momentoenroll')));


//Enable this plugin

$settings->add(new admin_setting_heading('enrol_momentoenroll_enabledtitle', get_string('enabletitle', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configcheckbox('enrol_momentoenroll/enabled', get_string('enabled', 'enrol_momentoenroll'), get_string('enabled_desc', 'enrol_momentoenroll'), ''));

/*
// Licence endpoint and Key

$settings->add(new admin_setting_heading('enrol_momentoenroll_licensetitle', get_string('licensetitle', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configtext('enrol_momentoenroll/endpointlicense', get_string('endpointlicense', 'enrol_momentoenroll'), get_string('endpointlicense_desc', 'enrol_momentoenroll'), 'https://tier0.momentolms.com'));
$settings->add(new admin_setting_configtextarea('enrol_momentoenroll/license', get_string('license', 'enrol_momentoenroll'), get_string('license_desc', 'enrol_momentoenroll'), ''));
*/

// Rest connection
/*
$settings->add(new admin_setting_heading('enrol_momentoenroll_restconnectiontitle', get_string('restconnectiontitle', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configcheckbox('enrol_momentoenroll/usetoken', get_string('usetoken', 'enrol_momentoenroll'), get_string('usetoken_desc', 'enrol_momentoenroll'), ''));
$settings->add( new admin_setting_configtext('enrol_momentoenroll/token', get_string('token', 'enrol_momentoenroll'), get_string('token_desc', 'enrol_momentoenroll'), ''));    
$settings->add(new admin_setting_configtext('enrol_momentoenroll/username', get_string('username', 'enrol_momentoenroll'), get_string('username_desc', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configpasswordunmask('enrol_momentoenroll/pass', get_string('pass', 'enrol_momentoenroll'), get_string('pass_desc', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configcheckbox('enrol_momentoenroll/usepost', get_string('usepost', 'enrol_momentoenroll'), get_string('usepost_desc', 'enrol_momentoenroll'), ''));
*/
// Sinchronization

$options = array(2=>get_string('create_update', 'enrol_momentoenroll'),1=>get_string('only_create', 'enrol_momentoenroll'),3=>get_string('only_update', 'enrol_momentoenroll'));
//$options_login = array(1=>get_string('login_google', 'enrol_momentoenroll'),2=>get_string('login_microsoft', 'enrol_momentoenroll'),3=>get_string('login_manual', 'enrol_momentoenroll'));
$options_login = array();
$options_login += core_plugin_manager::standard_plugins_list('auth');

$auths = array();

foreach($options_login as $key => $value){
    $auths[$value] = $value;
}
/*
//Load course formats
require_once($CFG->dirroot.'/course/lib.php');

$courseformats = get_sorted_course_formats(true);
$formcourseformats = array();

foreach ($courseformats as $courseformat) {
    $formcourseformats[$courseformat] = get_string('pluginname', "format_$courseformat");
}*/

$settings->add(new admin_setting_heading('enrol_momentoenroll_endpointuserstitle', get_string('endpointuserstitle', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configcheckbox('enrol_momentoenroll/usersenabled', get_string('usersenabled', 'enrol_momentoenroll'), get_string('usersenabled_desc', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configselect('enrol_momentoenroll/syncusers', get_string('syncusers', 'enrol_momentoenroll'), get_string('syncusers_desc', 'enrol_momentoenroll'), '', $options));
$settings->add(new admin_setting_configtext('enrol_momentoenroll/endopointusers', get_string('endopointusers', 'enrol_momentoenroll'), get_string('endopointusers_desc', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configselect('enrol_momentoenroll/loginusers', get_string('loginusers', 'enrol_momentoenroll'), get_string('loginusers_desc', 'enrol_momentoenroll'), 0, $auths, '', '<style>select {width: 100% !important;}</style>'));

$settings->add(new admin_setting_heading('enrol_momentoenroll_endpointcategoriestitle', get_string('endpointcategoriestitle', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configcheckbox('enrol_momentoenroll/categoriesenabled', get_string('categoriesenabled', 'enrol_momentoenroll'), get_string('categoriesenabled_desc', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configselect('enrol_momentoenroll/synccategories', get_string('synccategories', 'enrol_momentoenroll'), get_string('synccategories_desc', 'enrol_momentoenroll'), '', $options));
$settings->add(new admin_setting_configtext('enrol_momentoenroll/endopointcategories', get_string('endopointcategories', 'enrol_momentoenroll'), get_string('endopointcategories_desc', 'enrol_momentoenroll'), ''));

$settings->add(new admin_setting_heading('enrol_momentoenroll_endpointcoursestitle', get_string('endpointcoursestitle', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configcheckbox('enrol_momentoenroll/coursesenabled', get_string('coursesenabled', 'enrol_momentoenroll'), get_string('coursesenabled_desc', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configselect('enrol_momentoenroll/synccourses', get_string('synccourses', 'enrol_momentoenroll'), get_string('synccourses_desc', 'enrol_momentoenroll'), '', $options));
//$settings->add(new admin_setting_configselect('enrol_momentoenroll/courseformat', get_string('courseformat', 'enrol_momentoenroll'), get_string('courseformat_desc', 'enrol_momentoenroll'), '', $formcourseformats));
//$settings->add(new admin_setting_configtext('enrol_momentoenroll/numsectionscourse', get_string('numsectionscourse', 'enrol_momentoenroll'), get_string('numsectionscourse_desc', 'enrol_momentoenroll'), 1, PARAM_INT));
$settings->add(new admin_setting_configtext('enrol_momentoenroll/endopointcourses', get_string('endopointcourses', 'enrol_momentoenroll'), get_string('endopointcourses_desc', 'enrol_momentoenroll'), ''));

$categories = array();
$categories += core_course_category::make_categories_list('moodle/category:manage');
$settings->add(new admin_setting_configselect('enrol_momentoenroll/defaultcategory', get_string('defaultcategories', 'enrol_momentoenroll'), get_string('defaultcategories_desc', 'enrol_momentoenroll'), 0, $categories, '','<style>select {width: 100% !important;}</style>'));

$settings->add(new admin_setting_heading('enrol_momentoenroll_endpointenrolmentstitle', get_string('endpointenrolmentstitle', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configcheckbox('enrol_momentoenroll/enrolmentsenabled', get_string('enrolmentsenabled', 'enrol_momentoenroll'), get_string('enrolmentsenabled_desc', 'enrol_momentoenroll'), ''));
$settings->add(new admin_setting_configselect('enrol_momentoenroll/syncenrolments', get_string('syncenrolments', 'enrol_momentoenroll'), get_string('syncenrolments_desc', 'enrol_momentoenroll'), '', $options));
$settings->add(new admin_setting_configtext('enrol_momentoenroll/endopointenrolments', get_string('endopointenrolments', 'enrol_momentoenroll'), get_string('endopointenrolments_desc', 'enrol_momentoenroll'), ''));

