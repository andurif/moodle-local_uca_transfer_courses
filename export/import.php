<?php

/**
 * Script permettant de générer l'archive pour le cours que l'on veut transférer.
 * On passera l'identifiant du cours par une requête http.
 * On renverra un json contenant le chemin vers cette archive.
 */

//define('CLI_SCRIPT', true);
require('../../../config.php');
require_once($CFG->dirroot.'/backup/util/helper/backup_cron_helper.class.php');
require_once($CFG->dirroot.'/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot.'/backup/controller/backup_controller.class.php');
//require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

$action = is_null($_POST['action']) ? 'export' : $_POST['action'];
$courseid = $_POST['course'];
$folder = $CFG->dataroot . '/clfd/transfert/';
//Il faut simuler que l'admin fasse l'action => si besoin modifier l'affectation de $user->id = 2 par l'id d'un utilisateur étant administrateur du moodle


if($action == 'export') {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $USER->id = 2;
	
    try {
        $reponse = new stdClass();
        $config_backup = get_config('backup');
        //Modification des config de bases de la sauvegarde automatique
        set_config('backup_shortname', 0, 'backup');
        set_config('backup_auto_destination', $folder, 'backup');
        set_config('backup_auto_users', 0, 'backup');
        set_config('backup_auto_role_assignments', 0, 'backup');
        set_config('backup_auto_comments', 0, 'backup');
        set_config('backup_auto_badges', 0, 'backup');
        set_config('backup_auto_logs', 0, 'backup');
        set_config('backup_auto_histories', 0, 'backup');
        set_config('backup_auto_groups', 0, 'backup');

        //Création de l'archive
        $helper = backup_cron_automated_helper::launch_automated_backup($course, 0, $USER->id);
        //Récupération du nom de l'archive
        $fileName = backup_plan_dbops::get_default_backup_filename('moodle2', 'course', $course->id, get_config('backup_auto_users', 'backup'), 0, (get_config('backup_shortname', 'backup') == 0));

        //Remettre les anciens paramètres pour la sauvegarde automatique
        //    set_config('backup_shortname', $config_backup->backup_shortname, 'backup');
        set_config('backup_auto_destination', $config_backup->backup_auto_destination, 'backup');

        $reponse->course = $course;
        $reponse->archive_path = $fileName;

        echo json_encode($reponse);
	$USER->id = null;
        die;

    } catch (\Exception $e) {
        echo $e->getMessage();
	$USER->id = null;
        die;
    }
}

if($action == "delete") {
    unlink($folder . $_POST['archive_path']);
}
