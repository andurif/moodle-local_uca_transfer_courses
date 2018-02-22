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
 * Custom UCA mustache helper.
 *
 * Custom mustache helper which permits to use the moodle_url() in mustache templates.
 *
 * @package    local_uca_transfer_courses
 * @author     Université Clermont Auvergne, Pierre Raynaud, Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use renderer_base;
use Mustache_LambdaHelper;

/**
 * Custom UCA mustache helper.
 *
 * Custom mustache helper which permits to use the moodle_url() in mustache templates.
 *
 * @package    local_uca_transfer_courses
 * @author     Université Clermont Auvergne, Pierre Raynaud, Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class uca_url_helper
{
    /** @var renderer_base $renderer A reference to the renderer in use */
    private $renderer;

    /**
     * Save a reference to the renderer.
     * @param renderer_base $renderer
     */
    public function __construct(renderer_base $renderer) {
        $this->renderer = $renderer;
    }

    /**
     * We define the helper
     * @param string $string string we want to transform in moodle_url.
     * @param Mustache_LambdaHelper $helper the mustache helper.
     * @return \moodle_url the generated moodle url.
     */
    public function uca_url($string, Mustache_LambdaHelper $helper) {

        $string = $helper->render($string);

        $key = strtok($string, ",");
        $string = trim($key);

        $params = strtok(",");
        $params = trim($params);

        $params = json_decode($params,true);

        return new \moodle_url($string,$params);
    }
}