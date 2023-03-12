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


global $CFG;
global $PAGE;
global $SITE;
global $OUTPUT;
global $DB;
require_once('../../config.php');

require_once($CFG->dirroot . '/local/news/news_form.php');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/news/create_news.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading(get_string('pluginname', 'local_news'));
$newsform = new local_news_form();




if($data = $newsform->get_data()) {
//    require_capability('local/greetings:postmessages', $context);

    $titlenews = required_param('newstitle', PARAM_TEXT);
    $contentnews = required_param('newscontent', PARAM_TEXT);
    $selectedcategory = required_param('selectedcategory', 	PARAM_TEXT);
    
    if (!empty($titlenews)&&!empty($contentnews)&&!empty($selectedcategory)) {

        $file = $newsform->get_new_filename('image');
        $fullpath = "upload image/".time().$file;
        $success = $newsform->save_file('image', $fullpath,true);

        $record = new stdClass;
        $record->title = $titlenews;
        $record->content = $contentnews;
        $record->categoryid =$selectedcategory;
        $record->image =$fullpath;
        $record->timecreated = time();

        $DB->insert_record('local_news', $record);

    }

}


echo $OUTPUT->header();

echo html_writer::link(new moodle_url('/local/news/index.php'), 'All News', array('class' => 'btn btn-primary'));
echo html_writer::link(new moodle_url('/local/news/create_category.php'), 'Create Category', array('class' => 'btn btn-primary'));
echo html_writer::link(new moodle_url('/local/news/create_news.php'), 'Create News', array('class' => 'btn btn-primary'));
echo html_writer::tag('br','');
$newsform->display();

echo $OUTPUT->footer();