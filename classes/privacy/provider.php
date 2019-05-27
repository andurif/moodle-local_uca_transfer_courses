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
 * Privacy Subsystem implementation for local_uca_transfer_courses.
 *
 * @package    local_uca_transfer_courses
 * @author     Université Clermont Auvergne - Anthony Durif
 * @copyright  2019 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uca_transfer_courses\privacy;

use \core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\data_provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for local_uca_transfer_courses implementing null_provider.
 *
 * @copyright  2019 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, data_provider
{
//class provider implements \core_privacy\local\metadata\null_provider {

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason(): string
    {
        return 'privacy:metadata';
    }

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_metadata(collection $items): collection
    {
        $items->add_database_table(
            'course_transfer_request',
            [
                'user_id' => 'privacy:metadata:userid',
                'course_id' => 'privacy:metadata:courseid',
                'target_category_id' => 'privacy:metadata:categoryid',
            ],
            'privacy:metadata');
        return $items;
    }

    /**
     * Get the list of contexts related to users who made a transfer request.
     *
     * @param int $userid The user to search.
     *
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid($userid)
    {
        $contextlist = new contextlist();

        // Context for content_user_data.
        $cudsql = "
          SELECT
            c.id
          FROM {context} c
            INNER JOIN {user} u ON u.id = c.instanceid
          WHERE c.contextlevel = :contextlevel
                AND d.user_id = :userid
        ";

        $cudparams = array(
            'contextlevel' => CONTEXT_USER,
            'userid' => $userid,
        );

        $contextlist->add_from_sql($cudsql, $cudparams);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist)
    {
        if (empty($contextlist)) {
            return;
        }
        foreach ($contextlist as $context) {
            global $DB;
            $text = get_string('privacy:metadata:empty', 'local_uca_transfer_courses');
            if ($context->contextlevel == CONTEXT_USER) {

                $sql = "SELECT *
                        FROM {course_transfer_request} cfr
                        JOIN {context} c ON c.instanceid = cfr.user_id
                        WHERE c.contextlevel = :level
                        AND cfr.user_id = :user";
                $params = [
                    'level' => CONTEXT_USER,
                    'user' => $contextlist->get_user()->id
                ];

                $requests = $DB->get_records_sql($sql, $params);

                if (!empty($requests)) {
                    $text = sprintf(get_string('privacy:metadata:list', 'local_uca_transfer_courses'), fullname($contextlist->get_user()));
                    foreach ($requests as $request) {
                        $text .= sprintf(get_string('privacy:metadata:request', 'local_uca_transfer_courses'), $request->course_name, $request->target_category_name);
                    }
                }
            }
        }

        return $text;
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist)
    {
        global $DB;
        if (empty($contextlist->count())) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        $DB->delete_records('course_transfer_request', array('user_id' => $userid));
    }

    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param context $context Context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context)
    {
        global $DB;
        if ($context->contextlevel != CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;
        $DB->delete_records('course_transfer_request', array('user_id' => $userid));
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $sql = "SELECT d.userid FROM {course_transfer_request} ctr";
        $userlist->add_from_sql('userid', $sql);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $users = $userlist->get_userids();
        $DB->delete_records_select('course_transfer_request', 'user_id IN :usersid', ['users' => $users]);
    }
}

