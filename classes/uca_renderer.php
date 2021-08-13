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
 * Custom UCA renderer.
 *
 * Custom renderer made to use our custom helper for using moodle_url() in mustache templates.
 * Extends renderer_base class.
 *
 * @package    local_uca_transfer_courses
 * @author     Université Clermont Auvergne - Pierre Raynaud, Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Mustache_Engine;

/**
 * Custom UCA renderer.
 *
 * Custom renderer made to use our custom helper for using moodle_url() in mustache templates.
 * Extends renderer_base class.
 *
 * @package    local_uca_transfer_courses
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class uca_renderer extends \renderer_base {

    /** @var Mustache_Engine custom instance of the mustache class */
    protected $uca_mustache;

    /**
     * Constructor.
     * @param moodle_page $page the page we are doing output for.
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target = null) {
        parent::__construct($page, $target);
    }

    /**
     * Return an instance of the mustache class.
     *
     * @return Mustache_Engine
     */
    protected function get_mustache() {
        global $PAGE;

        if ($this->uca_mustache === null) {
            $themename = $this->page->theme->name;
            $themerev = theme_get_revision();

            $cachedir = make_localcache_directory("mustache/$themerev/$themename");

            $loader = new \core\output\mustache_filesystem_loader();
            $stringhelper = new \core\output\mustache_string_helper();
            $quotehelper = new \core\output\mustache_quote_helper();
            $jshelper = new \core\output\mustache_javascript_helper($PAGE->requires);
            $pixhelper = new \core\output\mustache_pix_helper($this);
            $urlhelper = new \uca_url_helper($this); //we add our helper

            $safeconfig = $this->page->requires->get_config_for_javascript($this->page, $this);

            $helpers = array(
                'config'    => $safeconfig,
                'str'       => array($stringhelper, 'str'),
                'quote'     => array($quotehelper, 'quote'),
                'js'        => array($jshelper, 'help'),
                'pix'       => array($pixhelper, 'pix'),
                'uca_url'   => array($urlhelper, 'uca_url')
            );

            $this->uca_mustache = new Mustache_Engine(array(
                'cache'     => $cachedir,
                'escape'    => 's',
                'loader'    => $loader,
                'helpers'   => $helpers,
                'pragmas'   => [Mustache_Engine::PRAGMA_BLOCKS]
            ));

        }

        return $this->uca_mustache;
    }
}