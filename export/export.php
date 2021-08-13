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
 * Script permettant de sortir un json des cours dont l'utilisateur a les droits de gestion.
 * Le json est ensuite utilisé dans la nouvelle plateforme pour sélectionner les cours à transférer.
 * Pour récupérer l'utilisateur, on passe son username par un requête http.
 *
 * @package    local_uca_transfer_courses
 * @author     Université Clermont Auvergne - Pierre Raynaud, Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');
include($CFG->dirroot.'/enrol/locallib.php');
include_once($CFG->libdir.'/accesslib.php');


$username = $_POST['username'];
//$username = "admin";
$user = $DB->get_record('user', array('username' => $username, 'suspended' => 0));
$data = array();
if ($user) {
    $courses = getCourses($user);

    $categories = array();
    $root = array();

    foreach ($courses as $course) {
        $cat = core_course_category::get($course->category, MUST_EXIST, true);
        $parents = array_reverse($cat->get_parents());

        if (!isset($categories[$course->category])) {
            $categorie = new StdClass();
            $categorie->category_id = $course->category;
            $categorie->text = $cat->get_formatted_name();
            $categorie->type = "category";
            $categorie->children = array();
            $categories[$course->category] = $categorie;

            for ($i=0; $i<count($parents); $i++) {
                if (!isset($categories[$parents[$i]])) {
                    $parent = core_course_category::get($parents[$i],MUST_EXIST,true);
                    $categorie = new StdClass();
                    $categorie->category_id = $parents[$i];
                    $categorie->text = $parent->get_formatted_name();
                    $categorie->type = "category";
                    $categorie->children = array();
                    $categories[$parents[$i]] = $categorie;
                }
            }
        }

        $parents = array_reverse($parents);
        $parents[] = $course->category;

        // Gestion des catégories filles.
        for ($i=0 ; $i<count($parents)-1; $i++) {
            if (!in_array($parents[$i+1], $categories[$parents[$i]]->children)) {
                $categories[$parents[$i]]->children[] = $parents[$i + 1];
            }
        }

        // On détermine le haut de l'arbre.
        $rootCat = (0 == count($parents)) ? $course->category : $parents[0];

        if (!in_array($rootCat,$root)) {
            $root[] = $rootCat;
        }

        $node = new stdClass();
        $node->course_id = $course->id;
        $node->text = $course->fullname;
        $node->data = json_encode($course);
        $node->type = 'course';
        $node->children = array();
        $categories[$course->category]->courses[$course->fullname] = $node;
        ksort($categories[$course->category]->courses);
        $categories[$course->category]->courses = array_values($categories[$course->category]->courses);
    }

    foreach ($root as $cat) {
        $data[] = getCategorie($cat,$categories);
    }

    foreach ($data as $cate) {
        add_courses_in_tree($cate);
    }

    $data = array_values($data);
}

echo json_encode($data);

/* ----------- */

/**
 * Fonction permettant de récupérer une liste de cours pour l'utilisateur passé en paramètres.
 * @param $user l'utilisateur
 * @return array
 */
function getCourses($user) {
    $courses = enrol_get_users_courses($user->id, true, null, 'fullname ASC,sortorder ASC');
    $list = array();

    foreach ($courses as $key => $course) {
        $ctx = context_course::instance($course->id, true);
        $roles = get_user_roles($ctx,$user->id, true);

        foreach ($roles as $role) {
            // On récupère les cours où l'utilisateur a des droits de gestion.
            // Si besoin modifier le nom du rôle.
            if ($role->shortname == 'manager') {
                $list[] = $course;
                break;
            }
        }
    }

    return $list;
}

/**
 * @param $categorieId
 * @param $categories
 * @return mixed
 */
function getCategorie($categorieId, $categories) {
    $category = $categories[$categorieId];
    $children = array();

    if ($category->children) {
        foreach ($category->children as $childId) {
            $children[$categories[$childId]->text.$childId] = getCategorie($childId, $categories);
        }
    }

    ksort($children);
    $category->children = array_values($children);

    return $category;
}

/**
 * Fonction permettant d'ajouter les cours au niveau de la catégorie passée en paramètre.
 * @param $category la catégorie de cours.
 */
function add_courses_in_tree($category) {
    if (isset($category->courses)) {
        foreach ($category->courses as $course) {
            $cc = new stdClass();
            $cc->text = $course->text;
            $cc->data = json_encode($course);
            $cc->type = "course";
            $cc->children = [];
            $category->children[] = $cc;
        }

        foreach ($category->children as $child) {
            add_courses_in_tree($child);
        }
    }
}