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
 * @package     local_news
 * @copyright   2023 Nawar Shabook <nawarshabook@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;
global $PAGE;
global $SITE;
global $OUTPUT;
global $DB;
require_once('../../config.php');
//require_once($CFG->dirroot . '/local/greetings/lib.php');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/news/show.php'));
$PAGE->set_pagelayout('standard');

$PAGE->set_title($SITE->fullname.'.'.get_string('pluginname', 'local_news'));
$PAGE->set_heading(get_string('head_list_news', 'local_news'));

$id = required_param('id', PARAM_TEXT);
$sql = "SELECT m.id, m.title,m.content, m.timecreated, m.categoryid,m.image, u.category_name
              FROM {local_news} m  LEFT JOIN {local_news_categories} u 
              ON u.id = m.categoryid where m.id=$id";
              
$news = $DB->get_record_sql($sql);

$news->timecreated=userdate($news->timecreated);
$action = optional_param('action', '', PARAM_TEXT);


echo $OUTPUT->header();
echo html_writer::link(new moodle_url('/local/news/create_category.php'), 'Create Category', array('class' => 'btn btn-primary'));
echo html_writer::link(new moodle_url('/local/news/create_news.php'), 'Create News', array('class' => 'btn btn-primary'));


$data = array('news' => $news, 'sesskey'=>sesskey());
    
echo $OUTPUT->render_from_template('local_news/show_content', $data);


echo $OUTPUT->footer();