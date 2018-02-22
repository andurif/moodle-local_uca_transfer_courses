<?php

define('AJAX_SCRIPT', true);

require('../../config.php');
require('./lib.php');

$action  = required_param('action', PARAM_ALPHANUMEXT);
$PAGE->set_url(new moodle_url('/local/uca_transfer_courses/ajax.php', array('action' => $action)));

$response = new stdClass();

try {
    switch($action) {
        case 'cancel_transfert_request':
            $request_id  = optional_param('request_id', 0, PARAM_INT);
            if($request_id != 0) {
                $DB->delete_records('course_transfer_request', array('id' => $request_id));
            }
            $response->type = 'success';
            break;

        case 'get_transfer_asker':
            $request_id  = optional_param('request_id', 0, PARAM_INT);
            if($request_id != 0) {
                $asker = $DB->get_records_sql('SELECT u.* from {course_transfer_request} tr join {user} u on tr.user_id = u.id where transfer_end IS NOT NULL and tr.course_id =' . $request_id);
                if($asker) {
                    $asker = current($asker);
                    $response->msg = $asker->firstname.' '.$asker->lastname;
                    $response->type = 'success';
                }
            }
            break;

        default:
            break;
    }
}
catch (Exception $exc) {
    $reponse->type = "error";
    $reponse->message = $exc->getMessage();
}

echo json_encode($response);
exit;
