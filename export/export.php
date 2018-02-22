<?php

/**
 * Script permettant de sortir un json des cours dont l'utilisateur a les droits de gestion.
 * Le json est ensuite utilisé dans la nouvelle plateforme pour sélectionner les cours à transférer.
 * Pour récupérer l'utilisateur, on passe son username par un requête http.
 */

require('../../../config.php');
include($CFG->dirroot.'/enrol/locallib.php');
include($CFG->libdir.'/coursecatlib.php');
include_once($CFG->libdir.'/accesslib.php');


$username = $_POST['username'];
//$username = "admin";
$user = $DB->get_record('user', array('username' => $username, 'suspended' => 0));
$data = array();
if($user)
{
    $courses = getCourses($user);

    $categories = array();
    $root = array();

    foreach ($courses as $course)
    {
        $cat = coursecat::get($course->category, MUST_EXIST, true);
        $parents = array_reverse($cat->get_parents());

        if (!isset($categories[$course->category]))
        {
            $categorie = new StdClass();
            $categorie->category_id = $course->category;
            $categorie->text = $cat->get_formatted_name();
            $categorie->type = "category";
            $categorie->children = array();
            $categories[$course->category] = $categorie;

            for ($i=0;$i<count($parents);$i++) {
                if (!isset($categories[$parents[$i]])) {
                    $parent = coursecat::get($parents[$i],MUST_EXIST,true);
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
//
//        // Gestion des catégories filles
        for ($i=0 ; $i<count($parents)-1; $i++) {
            if (!in_array($parents[$i+1], $categories[$parents[$i]]->children)) {
                $categories[$parents[$i]]->children[] = $parents[$i + 1];
            }
        }

        // On détermine le haut de l'arbre
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

function getCourses($user)
{
    $courses = enrol_get_users_courses($user->id, true, null, 'fullname ASC,sortorder ASC');
    $list = array();

    foreach($courses as $key => $course)
    {
        $ctx = context_course::instance($course->id, true);
        $roles = get_user_roles($ctx,$user->id, true);

        foreach($roles as $role) {
            //on récupère les cours où l'utilisateur a des droits de gestion.
            //Si besoin modifier le nom du rôle.
            if($role->shortname == 'manager') {
                $list[] = $course;
                break;
            }
        }
    }

    return $list;
}

function getCategorie($categorieId, $categories)
{
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

function add_courses_in_tree($category)
{
    foreach($category->courses as $course) {
        $cc = new stdClass();
        $cc->text = $course->text;
        $cc->data = json_encode($course);
        $cc->type = "course";
        $cc->children = [];
        $category->children[] = $cc;
    }

    foreach($category->children as $child) {
        add_courses_in_tree($child);
    }
}