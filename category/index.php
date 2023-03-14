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
require_once('../../../config.php');
//require_once($CFG->dirroot . '/local/greetings/lib.php');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/news/category/index.php'));
$PAGE->set_pagelayout('standard');

$PAGE->set_title($SITE->fullname.'.'.get_string('category', 'local_news'));
$PAGE->set_heading(get_string('head_list_categories', 'local_news'));

require_login();
if (isguestuser()) {
    throw new moodle_exception('noguest');
}


$action = optional_param('action', '', PARAM_TEXT);


$sql = "SELECT* FROM {local_news_categories} m ORDER BY parent_id ";
$categories = $DB->get_records_sql($sql);


if ($action == 'del') {
    
    require_sesskey();

    $id = required_param('id', PARAM_TEXT);

    $params = array('id' => $id);

    // Users without permission should only delete their own post.

    // TODO: Confirm before deleting.
    $DB->delete_records('local_news_categories', $params);

    redirect($PAGE->url);
}

echo $OUTPUT->header();
echo html_writer::link(new moodle_url('/local/news/category/create_category.php'), 'Create Category', array('class' => 'btn btn-primary'));
echo html_writer::link(new moodle_url('/local/news/index.php'), 'All News', array('class' => 'btn btn-primary'));

//print_r($messages);

$i=1;
$categories_array = array();
        
foreach ($categories as $category)
{
    //countre
    $category->i=$i++;

    //if this category has parent
    if($category->parent_id != 0)
    {    
        $sql = "SELECT m.category_name, m.id
        FROM {local_news_categories} m where m.id=$category->parent_id ";
        $parent_name=$DB->get_record_sql($sql);

        $category->parent_category_name=$parent_name->category_name;
    }
         
    //take only first 50 chars to display it in table

    $category->timecreated=userdate($category->timecreated);
    $categories_array[] = (array) $category;
}

$data = array('categories' => $categories_array, 'sesskey'=>sesskey());
    
echo $OUTPUT->render_from_template('local_news/category_content', $data);
echo $OUTPUT->footer();