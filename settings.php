<?php

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_uca_transfer_courses', get_string('pluginname', 'local_uca_transfer_courses') );
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext(
        'local_uca_transfer_courses/course_transfer_url',
        get_string('course_transfer_url', 'local_uca_transfer_courses'),
        get_string('course_transfer_url_desc', 'local_uca_transfer_courses'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_uca_transfer_courses/course_export_url',
        get_string('course_export_url', 'local_uca_transfer_courses'),
        get_string('course_export_url_desc', 'local_uca_transfer_courses'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_uca_transfer_courses/transfer_archives_folder',
        get_string('transfer_archives_folder', 'local_uca_transfer_courses'),
        get_string('transfer_archives_folder', 'local_uca_transfer_courses'),
        ''
    ));
}
