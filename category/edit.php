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
require_once($CFG->dirroot . '/local/news/category/category_form.php');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/news/create_category.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading(get_string('category', 'local_news'));

require_login();
if (isguestuser()) {
    throw new moodle_exception('noguest');
}


//$action= optional_param('action','', PARAM_TEXT);

$id = required_param('id',PARAM_INT);

$category_name = optional_param('category_name', '',PARAM_TEXT);
$parent_category_name = optional_param('parent_category_name','',PARAM_TEXT);

// $sql="SELECT m.id from {local_news_categories} as m where category_name=$parent_category_name";

$parent_id = $DB->get_record('local_news_categories', ['category_name'=>$parent_category_name])->id;

$classform=new local_news_category_form();
$classform->getMyObject()->setDefault('namecategory',$category_name);
$classform->getMyObject()->setDefault('selectedcategory',$parent_id);
$classform->getMyObject()->setDefault('id',$id);

if($data = $classform->get_data()) {
//    require_capability('local/greetings:postmessages', $context);

    $category_name1 = required_param('namecategory', PARAM_TEXT);
    $selectedcategory1 = required_param('selectedcategory', PARAM_TEXT);
//    $id = required_param('id', PARAM_TEXT);

    if (!empty($category_name1) && !empty($selectedcategory1)) {

        $record = new stdClass;
        $record->id = $id;
        $record->category_name = $category_name1;
        $record->parent_id = $selectedcategory1;
        $record->timecreated = time();

        $DB->update_record('local_news_categories', $record);

    }
}

echo $OUTPUT->header();
$classform->display();
echo $OUTPUT->footer();