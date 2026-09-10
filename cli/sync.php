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
 * CLI sync for full external REST synchronisation.
 *
 * Sample cron entry:
 * # 5 minutes past 4am
 * 5 4 * * * $sudo -u www-data /usr/bin/php /var/www/moodle/enrol/sap/cli/sync.php
 *
 * Notes:
 *   - it is required to use the web server account when executing PHP CLI scripts
 *   - you need to change the "www-data" to match the apache user account
 *   - use "su" if "sudo" not available
 *
 * @package    enrol_momentoenroll
 * @author     Juan Camilo Marthá Piñeros
 * @copyright  2022 Edu Labs {@link https://edu-labs.co}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once("../locallib.php");

$config = get_config('enrol_momentoenroll');

if ($config->enabled == ACTIVE){
    mtrace(get_string('sync_active', 'enrol_momentoenroll')[0]);
    reiniciar_logs();
/*
    if (!empty($config->endpointlicense) && !empty($config->license)){
        $url = htmlspecialchars_decode('https://demo.momentolms.com/local/momentolms/access.php?siteurl=' . $config->endpointlicense . '&component=test&license=' . $config->license);

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $resp = file_get_contents($url,false, stream_context_create($arrContextOptions));

        //mtrace( $resp);

        $alg = "aes-256-ctr"; //Encrypt algorith


        $json = json_decode($resp, true); //Se extraen los datos de JSON a Array

        $encriptado = explode("$", $json['file_encript'])[0]; //Se extrae el archivo encriptado
        $iv = base64_decode(explode("$", $json['file_encript'])[1]); //Se extrae el vector de inicialización

        $desencriptado = openssl_decrypt($encriptado, $alg, $config->license, 0, $iv);
        //mtrace (base64_decode($desencriptado)); //Se decodifica el archivo de base64 para extraer el contenido original y se guarda en disco.
    } else {
        mtrace(get_string('no_license', 'enrol_momentoenroll')[0]);
        mtrace(get_string('free_version', 'enrol_momentoenroll')[0]);
    }*/

    if ($config->usersenabled == ACTIVE){
        synchronize_users();
    } 

    if ($config->categoriesenabled == ACTIVE){
        synchronize_categories();
    } 
    
    if ($config->coursesenabled == ACTIVE){
        synchronize_courses();
    }
    
    if ($config->enrolmentsenabled == ACTIVE){
        synchronize_enrolments();
    }

    mtrace(get_string('general_sync_finished', 'enrol_momentoenroll')[0]);
} else {
    mtrace(get_string('sync_inactive', 'enrol_momentoenroll')[0]);
    mtrace(get_string('finished', 'enrol_momentoenroll')[0]);
}
