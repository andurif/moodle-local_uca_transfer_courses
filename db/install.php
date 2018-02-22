<?php

defined('MOODLE_INTERNAL') || die();

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_local_uca_transfer_courses_install() {
    global $DB;

    $dbman = $DB->get_manager();

    //Création de la table permettant de stocker les demandes de transfert de cours depuis l'ancienne version de coursenligne.
    $table = new xmldb_table('course_transfer_request');
    $table->add_field('id', XMLDB_TYPE_INTEGER, 11, null, true, XMLDB_SEQUENCE, null);
    $table->add_field('user_id', XMLDB_TYPE_INTEGER, 11, null, false, null, null);
    $table->add_field('course_id', XMLDB_TYPE_INTEGER, 11, null, true, null, null);
    $table->add_field('course_name', XMLDB_TYPE_TEXT, null, null, true, null, "");
    $table->add_field('target_category_id', XMLDB_TYPE_INTEGER, 11, null, true, null, null);
    $table->add_field('target_category_name', XMLDB_TYPE_TEXT, null, null, true, null, "");
    $table->add_field('request_date', XMLDB_TYPE_DATETIME, null, null, true);
    $table->add_field('transfer_start', XMLDB_TYPE_DATETIME, null, null, false);
    $table->add_field('transfer_end', XMLDB_TYPE_DATETIME, null, null, false);
    $pk = new xmldb_key('primary');
    $pk->set_attributes(XMLDB_KEY_PRIMARY, array('id'), null, null);
    $table->addKey($pk);

    if (!$dbman->table_exists($table->getName())) {
        $dbman->create_table($table);
    }

    return true;
}