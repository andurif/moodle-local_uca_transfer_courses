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
 * CLI script which start the course transfer.
 *
 * @package    local_uca_transfer_courses
 * @author     Université Clermont Auvergne - Pierre Raynaud, Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require('../../../config.php');
require($CFG->dirroot.'/local/uca_transfer_courses/lib.php');
require($CFG->dirroot.'/enrol/locallib.php');
require_once($CFG->dirroot.'/vendor/autoload.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_cron_helper.class.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/controller/backup_controller.class.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

$client = new GuzzleHttp\Client();
$requests = $DB->get_records('course_transfer_request', array('transfer_start' => null), '', '*', 0, 1);

foreach($requests as $request) {
    $options = array('course' => $request->course_id);
    $request->transfer_start = date('Y-m-d H:i:s');
//    $DB->update_record('course_transfer_request', $request);
    $response = $client->post(get_config('local_uca_transfer_courses', 'course_export_url'), ['form_params' => $options]);
    $datas = json_decode($response->getBody()->getContents());
    $config = get_config('backup');
    //On copie le fichier entre les deux moodledatas
    //On suppose que l'on peut communiquer simplement entre les dossiers moodledatas s'ils sont sur le même serveur ou avec un montage nfs
    //Si les deux dossiers sont distants => modifier la commande en faisant par exemple un scp.
    shell_exec('cp ' . get_config('local_uca_transfer_courses', 'transfer_archives_folder') . $datas->archive_path . ' ' . $config->backup_auto_destination);

    $filePath = $datas->archive_path . "_folder";
    $directory = $config->backup_auto_destination;
    $category = $request->target_category_id;
    $user->id = $request->user_id;
//    $user->id = 2;

    $courseid = restore_dbops::create_new_course('Test FullName', 'Test ShortName', $category);
    $newCourse = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

    $directory_dest = $CFG->dataroot . "/temp/backup/";
    $fb = get_file_packer('application/vnd.moodle.backup');
    if (!is_dir($directory_dest . $filePath)) {
        mkdir($directory_dest . $filePath);
    }

    $fb->extract_to_pathname($directory . $datas->archive_path, $directory_dest . $filePath);
    unlink($directory . $datas->archive_path);

    //Il faut simuler que l'admin fasse l'action => si besoin modifier l'affectation de $user->id = 2 par l'id d'un utilisateur étant administrateur du moodle
    if($user->id != null) {
        enrol_course_updated(true, $newCourse, $newCourse);
        $enrol_plugin = enrol_get_plugin('manual');
        $enrolid = $DB->get_record('enrol', array('enrol' => 'manual', 'courseid' => $courseid), '*', MUST_EXIST);
        //On ajoute le rôle donné à la création (normalement gestionnaire) pour que l'utilisateur ayant transféré le cours puisse le gérer
        //Si besoin modifier en mettant directement l'id du role voulu.
        $enrol_plugin->enrol_user($enrolid, $user->id, $CFG->creatornewroleid);
        $user->id = 2;
    } else {
        $user->id = 2;
    }

    $controller = new restore_controller($filePath, $courseid, backup::INTERACTIVE_NO, backup::MODE_SAMESITE, $user->id, backup::TARGET_NEW_COURSE, null);
    $controller->execute_precheck();
    $controller->execute_plan();
//    rmdir($directory_dest . $filePath);

    $request->transfer_end = date('Y-m-d H:i:s');
    $DB->update_record('course_transfer_request', $request);

    //On supprime l'archive générée une fois le transfert terminé
    $options = array('archive_path' => $datas->archive_path, 'action' => 'delete');
    $client->post(get_config('local_uca_transfer_courses', 'course_export_url'), ['form_params' => $options]);
}
$user->id = null;