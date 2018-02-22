<?php

/**
 * Returns HTML to print tree of course categories (with number of courses) for the frontpage
 *
 * @param int|stdClass|coursecat $category
 * @return string
 */
function course_categories_list($renderer, $category) {
    global $CFG;
    require_once($CFG->libdir. '/coursecatlib.php');

    $chelper = new coursecat_helper();

    // Prepare parameters for courses and categories lists in the tree
    $chelper->set_show_courses($renderer::COURSECAT_SHOW_COURSES_COUNT)
        ->set_attributes(array('class' => 'category-browse category-browse-'.$category))
        ->set_categories_display_options(array('visible' => true));

    return $renderer->coursecat_tree($chelper, coursecat::get($category));
}