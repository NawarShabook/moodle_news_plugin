<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>;.

/**
 * @package     local_greetings
 * @copyright   2023 Nawar Shabook <nawarshabook@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->libdir . '/formslib.php');

class local_news_category_form extends moodleform {
    /**
     * Define the form.
     */
    public function definition() {
        global $DB;

        $mform    = $this->_form; // Don't forget the underscore!

        $mform->addElement('textarea', 'namecategory', get_string('yourmessage', 'local_news')); // Add elements to your form.
        $mform->setType('namecategory', PARAM_TEXT); // Set type of element.

        $records=$DB->get_records('local_news_categories', ['parent_id'=>'0']);
        $categories=array(0=>'MAIN CATEGORY');
        foreach($records as $record)
        {
            $categories[$record->id]=$record->category_name;
            
        }
        var_dump($categories[4]);
        $mform->addElement('select', 'selectedcategory',get_string('listparentcategory', 'local_news'),$categories);

        $submitlabel = get_string('submit');
        $mform->addElement('submit', 'submitmessage', $submitlabel);

    }
}
