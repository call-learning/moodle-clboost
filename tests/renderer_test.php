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
 * Unit tests for theme_clboost\local\utils
 *
 * @package   theme_clboost
 * @category  test
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for theme_clboost\output
 *
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_clboost_renderer_test extends advanced_testcase {

    /**
     * @var \theme_clboost\output\core_renderer $output
     */
    protected $output;

    /**
     * Get this theme's renderer
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function setUp() {
        parent::setUp();
        // This is to prevent CLI target override for tested renderer so get_renderer
        // returns the theme core renderer instead of the CLI renderer.
        $theme = theme_config::load('clboost');
        $page = new moodle_page();
        $page->set_pagelayout('standard');
        $page->set_context(context_system::instance());
        $page->force_theme('clboost');
        $this->output = new \theme_clboost\output\core_renderer($page, 'general');;
    }

    /**
     * Test that we get additional info  for the template
     */
    public function test_template_additional_information() {
        global $CFG;
        $additionalinfo = $this->output->get_template_additional_information();
        $expected = (object) array(
            'isloggedin' => false,
            'themebasepath' => $CFG->dirroot . '/theme/clboost',
            'themename' => 'clboost',
        );
        $this->assertEquals($expected, $additionalinfo);
    }

    /**
     * Get logo and compact logo url
     */
    public function test_get_logo_url() {
        global $PAGE;
        $logourl = $this->output->get_logo_url();
        $this->assertEquals('https://www.example.com/moodle/theme/clboost/pix/logo.png',
            $logourl->out());
        $clogourl = $this->output->get_compact_logo_url();
        $this->assertEquals('https://www.example.com/moodle/theme/clboost/pix/logo-compact.png',
            $clogourl->out());
    }

    public function test_get_mustache_template() {
        global $CFG, $PAGE;
        $PAGE->force_theme('clboost'); // Important, if not it will just go through boost theme.
        $templatefinder = new \theme_clboost\output\mustache_template_finder();
        $this->assertEquals($CFG->dirroot . '/theme/boost/templates/secure.mustache',
            $templatefinder->get_template_filepath('theme_boost/secure'));
        $this->assertEquals($CFG->dirroot . '/theme/clboost/templates/theme_boost/navbar.mustache',
            $templatefinder->get_template_filepath('theme_boost/navbar'));
        $this->assertEquals($CFG->dirroot . '/theme/clboost/templates/frontpage.mustache',
            $templatefinder->get_template_filepath('theme_clboost/frontpage'));
        $this->assertEquals($CFG->dirroot . '/theme/boost/templates/navbar-secure.mustache',
            $templatefinder->get_template_filepath('theme_boost/navbar-secure'));
    }

    public function test_get_mustache_template_dirs() {
        global $CFG, $PAGE;
        $PAGE->force_theme('clboost'); // Important, if not it will just go through boost theme.
        $templatefinder = new \theme_clboost\output\mustache_template_finder();
        $folders = $templatefinder->get_template_directories_for_component('block_myoverview');
        $this->assertEquals(
            array (
                $CFG->dirroot . '/theme/clboost/templates/block_myoverview/',
                $CFG->dirroot . '/theme/boost/templates/block_myoverview/',
                $CFG->dirroot . '/blocks/myoverview/templates/',
                $CFG->dirroot . '/theme/boost/templates/'
            ),
            $folders);
        $folders = $templatefinder->get_template_directories_for_component('');
        $this->assertEquals(
            array (
                $CFG->dirroot . '/theme/clboost/templates/',
                $CFG->dirroot . '/theme/boost/templates/',
                $CFG->dirroot . '/lib/templates/',
                $CFG->dirroot . '/theme/boost/templates/'
            ),
            $folders);
    }
}