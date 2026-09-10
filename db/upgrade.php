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
 * Upgrade code for enrol_momentoenroll.
 *
 * @package    enrol_momentoenroll
 * @copyright  2026 Edu Labs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute enrol_momentoenroll upgrades.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_enrol_momentoenroll_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026050800) {
        $table = new xmldb_table('enrol_momentoenroll_users');

        $field = new xmldb_field('department', XMLDB_TYPE_CHAR, '256', null, null, null, null, 'institution');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('programa_academico', XMLDB_TYPE_CHAR, '256', null, null, null, null, 'lang');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('programa_academico_2', XMLDB_TYPE_CHAR, '256', null, null, null, null,
            'programa_academico');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026050800, 'enrol', 'momentoenroll');
    }

    return true;
}
