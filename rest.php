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


if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot."/enrol/momentoenroll/locallib.php");

require_login();

define('STATUS', 'status');
define('START_SYNC', 'start_sync');

$info = json_decode(file_get_contents('php://input'),true);
$query = clean_param($info['action'],  PARAM_TEXT);

if ($query == STATUS){
    echo json_encode(get_status());
} else if ($query == START_SYNC){
    $status = get_status();

    if ($status['users_sync']->status == 1 || $status['courses_sync']->status == 1 || $status['enrolments_sync']->status == 1){
        echo json_encode(array('error' => 'No se puede ejecutar la sincronización debido a que hay una en curso.'));
    } else {
        end_front_connection();
        sleep(2);
        
        global $PRINT_LOG;
        $PRINT_LOG = false;
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        
        try {
            if ($config->enabled == ACTIVE){
                if ($config->usersenabled == ACTIVE){
                    synchronize_users();
                } 
                
                if ($config->coursesenabled == ACTIVE){
                    synchronize_courses();
                }
                
                if ($config->enrolmentsenabled == ACTIVE){
                    synchronize_enrolments();
                }
                logs('Terminó la sincronización');
            } else {
                echo json_encode(array('error' => 'No se puede ejecutar la sincronización porque el plugin está deshabilitado.'));
            }
        } catch (Exception $ex){
            logs('Falló la sincronización');
            logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
        }

        purge_cache();
    }
}