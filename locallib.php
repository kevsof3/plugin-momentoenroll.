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
 * Archivo de funciones auxiliares locallib.php
 *
 * @package    enrol_momentoenroll
 * @author     Juan Camilo Marthá Piñeros
 * @copyright  2022 Edu Labs {@link https://edu-labs.co}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

define('ACTIVE', 1);
define('POST', 1);
define('HEADERS', 2);
define('TOKEN', 1);
define('ONLY_CREATE', 1);
define('CREATE_UPDATE', 2);
define('ONLY_UPDATE', 3);
define('CONNECTION_OK', 0);
define('CONNECTION_ERROR', 1);
define('IN_PROGRESS', 1);
define('SUCCESS_FINISHED', 2);
define('INCOMPLETE_FINISHED', 3);
define('VISIBLE', 1);
define('ENROL', 'enrol');
define('UNENROL', 'unenrol');
define('SUSPEND', 'suspend');
define('AUTHENTICATED_ROL', 7);
define('INFO', 'INFO');
define('WARNING', 'WARNING');
define('ERROR', 'ERROR');
define('CSV_SEPARATOR', ',');
define('IN_PROGRESS_STR', 'En progreso');
define('FINISHED', 'Terminado correctamente');
define('INCOMPLETE_FINISHED_STR', 'Terminado incompleto');
define('PROGRAMA_ACADEMICO_PROFILE_FIELD', 'programa_academico');
define('PROGRAMA_ACADEMICO_2_PROFILE_FIELD', 'programa_academico_2');


ini_set('memory_limit', -1);

function normalize_user_payload($payload){
    $keys = array(
        'username',
        'idnumber',
        'firstname',
        'lastname',
        'password',
        'email',
        'phone1',
        'phone2',
        'institution',
        'department',
        'city',
        'country',
        'lang'
    );

    $user = array();
    foreach ($keys as $key){
        if (is_array($payload) && array_key_exists($key, $payload)){
            $user[$key] = $payload[$key];
            continue;
        }

        if (is_object($payload) && property_exists($payload, $key)){
            $user[$key] = $payload->{$key};
            continue;
        }

        $user[$key] = '';
    }

    $user['programa_academico'] = '';
    $user['programa_academico_2'] = '';

    if (is_array($payload)){
        if (array_key_exists('programa_academico', $payload)){
            $user['programa_academico'] = $payload['programa_academico'];
        }
        if (array_key_exists('programa_academico_2', $payload)){
            $user['programa_academico_2'] = $payload['programa_academico_2'];
        }
    } else if (is_object($payload)){
        if (property_exists($payload, 'programa_academico')){
            $user['programa_academico'] = $payload->programa_academico;
        }
        if (property_exists($payload, 'programa_academico_2')){
            $user['programa_academico_2'] = $payload->programa_academico_2;
        }
    }

    return $user;
}

function reiniciar_logs(){
    global $DB;
    $DB->execute("TRUNCATE {enrol_momentoenroll_logs}");
}

function synchronize_users(){
    global $DB;
    $config = get_config('enrol_momentoenroll');

    logs('Inicio de sincronización de usuarios');

    try {
        $id_update = $DB->insert_record('enrol_momentoenroll_usr_upd', array('datetime' => time(), 'status' => IN_PROGRESS));

        logs('Obteniendo datos del endpoint de usuarios...');
        $users_post = post_request($config->endopointusers);
        $DB->execute("TRUNCATE {enrol_momentoenroll_users}");
        logs('Datos de usuarios obtenidos.');
        
        logs('Almacenando datos localmente...');
        foreach ($users_post as $payload){
            $user = normalize_user_payload($payload);

            if (empty($user['country'])){
                $user['country'] = 'CO';
            }

            if (empty($user['lang'])){
                $user['lang'] = 'es';
            }

            $DB->insert_record('enrol_momentoenroll_users', $user);
        }
        logs('Datos almacenados');

        $users = $DB->get_recordset("enrol_momentoenroll_users");
        $total_data = $DB->count_records('enrol_momentoenroll_users');
        
        logs('Inicio de iteración de datos');
        $current_info = 1;
        $progress_percentage = $total_data > 0 ? 0 : 100;

        foreach ($users as $user){
            logs('Iterando registro ' . $current_info . '...');
            validate_user_data($user);

            if (empty($user->username)){
                logs('El campo username no puede ser nulo. Registro ' . $user->firstname . ' ' . $user->lastname . ', idnumber: ' . $user->idnumber, '', ERROR);
                $progress_percentage = update_users_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
                $current_info++;
                continue;
            }

            if (empty($user->firstname)){
                logs('El campo firstname no puede ser nulo. Registro ' . $user->username . ', idnumber: ' . $user->idnumber, '', ERROR);
                $progress_percentage = update_users_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
                $current_info++;
                continue;
            }

            if (empty($user->lastname)){
                logs('El campo lastname no puede ser nulo. Registro ' . $user->username . ', idnumber: ' . $user->idnumber, '', ERROR);
                $progress_percentage = update_users_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
                $current_info++;
                continue;
            }

            if (empty($user->email)){
                logs('El campo email no puede ser nulo. Registro ' . $user->firstname . ' ' . $user->lastname . ', idnumber: ' . $user->idnumber, '', ERROR);
                $progress_percentage = update_users_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
                $current_info++;
                continue;
            }

            if (empty($user->idnumber)){
                logs('El campo idnumber no puede ser nulo. Registro ' . $user->firstname . ' ' . $user->lastname . ', username: ' . $user->username, '', ERROR);
                $progress_percentage = update_users_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
                $current_info++;
                continue;
            }

            if (empty($user->phone1)){
                $user->phone1 = '';
            } else if (strlen($user->phone1)  > 20){
                logs('El campo phone1 no puede tener más de 20 caracteres. Registro ' . $user->firstname . ' ' . $user->lastname . ', username: ' . $user->username, '', ERROR);
                $progress_percentage = update_users_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
                $current_info++;
                continue;
            }

            if (empty($user->phone2)){
                $user->phone2 = '';
            } else if (strlen($user->phone2) > 20){
                logs('El campo phone2 no puede tener más de 20 caracteres. Registro ' . $user->firstname . ' ' . $user->lastname . ', username: ' . $user->username, '', ERROR);
                $progress_percentage = update_users_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
                $current_info++;
                continue;
            }

            if (empty($user->institution)){
                $user->institution = '';
            }

            if (empty($user->department)){
                $user->department = '';
            } else if (strlen($user->department) > 255){
                logs('El campo department no puede tener más de 255 caracteres. Registro ' . $user->firstname . ' ' . $user->lastname . ', username: ' . $user->username, '', ERROR);
                $progress_percentage = update_users_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
                $current_info++;
                continue;
            } else {
                $user->department = trim($user->department);
            }

            $programaacademico = '';
            if (!empty($user->programa_academico)){
                $programaacademico = trim($user->programa_academico);
            }

            if (strlen($programaacademico) > 255){
                logs('El campo programa_academico no puede tener más de 255 caracteres. Registro ' . $user->firstname . ' ' . $user->lastname . ', username: ' . $user->username, '', ERROR);
                $progress_percentage = update_users_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
                $current_info++;
                continue;
            }

            $programaacademico2 = '';
            if (!empty($user->programa_academico_2)){
                $programaacademico2 = trim($user->programa_academico_2);
            }

            if (strlen($programaacademico2) > 255){
                logs('El campo programa_academico_2 no puede tener más de 255 caracteres. Registro ' . $user->firstname . ' ' . $user->lastname . ', username: ' . $user->username, '', ERROR);
                $progress_percentage = update_users_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
                $current_info++;
                continue;
            }
            unset($user->programa_academico);
            unset($user->programa_academico_2);

            if (empty($user->city)){
                $user->city = '';
            }

            if (empty($user->country)){
                $user->country = '';
            }

            if (empty($user->lang)){
                $user->lang = '';
            }

            if ($config->syncusers == ONLY_CREATE){
                if (!user_exists($user->username) && !user_exists_by_idnumber($user->idnumber)){
                    $user->auth = $config->loginusers;
                    create_user($user, $id_update, $programaacademico, $programaacademico2);
                }
            } else if ($config->syncusers == CREATE_UPDATE){
                if (!user_exists($user->username) && !user_exists_by_idnumber($user->idnumber)){
                    $user->auth = $config->loginusers;
                    create_user($user, $id_update, $programaacademico, $programaacademico2);
                } else {
                    update_user($user, $id_update, $programaacademico, $programaacademico2);
                }
            } else if ($config->syncusers == ONLY_UPDATE){
                if (user_exists($user->username) || user_exists_by_idnumber($user->idnumber)){
                    update_user($user, $id_update, $programaacademico, $programaacademico2);
                }
            }

            $progress_percentage = update_users_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
            $current_info++;
        }

        $users->close();
        
        $current_update = array(
            'id' => $id_update,
            'progress' => $progress_percentage,
            'status' => SUCCESS_FINISHED,
            'datetimefinished' => time()
        );
        $DB->update_record('enrol_momentoenroll_usr_upd', $current_update);

        logs('Iteración de datos finalizada');
    
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }

    logs('Sincronización de usuarios finalizada.');
}

function validate_user_data(&$user){
    $user->password = $user->password == null ? '' : $user->password;
    $user->city = $user->city == null ? '' : $user->city;
    $user->country = $user->country == null ? '' : $user->country;
    $user->phone1 = $user->phone1 == null ? '' : $user->phone1;
    $user->department = $user->department == null ? '' : $user->department;
    $user->programa_academico = $user->programa_academico == null ? '' : $user->programa_academico;
    $user->programa_academico_2 = $user->programa_academico_2 == null ? '' : $user->programa_academico_2;
}

function update_users_percentage_progress($increment, $total, $percentage, $current_update){
    $calc = floor(($increment / $total) * 100);
    
    if ($calc > $percentage){
        $current_update['progress'] = $calc;
        global $DB;
        
        $DB->update_record('enrol_momentoenroll_usr_upd', $current_update);

        return $calc; 
    }

    return $percentage;
}

function user_exists($username){
    global $DB;
    return $DB->record_exists('user', array('username' => $username));
}

function user_exists_by_idnumber($idnumber){
    global $DB;
    return $DB->record_exists('user', array('idnumber' => $idnumber));
}

function get_profile_field_id($shortname){
    global $DB;

    static $cache = array();
    static $missingfieldswarned = array();

    if (array_key_exists($shortname, $cache)){
        return $cache[$shortname];
    }

    $field = $DB->get_record('user_info_field', array('shortname' => $shortname), 'id', IGNORE_MISSING);
    if (!$field){
        if (empty($missingfieldswarned[$shortname])){
            logs('No existe el campo de perfil personalizado con shortname ' . $shortname . '.', '', WARNING);
            $missingfieldswarned[$shortname] = true;
        }
        $cache[$shortname] = 0;
        return 0;
    }

    $cache[$shortname] = (int)$field->id;
    return $cache[$shortname];
}

function save_custom_profile_field($userid, $shortname, $value){
    global $DB;

    $fieldid = get_profile_field_id($shortname);
    if (empty($fieldid)){
        return;
    }

    $record = $DB->get_record('user_info_data', array('userid' => $userid, 'fieldid' => $fieldid), 'id', IGNORE_MISSING);
    $data = (object)array(
        'userid' => $userid,
        'fieldid' => $fieldid,
        'data' => $value,
        'dataformat' => 0
    );

    if ($record){
        $data->id = $record->id;
        $DB->update_record('user_info_data', $data);
        return;
    }

    $DB->insert_record('user_info_data', $data);
}

function create_user($user, $update_id, $programaacademico = '', $programaacademico2 = ''){
    global $CFG,$DB;
    try {
        require_once($CFG->libdir . '/moodlelib.php');

        logs('Creando usuario ' . $user->firstname . ' ' . $user->lastname . ' con idnumber ' . $user->idnumber);
        $user->password = hash_internal_user_password($user->password);
        $user->confirmed = 1;
        $user->mnethostid = 3;
        $user->timecreated = time();
        $user->timemodified = time();

        $userid = $DB->insert_record('user', $user);
        save_custom_profile_field($userid, PROGRAMA_ACADEMICO_PROFILE_FIELD, $programaacademico);
        save_custom_profile_field($userid, PROGRAMA_ACADEMICO_2_PROFILE_FIELD, $programaacademico2);
        logs('Creado');

        $record_update = $DB->get_record('enrol_momentoenroll_usr_upd', array('id' => $update_id));
        $DB->update_record('enrol_momentoenroll_usr_upd', array('id' => $update_id, 'userscreated' => (intval($record_update->userscreated) + 1)));
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }
}

function update_user($user, $update_id, $programaacademico = '', $programaacademico2 = ''){
    global $DB;
    try {
        logs('Actualizando usuario ' . $user->firstname . ' ' . $user->lastname . ' con idnumber ' . $user->idnumber);
        $user_id = -1;

        if (user_exists_by_idnumber($user->idnumber)){
            $user_id = get_user_id_by_idnumber($user->idnumber);

            if (user_exists($user->username)){
                $userdb = get_user_by_username($user->username);

                if ($userdb->id != $user_id){
                    logs('No se puede actulizar el usuario ' . $user->firstname . ' ' . $user->lastname . ' con idnumber ' . $user->idnumber . ' y id ' . $user_id . ', porque existe un usuario diferente de id ' . $userdb->id . ' con el mismo username ' . $user->username, '', ERROR);
                    return;
                }
            }
        } else {
            $user_id = get_user_id_by_username($user->username);
        }
            
        if ($user_id == -1){
            logs('No se puede actulizar el usuario ' . $user->firstname . ' ' . $user->lastname . ' con idnumber ' . $user->idnumber . ' porque no existe', '', ERROR);
            return;
        }

        $user->id = $user_id;
        $user->mnethostid = 3;
        $user->timemodified = time();
        unset($user->password);

        $DB->update_record('user', $user);
        save_custom_profile_field($user_id, PROGRAMA_ACADEMICO_PROFILE_FIELD, $programaacademico);
        save_custom_profile_field($user_id, PROGRAMA_ACADEMICO_2_PROFILE_FIELD, $programaacademico2);
        logs('Actualizado');

        $record_update = $DB->get_record('enrol_momentoenroll_usr_upd', array('id' => $update_id));
        $DB->update_record('enrol_momentoenroll_usr_upd', array('id' => $update_id, 'usersupdated' => (intval($record_update->usersupdated) + 1)));
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }
}

function get_user_by_username($username){
    global $DB;
    return $DB->get_record('user', array('username' => $username));
}

function get_user_id_by_idnumber($idnumber){
    global $DB;
    $user = $DB->get_records('user', array('idnumber' => $idnumber));

    if (!$user){
        return -1;
    }
    
    $u = array_shift($user);

    return $u->id;
}

function get_user_id_by_username($username){
    global $DB;
    $user = $DB->get_record('user', array('username' => $username));

    if (!$user){
        return -1;
    }

    return $user->id;
}

function synchronize_categories(){
    global $DB;
    $config = get_config('enrol_momentoenroll');

    logs('Inicio de sincronización de categorías');

    try {
        $id_update = $DB->insert_record('enrol_momentoenroll_cat_upd', array('datetime' => time(), 'status' => IN_PROGRESS));

        logs('Obteniendo datos del endpoint de categorías...');
        $categories_post = post_request($config->endopointcategories);
        $DB->execute("TRUNCATE {enrol_momentoenroll_cats}");
        logs('Datos de categorías obtenidos.');
        
        logs('Almacenando datos localmente...');
        foreach ($categories_post as $category){
            $DB->insert_record('enrol_momentoenroll_cats', $category);
        }
        logs('Datos almacenados');

        $categories = $DB->get_recordset("enrol_momentoenroll_cats");
        $total_data = $DB->count_records('enrol_momentoenroll_cats');
        
        logs('Inicio de iteración de datos');
        $current_info = 1;
        $progress_percentage = $total_data > 0 ? 0 : 100;

        foreach ($categories as $category){
            logs('Iterando registro ' . $current_info . '...');

            if ($config->synccategories == ONLY_CREATE){
                if (!category_exists($category->idnumber)){
                    create_category($category, $id_update);
                }
            } else if ($config->synccategories == CREATE_UPDATE){
                if (!category_exists($category->idnumber)){
                    create_category($category, $id_update);
                } else {
                    update_category($category, $id_update);
                }
            } else if ($config->synccategories == ONLY_UPDATE){
                if (category_exists($category->idnumber)){
                    update_category($category, $id_update);
                }
            }

            $progress_percentage = update_categories_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
            $current_info++;
        }

        $categories->close();
        
        $current_update = array(
            'id' => $id_update,
            'progress' => $progress_percentage,
            'status' => SUCCESS_FINISHED,
            'datetimefinished' => time()
        );
        $DB->update_record('enrol_momentoenroll_cat_upd', $current_update);

        purge_cache();
        logs('Iteración de datos finalizada');
    
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }

    logs('Sincronización de categorías finalizada.');
}

function update_categories_percentage_progress($increment, $total, $percentage, $current_update){
    $calc = floor(($increment / $total) * 100);
    
    if ($calc > $percentage){
        $current_update['progress'] = $calc;
        global $DB;
        
        $DB->update_record('enrol_momentoenroll_cat_upd', $current_update);

        return $calc; 
    }

    return $percentage;
}

function create_category($category, $update_id){
    global $CFG,$DB;
    try {
        require_once($CFG->libdir . '/moodlelib.php');

        logs('Creando categoría ' . $category->name . ' con idnumber ' . $category->idnumber);
        $category->parent = category_exists($category->parent) ? get_category_id($category->parent) : 0;
        $DB->insert_record('course_categories', $category);
        logs('Creado');

        $record_update = $DB->get_record('enrol_momentoenroll_cat_upd', array('id' => $update_id));
        $DB->update_record('enrol_momentoenroll_cat_upd', array('id' => $update_id, 'categoriescreated' => (intval($record_update->categoriescreated) + 1)));
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }
}

function update_category($category, $update_id){
    global $DB;
    try {
        logs('Actualizando categoría ' . $category->name . ' con idnumber ' . $category->idnumber);
        $category->id = get_category_id($category->idnumber);
        unset($category->parent);

        $DB->update_record('course_categories', $category);
        logs('Actualizado');

        $record_update = $DB->get_record('enrol_momentoenroll_cat_upd', array('id' => $update_id));
        $DB->update_record('enrol_momentoenroll_cat_upd', array('id' => $update_id, 'categoriesupdated' => (intval($record_update->categoriesupdated) + 1)));
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }
}

function synchronize_courses(){
    global $DB;
    $config = get_config('enrol_momentoenroll');

    logs('Inicio de sincronización de cursos');

    try {
        $id_update = $DB->insert_record('enrol_momentoenroll_crs_upd', array('datetime' => time(), 'status' => IN_PROGRESS));

        logs('Obteniendo datos del endpoint de cursos...');
        $courses_post = post_request($config->endopointcourses);
        $DB->execute("TRUNCATE {enrol_momentoenroll_courses}");
        logs('Datos de cursos obtenidos.');
        
        logs('Almacenando datos localmente...');
        foreach ($courses_post as $course){
            $DB->insert_record('enrol_momentoenroll_courses', $course);
        }
        logs('Datos almacenados');

        $courses = $DB->get_recordset("enrol_momentoenroll_courses");
        $total_data = $DB->count_records('enrol_momentoenroll_courses');
        
        logs('Inicio de iteración de datos');
        $current_info = 1;
        $progress_percentage = $total_data > 0 ? 0 : 100;
        
        foreach ($courses as $course){
            if ($config->synccourses == ONLY_CREATE){
                if (!course_exists($course->shortname) && !course_exists_by_idnumber($course->idnumber)){
                    create_new_course($course, $id_update, $config->defaultcategory, $config->courseformat, $config->numsectionscourse);
                }
            } else if ($config->synccourses == CREATE_UPDATE){
                if (!course_exists($course->shortname) && !course_exists_by_idnumber($course->idnumber)){
                    create_new_course($course, $id_update, $config->defaultcategory);
                } else {
                    update_exist_course($course, $id_update);
                }
            } else if ($config->synccourses == ONLY_UPDATE){
                if (course_exists($course->shortname) || course_exists_by_idnumber($course->idnumber)){
                    update_exist_course($course, $id_update);
                }
            }

            $progress_percentage = update_courses_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
            $current_info++;
        }

        $courses->close();
        
        $current_update = array(
            'id' => $id_update,
            'progress' => $progress_percentage,
            'status' => SUCCESS_FINISHED,
            'datetimefinished' => time()
        );
        $DB->update_record('enrol_momentoenroll_crs_upd', $current_update);

        logs('Iteración de datos finalizada');
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }

    logs('Sincronización de cursos finalizada.');
}

function update_courses_percentage_progress($increment, $total, $percentage, $current_update){
    $calc = floor(($increment / $total) * 100);
    
    if ($calc > $percentage){
        $current_update['progress'] = $calc;
        global $DB;
        
        $DB->update_record('enrol_momentoenroll_crs_upd', $current_update);

        return $calc; 
    }

    return $percentage;
}

function create_new_course($course, $update_id, $default_category){
    global $DB,$CFG;
    require_once($CFG->dirroot.'/course/lib.php');

    try {
        logs('Creando curso ' . $course->fullname . ' con idnumber ' . $course->idnumber . '...');
        $course->timecreated = time();
        $course->startdate = empty($course->startdate) ? time() : strtotime($course->startdate);
        $course->enddate = empty($course->enddate) ? 0 : strtotime($course->enddate);
        $course->lang = empty($course->lang) ? '' : $course->lang;
        $course->visible = 0;

        if (category_exists($course->category)){
            $course->category = get_category_id($course->category);
        } else {
            $course->category = $default_category;
        }

        $courseconfig = get_config('moodlecourse');
        $course = (object) array_merge((array) $courseconfig, (array) $course);
        $course->gnumsections = $course->numsections;
        print_r($course);
        $created_course = create_course($course);
        logs('Creado');

        $record_update = $DB->get_record('enrol_momentoenroll_crs_upd', array('id' => $update_id));
        $DB->update_record('enrol_momentoenroll_crs_upd', array('id' => $update_id, 'coursescreated' => (intval($record_update->coursescreated) + 1)));
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }
}

function update_exist_course($course, $update_id){
    global $DB;
    try {
        logs('Actualizando curso ' . $course->fullname . ' con idnumber ' . $course->idnumber . '...');
        $course_id = get_course_id_by_idnumber($course->idnumber);
        mtrace('por idnumber: ' . $course_id);
        if ($course_id == -1){
            $course->id = get_course_id_by_shortname($course->shortname);
            mtrace('por shortname: ' . $course->id);
        } else {
            $course->id = $course_id;
        }

        $course->startdate = empty($course->startdate) ? time() : strtotime($course->startdate);
        $course->enddate = empty($course->enddate) ? 0 : strtotime($course->enddate);

        unset($course->category);
        $course->timemodified = time();
		
		if (empty($course->lang)){
			unset($course->lang);
		}
        
        $DB->update_record('course', $course);
        logs('Actualizado');

        $record_update = $DB->get_record('enrol_momentoenroll_crs_upd', array('id' => $update_id));
        $DB->update_record('enrol_momentoenroll_crs_upd', array('id' => $update_id, 'coursesupdated' => (intval($record_update->coursesupdated) + 1)));
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }
}

function course_exists($shortname){
    global $DB;
    return $DB->record_exists('course', array('shortname' => $shortname));
}

function course_exists_by_idnumber($idnumber){
    global $DB;
    return $DB->record_exists('course', array('idnumber' => $idnumber));
}

function category_exists($idnumber){
    global $DB;
    return $DB->record_exists('course_categories', array('idnumber' => $idnumber));
}

function get_course_id_by_shortname($shortname){
    global $DB;
    $course = $DB->get_record('course', array('shortname' => $shortname));

    if ($course != false){
        return $course->id;
    }

    return -1;
}

function get_course_id_by_idnumber($idnumber){
    global $DB;
    $course = $DB->get_record('course', array('idnumber' => $idnumber));

    if ($course != null){
        return $course->id;
    }

    return -1;
}

function get_category_id($idnumber){
    global $DB;
    return $DB->get_record('course_categories', array('idnumber' => $idnumber))->id;
}

function add_course_sections($course_id, $num_sections){
    global $DB;

    for ($i = 1; $i <= $num_sections; $i++){
        $section = new stdClass();
        $section->course = $course_id;
        $section->section = $i;
        $section->summaryformat = 1;
        $section->visible = VISIBLE;
        $section->timemodified = time();

        $DB->insert_record('course_sections', $section);
    }
}

function synchronize_enrolments(){
    global $DB;
    $config = get_config('enrol_momentoenroll');

    logs('Inicio de sincronización de matrículas');

    try {
        $id_update = $DB->insert_record('enrol_momentoenroll_enrl_upd', array('datetime' => time(), 'status' => IN_PROGRESS));

        logs('Obteniendo datos del endpoint de matrículas...');
        $enrolments_post = post_request($config->endopointenrolments);
        $DB->execute("TRUNCATE {enrol_momentoenroll_actions}");
        logs('Datos de matrículas obtenidos.');
        
        logs('Almacenando datos localmente...');
        foreach ($enrolments_post as $enrol){
            $DB->insert_record('enrol_momentoenroll_actions', $enrol);
        }
        logs('Datos almacenados');

        $enrolments = $DB->get_recordset("enrol_momentoenroll_actions");
        $total_data = $DB->count_records('enrol_momentoenroll_actions');
        
        logs('Inicio de iteración de datos');
        $current_info = 1;
        $progress_percentage = $total_data > 0 ? 0 : 100;
        
        foreach ($enrolments as $enrol){
            if (!user_exists($enrol->useridnumber)){
                logs('El usuario con username ' . $enrol->useridnumber . ' no existe. No se puede matricular en el curso con idnumber ' . $enrol->courseidnumber, '', ERROR);
                continue;
            }

            if (!course_exists_by_idnumber($enrol->courseidnumber)){
                logs('El curso con idnumber ' . $enrol->courseidnumber . ' no existe. No se puede matricular el usuario con username ' . $enrol->useridnumber, '', ERROR);
                continue;
            }
            
            $enrol->user_id = get_user_id_by_username($enrol->useridnumber);
            $enrol->course_id = get_course_id_by_idnumber($enrol->courseidnumber);
            $enrol->enrol_id = ($DB->get_record('enrol', array('courseid' => $enrol->course_id, 'enrol' => 'manual')))->id;
            $enrol->timestart = empty($enrol->startdate) ? 0 : strtotime($enrol->startdate);
            $enrol->timeend = empty($enrol->enddate) ? 0 : strtotime($enrol->enddate);

            if ($config->syncenrolments == ONLY_CREATE){
                if ($enrol->action == ENROL && !exists_enrol($enrol->user_id, $enrol->enrol_id)){
                    create_enrol($enrol, $id_update);
                }
            } else if ($config->syncenrolments == CREATE_UPDATE){
                if (!exists_enrol($enrol->user_id, $enrol->enrol_id)){
                    create_enrol($enrol, $id_update);
                } else {
                    logs('Ya estaba matriculado el usuario ' . $enrol->useridnumber . ' en el curso ' . $enrol->courseidnumber);
                    update_enrol($enrol, $id_update);
                }
            } else if ($config->syncenrolments == ONLY_UPDATE){
                if (exists_enrol($enrol->user_id, $enrol->enrol_id)){
                    update_enrol($enrol, $id_update);
                }
            }

            $progress_percentage = update_enrolments_percentage_progress($current_info, $total_data, $progress_percentage, array('id' => $id_update));
            $current_info++;
        }

        $enrolments->close();
        
        $current_update = array(
            'id' => $id_update,
            'progress' => $progress_percentage,
            'status' => SUCCESS_FINISHED,
            'datetimefinished' => time()
        );
        $DB->update_record('enrol_momentoenroll_enrl_upd', $current_update);

        logs('Iteración de datos finalizada');
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }

    logs('Sincronización de matriculaciones finalizada.');
}

function update_enrolments_percentage_progress($increment, $total, $percentage, $current_update){
    $calc = floor(($increment / $total) * 100);
    
    if ($calc > $percentage){
        $current_update['progress'] = $calc;
        global $DB;
        
        $DB->update_record('enrol_momentoenroll_enrl_upd', $current_update);

        return $calc; 
    }

    return $percentage;
}

function create_enrol($enrol, $update_id){
    global $DB,$CFG;

    try {
        if (strcasecmp($enrol->action, ENROL) == 0){
            require_once($CFG->dirroot.'/enrol/manual/locallib.php');
            logs('Matriculando usuario de id ' . $enrol->user_id . ' en el curso de id ' . $enrol->course_id);

            $enrol_manual_plugin = enrol_get_plugin('manual');
            $enrol_manual = $DB->get_record('enrol', array('courseid' => $enrol->course_id, 'enrol' => 'manual'));
            $rol = get_rol_code($enrol->rol);

            if ($enrol_manual == false){
                add_manual_enrol_method($enrol->course_id);
                $enrol_manual = $DB->get_record('enrol',array('courseid'=>$enrol->course_id,'enrol'=>'manual'));
            }
            
            $enrol_manual_plugin->enrol_user($enrol_manual, $enrol->user_id, $rol, $enrol->timestart, $enrol->timeend);
            logs('Matriculado.');

            $record_update = $DB->get_record('enrol_momentoenroll_enrl_upd', array('id' => $update_id));
            $DB->update_record('enrol_momentoenroll_enrl_upd', array('id' => $update_id, 'enrolmentscreated' => (intval($record_update->enrolmentscreated) + 1)));
        }
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }
}

function update_enrol($enrol, $update_id){
    global $DB, $CFG;
    try {
        $user_enrolment = $DB->get_record('user_enrolments', array('enrolid' => $enrol->enrol_id, 'userid' => $enrol->user_id));

        if (strcasecmp($enrol->action, UNENROL) == 0){
            require_once($CFG->dirroot.'/enrol/locallib.php');

            $instance = $DB->get_record('enrol', array('id'=>$user_enrolment->enrolid), '*', MUST_EXIST);
            $plugin = enrol_get_plugin($instance->enrol);
            $plugin->unenrol_user($instance, $enrol->user_id);

            $record_update = $DB->get_record('enrol_momentoenroll_enrl_upd', array('id' => $update_id));
            $DB->update_record('enrol_momentoenroll_enrl_upd', array('id' => $update_id, 'enrolmentsdeleted' => (intval($record_update->enrolmentsdeleted) + 1)));
        } else if (strcasecmp($enrol->action, SUSPEND) == 0){
            $suspenduser = new stdClass(); 
            $suspenduser->id = $user_enrolment->id;
            $suspenduser->status = 1;
            $suspenduser->timemodified = time();

            $DB->update_record('user_enrolments', $suspenduser);

            $record_update = $DB->get_record('enrol_momentoenroll_enrl_upd', array('id' => $update_id));
            $DB->update_record('enrol_momentoenroll_enrl_upd', array('id' => $update_id, 'enrolmentssuspended' => (intval($record_update->enrolmentssuspended) + 1)));
        } else {
            $suspenduser = new stdClass(); 
            $suspenduser->id = $user_enrolment->id;
            $suspenduser->timestart = $enrol->timestart;
            $suspenduser->timeend = $enrol->timeend;
            $suspenduser->timemodified = time();

            $DB->update_record('user_enrolments', $suspenduser);
        }
    } catch (Exception $ex){
        logs($ex->getMessage(), $ex->getTraceAsString(), ERROR);
    }
}

function add_manual_enrol_method($course_id){
    global $DB;
    
    $course = $DB->get_record('course', array('id' => $course_id), '*', MUST_EXIST);
    $plugin = enrol_get_plugin('manual');

    $data = array(
        'courseid' => $course->id,
        'type' => 'manual',
        'status' => '0',
        'roleid' => '5',
        'expirynotify' => '0'
    );

    $plugin->add_instance($course, $data);
}

function exists_enrol($user_id, $enrol_id){
    global $DB;
    return $DB->record_exists('user_enrolments', array('userid' => $user_id, 'enrolid' => $enrol_id));
}

function get_rol_code($rol_str){
    global $DB;
    
    $rol = AUTHENTICATED_ROL;

    if ($DB->record_exists('role', array('shortname' => $rol_str))){
        $db_rol = $DB->get_record('role', array('shortname' => $rol_str));
        $rol = $db_rol->id;
    }

    return $rol;
}

function post_request($url){
    $config = get_config('enrol_momentoenroll');
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
//Brayan
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

    if ($config->restmethod == POST){
        $post = '';

        if ($config->restauth == TOKEN){
            $post = 'token=' . $config->token;
            curl_setopt($curl,CURLOPT_POST,1);
        } else {
            $post = 'username=' . $config->username . '&pass=' . $config->pass;
            curl_setopt($curl,CURLOPT_POST,2);
        }
        
        curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
    } else if ($config->restmethod == HEADERS){
        $headers = array();

        if ($config->restauth == TOKEN){
            $headers = array(
               "token: " . $config->token,
            );
        } else {
            $headers = array(
               "username: " . $config->username,
               "pass: " . $config->pass
            );
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $resp = curl_exec($curl); //Se hace la petición GET al servidor
    curl_close($curl);

    return json_decode($resp, true); //Se retornan los datos de JSON a Array
}

function test_webservice_endpoint($url){
    $config = get_config('enrol_momentoenroll');
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);

    if ($config->restmethod == POST){
        $post = '';

        if ($config->restauth == TOKEN){
            $post = 'token=' . $config->token;
            curl_setopt($curl,CURLOPT_POST,1);
        } else {
            $post = 'username=' . $config->username . '&pass=' . $config->pass;
            curl_setopt($curl,CURLOPT_POST,2);
        }
        
        curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
    } else if ($config->restmethod == HEADERS){
        $headers = array();

        if ($config->restauth == TOKEN){
            $headers = array(
               "token: " . $config->token,
            );
        } else {
            $headers = array(
               "username: " . $config->username,
               "pass: " . $config->pass
            );
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    } else {
        curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'GET' );
    }
//Brayan   
   curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $data = curl_exec($curl);
    $content = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    return $content == "200" ? CONNECTION_OK : CONNECTION_ERROR;
}

function logs($action, $description = '', $status = INFO, $save = true){
    global $DB, $PRINT_LOG;
    
    $datetime = date('Y-m-d H:i:s');
    //if ($PRINT_LOG != false){
        mtrace($datetime . ' ' . $status . ' ' . $action . ' ' . $description);
    //}

    if ($save){
        $log = new stdClass();
        $log->status = $status;
        $log->action = $action;
        $log->description = $description;
        $log->timestamp = time();
        $log->datetime = $datetime;

        $DB->insert_record('enrol_momentoenroll_logs', $log);
    }
}

function get_enrol_logs($status = null){
    global $DB;

    $where = array();

    if (!empty($status)){
        $where["status"] = $status; 
    }

    return $DB->get_records('enrol_momentoenroll_logs', $where);
}

function validate_settings_exists(){
    global $DB;

    $settings = array(/*
        'coursesenabled',
        'defaultcategory',
        'enabled',
        'endopointcourses',
        'endopointenrolments',
        'endopointusers',
        'endpointlicense',
        'enrolmentsenabled',
        'license',
        'loginusers',*/
        'restmethod',
        'restauth',
        'pass',
        'token',
        'username'/*,
        'sourcecategory',
        'synccourses',
        'syncenrolments',
        'syncusers',
        'usepost',
        'user',
        'usersenabled',
        'usetoken',
        'version'*/
    );

    foreach ($settings as $setting){
        if (!$DB->record_exists('config_plugins', array('plugin' => 'enrol_momentoenroll', 'name' => $setting))){
            $newsetting = array(
                'plugin' => 'enrol_momentoenroll',
                'name' => $setting,
                'value' => ''
            );

            $DB->insert_record('config_plugins', $newsetting);
        }
    }
    
    purge_cache();
}

function update_settings(){
    global $DB;

    $settings = array(
        'restmethod' => optional_param('restmethod', '', PARAM_RAW),
        'restauth' => optional_param('restauth', '', PARAM_RAW),
        'username' => optional_param('username', '', PARAM_RAW),
        'pass' => optional_param('pass', '', PARAM_RAW),
        'token' => optional_param('token', '', PARAM_RAW)
    );

    foreach ($settings as $setting => $value){
        if ($DB->record_exists('config_plugins', array('plugin' => 'enrol_momentoenroll', 'name' => $setting))){
            $DB->execute('UPDATE {config_plugins} SET value="' . $value . '" WHERE plugin="enrol_momentoenroll" AND name="' . $setting . '"');
        } else {
            $newsetting = array(
                'plugin' => 'enrol_momentoenroll',
                'name' => $setting,
                'value' => $value
            );

            $DB->insert_record('config_plugins', $newsetting);
        }
    }

    purge_cache();
}

function get_status(){
    global $DB;

    $last_users_sync = $DB->get_record_sql('SELECT * FROM {enrol_momentoenroll_usr_upd} ORDER BY id DESC LIMIT 1');
    $last_categories_sync = $DB->get_record_sql('SELECT * FROM {enrol_momentoenroll_cat_upd} ORDER BY id DESC LIMIT 1');
    $last_courses_sync = $DB->get_record_sql('SELECT * FROM {enrol_momentoenroll_crs_upd} ORDER BY id DESC LIMIT 1');
    $last_enrolments_sync = $DB->get_record_sql('SELECT * FROM {enrol_momentoenroll_enrl_upd} ORDER BY id DESC LIMIT 1');
    
    if (!empty($last_users_sync)){
        $last_users_sync->datetime_formated = date('Y-m-d H:i:s', $last_users_sync->datetime);

        if ($last_users_sync->datetimefinished != 0){
            $last_users_sync->datetimefinished_formated = date('Y-m-d H:i:s', $last_users_sync->datetimefinished);
        }

        if ($last_users_sync->status == 1){
            $last_users_sync->status_name = IN_PROGRESS_STR;
        } else if ($last_users_sync->status == 2){
            $last_users_sync->status_name = FINISHED;
        } elseif ($last_users_sync->status == 3){
            $last_users_sync->status_name = INCOMPLETE_FINISHED_STR;
        } 
    }
    
    if (!empty($last_categories_sync)){
        $last_categories_sync->datetime_formated = date('Y-m-d H:i:s', $last_categories_sync->datetime);

        if ($last_categories_sync->datetimefinished != 0){
            $last_categories_sync->datetimefinished_formated = date('Y-m-d H:i:s', $last_categories_sync->datetimefinished);
        }

        if ($last_categories_sync->status == 1){
            $last_categories_sync->status_name = IN_PROGRESS_STR;
        } else if ($last_categories_sync->status == 2){
            $last_categories_sync->status_name = FINISHED;
        } elseif ($last_categories_sync->status == 3){
            $last_categories_sync->status_name = INCOMPLETE_FINISHED_STR;
        } 
    }

    if (!empty($last_courses_sync)){
        $last_courses_sync->datetime_formated = date('Y-m-d H:i:s', $last_courses_sync->datetime);

        if ($last_courses_sync->datetimefinished != 0){
            $last_courses_sync->datetimefinished_formated = date('Y-m-d H:i:s', $last_courses_sync->datetimefinished);
        }

        if ($last_courses_sync->status == 1){
            $last_courses_sync->status_name = IN_PROGRESS_STR;
        } else if ($last_courses_sync->status == 2){
            $last_courses_sync->status_name = FINISHED;
        } elseif ($last_courses_sync->status == 3){
            $last_courses_sync->status_name = INCOMPLETE_FINISHED_STR;
        } 
    }

    if (!empty($last_enrolments_sync)){
        $last_enrolments_sync->datetime_formated = date('Y-m-d H:i:s', $last_enrolments_sync->datetime);

        if ($last_enrolments_sync->datetimefinished != 0){
            $last_enrolments_sync->datetimefinished_formated = date('Y-m-d H:i:s', $last_enrolments_sync->datetimefinished);
        }

        if ($last_enrolments_sync->status == 1){
            $last_enrolments_sync->status_name = IN_PROGRESS_STR;
        } else if ($last_enrolments_sync->status == 2){
            $last_enrolments_sync->status_name = FINISHED;
        } elseif ($last_enrolments_sync->status == 3){
            $last_enrolments_sync->status_name = INCOMPLETE_FINISHED_STR;
        } 
    }

    return array('users_sync' => $last_users_sync, 'categories_sync' => $last_categories_sync, 'courses_sync' => $last_courses_sync, 'enrolments_sync' => $last_enrolments_sync);
}

function purge_cache(){
    global $CFG;
    require_once($CFG->libdir.'/clilib.php');

    $options = array("muc"=>1);
    purge_caches(array_filter($options));
}
