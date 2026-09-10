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
$string['pluginname_desc'] = 'Utiliza esta extensión para integrar tus bases de datos con Moodle por medio de servicios REST y automatizar el proceso de creación de usuarios, cursos y matrículas.';
$string['enabletitle'] = 'Activación del plugin';
$string['enabled'] = 'Activar';
$string['enabled_desc'] = 'Activar o desactivar el plugin';
$string['licensetitle'] = 'Configuración de licencia';
$string['endpointlicense'] = 'Url de verificación';
$string['endpointlicense_desc'] = 'Url en la que ser verificará la licencia cuando la hubiere';
$string['license'] = 'Licencia';
$string['license_desc'] = 'Código de la licencia adquirida con Edu Labs (edu-labs.com)';
$string['authenticationtitle'] = 'Autenticación REST';
$string['restauth'] = 'Autenticación por';
$string['restmethod'] = 'Método';
$string['userpass'] = 'Usuario y contraseña';
$string['usetoken'] = 'Usar token';
$string['usetoken_desc'] = 'Si está activado se usará autenticación por token, de lo contrario por usuario y contraseña';
$string['token'] = 'Token';
$string['token_desc'] = 'Token para autenticarse';
$string['username'] = 'Usuario';
$string['username_desc'] = 'Usuario para autenticarse';
$string['pass'] = 'Contraseña';
$string['pass_desc'] = 'Contraseña para autenticarse';
$string['usepost'] = 'Usar POST';
$string['usepost_desc'] = 'Si está activado se usará autenticación por POST, de lo contrario por headers';
$string['endpointuserstitle'] = 'Sincronización de usuarios';
$string['endpointcoursestitle'] = 'Sincronización de cursos';
$string['endpointcategoriestitle'] = 'Sincronización de categorías';
$string['endpointenrolmentstitle'] = 'Sincronización de matrículas';
$string['endopointusers'] = 'Url para consultar usuarios';
$string['endopointusers_desc'] = 'Url de la cual se consultará la información de usuarios.';
$string['endopointcourses'] = 'Url para consultar cursos';
$string['endopointcourses_desc'] = 'Url de la cual se consultará la información de cursos.';
$string['endopointcategories'] = 'Url para consultar categorías';
$string['endopointcategories_desc'] = 'Url de la cual se consultará la información de categorías.';
$string['endopointenrolments'] = 'Url para consultar matrículas';
$string['endopointenrolments_desc'] = 'Url de la cual se consultará la información de matrículas.';
$string['usersenabled'] = 'Activar sincronización de usuarios';
$string['usersenabled_desc'] = 'Activar o desactivar la sincronización de usuarios';
$string['coursesenabled'] = 'Activar sincronización de cursos';
$string['coursesenabled_desc'] = 'Activar o desactivar la sincronización de cursos';
$string['categoriesenabled'] = 'Activar sincronización de categorías';
$string['categoriesenabled_desc'] = 'Activar o desactivar la sincronización de categorías';
$string['enrolmentsenabled'] = 'Activar sincronización de matrículas';
$string['enrolmentsenabled_desc'] = 'Activar o desactivar la sincronización de matrículas';
$string['only_create'] = 'Agregar sólo nuevos, pasar por alto los existentes';
$string['create_update'] = 'Agregar nuevos y acutalizar los existentes';
$string['only_update'] = 'Acutalizar sólo los existentes';
$string['syncusers'] = 'Acción sobre usuarios';
$string['syncusers_desc'] = 'Define cómo se sincronizarán los usuarios';
$string['synccourses'] = 'Acción sobre cursos';
$string['synccourses_desc'] = 'Define cómo se sincronizarán los cursos';
$string['synccategories'] = 'Acción sobre categorías';
$string['synccategories_desc'] = 'Define cómo se sincronizarán los categorías';
$string['syncenrolments'] = 'Acción sobre matrículas';
$string['syncenrolments_desc'] = 'Define cómo se sincronizarán las matrículas';
$string['loginusers'] = 'Forma de iniciar sesión';
$string['loginusers_desc'] = 'Forma de iniciar sesión de los usuarios';
$string['login_microsoft'] = 'Microsoft';
$string['login_google'] = 'Google';
$string['login_manual'] = 'Manual';
$string['courseformat'] = 'Formato para los cursos nuevos';
$string['courseformat_desc'] = 'Selecciona el formato de curso que se usará para los cursos nuevos';
$string['numsectionscourse'] = 'Número de secciones para los cursos nuevos';
$string['numsectionscourse_desc'] = 'Selecciona el número de secciones que se deben crear para los cursos nuevos';
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