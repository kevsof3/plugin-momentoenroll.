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

$json = json_decode('{
    "status": {
        "description": "ok",
        "success": true
    },
    "result": [
        {
            "entityId": "HoWXQAc8R9K0hgD4Z-EzPg",
            "openBadgeId": "https://api.badgr.io/public/badges/HoWXQAc8R9K0hgD4Z-EzPg",
            "createdAt": "2023-07-11T17:07:29.425097Z",
            "createdBy": "vCzzTeqeQCyI4ssEIque-g",
            "issuer": "A5wUDt2RQn2vRRTXVUyS6g",
            "issuerOpenBadgeId": "https://api.badgr.io/public/issuers/A5wUDt2RQn2vRRTXVUyS6g",
            "name": "Pruebas de verificaci�n 3",
            "image": "https://api.badgr.io/public/badges/HoWXQAc8R9K0hgD4Z-EzPg/image",
            "description": "Esta es la pruebas pt3",
            "criteriaUrl": "https://techsupport3.edu-labs.co/tresnueve/badges/badge.php?hash=ed91cd8fa1f8d131f1633e1d980bb6891b8ee22a",
            "criteriaNarrative": "Los estudiantes son galardonados con esta insignia cuando han cumplido el siguiente requisito:\n * Esta insignia debe ser otorgada por un usuario con el siguiente rol:\nGestor",
            "tags": [],
            "alignments": [],
            "expires": {
                "amount": null,
                "duration": null
            },
            "archived": false
        }
    ],
    "timestamp": null,
    "pagination": null,
    "validationErrors": [],
    "fieldErrors": {},
    "nonFieldErrors": [],
    "errorCode": null,
    "warnings": {}
}');

$json = $json->result;

print_r($json);