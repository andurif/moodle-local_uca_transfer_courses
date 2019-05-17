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
 * Plugin libs.
 *
 * @package    local_uca_transfer_courses
 * @author     Université Clermont Auvergne - Pierre Raynaud, Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns HTML to print tree of course categories (with number of courses) for the frontpage
 *
 * @param int|stdClass|coursecat $category
 * @return string
 */
function course_categories_list($renderer, $category) {
    $chelper = new coursecat_helper();

    // Prepare parameters for courses and categories lists in the tree
    $chelper->set_show_courses($renderer::COURSECAT_SHOW_COURSES_COUNT)
        ->set_attributes(array('class' => 'category-browse category-browse-'.$category))
        ->set_categories_display_options(array('visible' => true));

    return $renderer->coursecat_tree($chelper, core_course_category::get($category));
}