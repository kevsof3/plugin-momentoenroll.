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
 * Momento enroll plugin settings menu.
 *
 * @package    momento_enroll
 * @author     Juan Camilo Marthá Piñeros
 * @copyright  2022 Edu Labs {@link https://edu-labs.co}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$config = get_config('enrol_momentoenroll');

$items = '';

if (!empty($config->enabled) && $config->enabled == 1){
    $items = html_writer::empty_tag('hr');
    $items .= html_writer::tag('a', 'Sincronización', array('class' => 'btn btn-primary m-2', 'href' => new moodle_url('/admin/settings.php?section=enrolsettingsmomentoenroll')));
    $items .= html_writer::tag('a', 'Autenticación', array('class' => 'btn btn-info m-2', 'href' => new moodle_url('/enrol/momentoenroll/index.php')));
    $items .= html_writer::tag('a', 'Estado', array('class' => 'btn btn-success m-2', 'href' => new moodle_url('/enrol/momentoenroll/status.php')));
    $items .= html_writer::tag('a', 'Logs', array('class' => 'btn btn-secondary m-2', 'href' => new moodle_url('/enrol/momentoenroll/logs.php')));
    //$items .= html_writer::tag('a', 'Ejecución manual', array('class' => 'btn btn-danger m-2', 'href' => new moodle_url('/enrol/momentoenroll/execution.php')));
    $items .= html_writer::empty_tag('hr');
}