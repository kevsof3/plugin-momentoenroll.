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
 * momentoenroll logs plugin.
 *
 * @package    enrol_momentoenroll
 * @author     Juan Camilo Marthá Piñeros
 * @copyright  2023 Edu Labs {@link https://edu-labs.co}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG, $OUTPUT;

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot."/enrol/momentoenroll/locallib.php");
include($CFG->dirroot."/enrol/momentoenroll/menu.php");

$PAGE->set_context(\context_system::instance());
$PAGE->set_url($CFG->wwwroot ."/enrol/momentoenroll/logs.php");
$PAGE->set_heading(get_string('pluginname', 'enrol_momentoenroll'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Logs');

require_login();

// if not side admin the die
//if (!is_siteadmin){die();}

$status = optional_param('status', '', PARAM_RAW);

$logs = get_enrol_logs($status);

echo $OUTPUT->header();
echo $OUTPUT->heading('Logs', 3);
echo html_writer::tag('p', get_string('pluginname_desc', 'enrol_momentoenroll'));

//Menú
echo $items;

echo html_writer::tag('p', 'Logs de la última ejecución');
/*
echo html_writer::tag('p', 'Selecciona los módulos que deseas consultar');

$modulos = array('', 'Categorías', 'Cursos', 'Matriculaciones');

echo html_writer::tag('button', 'Usuarios', array('class' => 'mx-2 btn ' . (!empty($users) ? 'btn-success' : 'btn-secondary'), 'onclick' => 'procesar("users")'));
echo html_writer::tag('button', 'Categorías', array('class' => 'mx-2 btn ' . (!empty($categories) ? 'btn-success' : 'btn-secondary'), 'onclick' => 'procesar("categories")'));
echo html_writer::tag('button', 'Cursos', array('class' => 'mx-2 btn ' . (!empty($courses) ? 'btn-success' : 'btn-secondary'), 'onclick' => 'procesar("courses")'));
echo html_writer::tag('button', 'Matriculaciones', array('class' => 'mx-2 btn ' . (!empty($enrolments) ? 'btn-success' : 'btn-secondary'), 'onclick' => 'procesar("enrolments")'));
*/
echo html_writer::empty_tag('hr');
echo html_writer::tag('p', 'Selecciona los tipos de mensajes que deseas consultar');

echo html_writer::tag('a', 'Información', array('class' => 'mx-2 btn ' . (!empty($info) ? 'btn-success' : 'btn-secondary'), 'href' => '?status=INFO'));
echo html_writer::tag('a', 'Errores', array('class' => 'mx-2 btn ' . (!empty($error) ? 'btn-success' : 'btn-secondary'), 'href' => '?status=ERROR'));
/*
echo html_writer::empty_tag('hr');
echo html_writer::tag('button', 'Consultar', array('class' => 'btn btn-primary', 'onclick' => 'consultar()'));
*/
echo html_writer::empty_tag('hr');
echo html_writer::start_tag('div', array('style' => 'max-height: 60vh; overflow: scroll;'));

foreach ($logs as $log){
    echo html_writer::tag('span', $log->datetime . ' ' . $log->action, array('class' => ($log->status == ERROR ? 'text-danger' : 'text-info')));
    echo html_writer::empty_tag('br');
}

echo html_writer::end_tag('div');
?>

<script type="text/javascript">
    var cols = <?= json_encode($keys) ?>;
    var showcols = <?= json_encode($keysbooleans); ?>

    function applyFilters(){
        var url = "<?= $urlonlyreport ?>";

        cols.forEach(function(col, index){
            url += '&columns[]=' + (showcols[index] ? 'y-' : 'n-') + col;
        });
        //console.log(url);
        window.location = url;
    }

    function processCol(col){
        var index = cols.indexOf(col);

        if (showcols[index] == false){
            showcols[index] = true;
            $('#column' + col).removeClass("btn-secondary").addClass("btn-success");
        } else {
            showcols[index] = false;
            $('#column' + col).removeClass("btn-success").addClass("btn-secondary");
        }
    }
</script>

<?php
echo $OUTPUT->footer();