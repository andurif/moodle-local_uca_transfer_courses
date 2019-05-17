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
 * Plugin lang file: English.
 *
 * @package    local_uca_transfer_course
 * @author     Université Clermont Auvergne - Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Courses\' transfer';
$string['transfer_request'] = 'Course tranfer request';
$string['transfer_histo'] = 'Requests\' history';
$string['transfer_list'] = 'Your avalaible courses';
$string['transfer_requests'] = 'Your requests';
$string['no_request'] = 'No course transfer request.';
$string['request_date'] = 'Request date';
$string['course_transfer_url'] = 'Url of the json file listing available courses to transfer';
$string['course_transfer_url_desc'] = 'Url of the json file listing courses of your former moodle available for a transfer.';
$string['course_export_url'] = 'Url to launch the courses\' transfer';
$string['course_export_url_desc'] = 'Url of the command which launch the courses\' transfer.';
$string['transfer_archives_folder'] = 'Folder where courses\' archives we want to transfer are generated';
$string['transfer_archives_folder_desc'] = 'Path of the folder where courses\' archives we want to transfer are generated on your former moodle.';
$string['no_course'] = 'There is no course to transfer.';
$string['transfer'] = 'Transfer selected courses';
$string['to_transfer'] = 'Courses to transfer';
$string['cancel_transfer'] = 'Cancel the transfer';
$string['transfer_complete'] = 'Transfered on ';
$string['transfer_in_progress'] = 'In progress, started on ';
$string['target'] = 'Target course category: ';
$string['privacy:metadata'] = 'This plugin store course transfer requests from another moodle and done by the user.';
$string['privacy:metadata:userid'] = 'The ID of the user who made the request.';
$string['privacy:metadata:courseid'] = 'The ID of the course concerned by the request.';
$string['privacy:metadata:categoryid'] = 'The ID of the course category where the new course will be transfered.';
$string['privacy:metadata:list'] = 'List of transfers requested by the user %s:';
$string['privacy:metadata:request'] = 'Course "%s" in the course category "%s".\n';
$string['privacy:metadata:empty'] = 'No data to export.';