<?php

require_once('../../config.php');
require('./lib.php');
require($CFG->dirroot.'/enrol/locallib.php');
require_once($CFG->dirroot.'/vendor/autoload.php');
require_once('./classes/uca_renderer.php');
require_once('./classes/uca_url_helper.php');

require_login();

$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/uca_transfert_courses/import.php'));
$PAGE->set_title(fullname($USER).' - Import ');
$PAGE->set_heading($PAGE->title);

$PAGE->requires->css('/local/uca_transfer_courses/jstree/dist/themes/default/style.min.css');
$PAGE->requires->css('/local/uca_transfer_courses/styles.css');
$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->js('/local/uca_transfer_courses/js/modal.js', true);
$PAGE->requires->js('/local/uca_transfer_courses/jstree/dist/jstree.js', true);
$PAGE->requires->js('/local/uca_transfer_courses/js/show_categories_tree.js', true);

echo $OUTPUT->header();

$renderer = new uca_renderer($PAGE);
$courserenderer = $PAGE->get_renderer('core', 'course');
$tab = '';

if($_POST['submit_requests']) {
    $requests = json_decode($_POST['json_requests']);

    foreach ($requests as $request) {
        $request->user_id = $USER->id;
        $request->request_date = date('Y-m-d H:i:s');
        $DB->insert_record('course_transfer_request', $request);
    }
    $tab = 'histo';
}

try {
    $client = new GuzzleHttp\Client();
    $options  = array('username' => $USER->username);
    $response = $client->post(get_config('local_uca_transfer_courses', 'course_transfer_url'), ['form_params' => $options]);
    $json = $response->getBody()->getContents();
}
catch(Exception $e) {
    $json = null;
}

$requests = $DB->get_records('course_transfer_request', array('user_id' => $USER->id), 'request_date ASC');
$transferred = $DB->get_records_sql('SELECT DISTINCT course_id from {course_transfer_request} where transfer_end IS NOT NULL');

echo (count(json_decode($json)) != 0) ? $renderer->render_from_template('local_uca_transfer_courses/list_for_import', array(
    'json_courses'  => $json,
    'requests'      => ($requests) ? array('courses' => array_values($requests)) : null,
    'tree'          => course_categories_list($courserenderer, 0),
    'active_histo'  => ($tab == "histo"),
    'transferred'   => implode("-", array_keys($transferred)),
))
: "<div class='alert alert-danger'>".get_string('no_course', 'local_uca_transfer_courses')."</div>";

echo $OUTPUT->footer();
