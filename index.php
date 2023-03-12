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
$PAGE->set_url(new moodle_url('/local/news/index.php'));
$PAGE->set_pagelayout('standard');

$PAGE->set_title($SITE->fullname.'.'.get_string('pluginname', 'local_news'));
$PAGE->set_heading(get_string('head_list_news', 'local_news'));

//$sql = "SELECT m.id, m.title,m.content, m.timecreated, m.categoryid
//              FROM {local_news} m
//         LEFT JOIN {local_news_categories } u ON u.id = m.categoryid
//          ORDER BY timecreated DESC";
$action = optional_param('action', '', PARAM_TEXT);

if($action=='filter')
{
    $filt = required_param('filt', PARAM_TEXT);
    $sql = "SELECT m.id, m.parent_id FROM {local_news_categories} m where m.category_name=$filt";
    $cat = $DB->get_record_sql($sql);
    

    $sql = " SELECT m.id, m.title,m.content, m.timecreated, m.categoryid,m.image, u.category_name, u.parent_id
              FROM mdl_local_news_categories u LEFT JOIN mdl_local_news m
              ON u.id = m.categoryid where  u.id=$cat->id or u.parent_id=$cat->id and m.id IS NOT NULL  ORDER BY timecreated DESC;";
    
}
else
{
        $sql = "SELECT m.id, m.title,m.content, m.timecreated, m.categoryid,m.image, u.category_name
              FROM {local_news} m  LEFT JOIN {local_news_categories} u 
              ON u.id = m.categoryid ORDER BY timecreated DESC";
              $news = $DB->get_records_sql($sql);

}

    
$news = $DB->get_records_sql($sql);
       


if ($action == 'del') {
    
    require_sesskey();

    $id = required_param('id', PARAM_TEXT);


    $params = array('id' => $id);

    // Users without permission should only delete their own post.


    // TODO: Confirm before deleting.
    $DB->delete_records('local_news', $params);

    redirect($PAGE->url);
}

echo $OUTPUT->header();
echo html_writer::link(new moodle_url('/local/news/create_category.php'), 'Create Category', array('class' => 'btn btn-primary'));
echo html_writer::link(new moodle_url('/local/news/create_news.php'), 'Create News', array('class' => 'btn btn-primary'));
//print_r($messages);

$i=1;
$news_array = array();
        foreach ($news as $article) {
            //countre
            $article->i=$i++;

            //select parent category
            $sql = "SELECT *FROM {local_news_categories} m where m.id=$article->categoryid";
            $cat=$DB->get_record_sql($sql);
      
            if($cat->parent_id != 0)
            {
                
                $sql = "SELECT m.category_name, m.id
                FROM {local_news_categories} m where m.id=$cat->parent_id ";
                $parent_name=$DB->get_record_sql($sql);
                // $article->category_name=$parent_name->category_name.'/'.$article->category_name;
                $article->parent_category_name=$parent_name->category_name;
            }
         
            //take only first 50 chars to display it in table
            if(strlen($article->content)>50)
            {
                $article->subcontent=substr($article->content,0,50).".....";
            }
            else{
                $article->subcontent=$article->content;
            }
            //
            $article->timecreated=userdate($article->timecreated);
            $news_array[] = (array) $article;
        }

$data = array('news' => $news_array, 'sesskey'=>sesskey());
    
echo $OUTPUT->render_from_template('local_news/content', $data);

// print_r($news_array);




/*echo $OUTPUT->box_start('card-columns');
foreach ($news as $m) {
    echo html_writer::start_tag('div', array('class' => 'card '));
    echo html_writer::start_tag('div', array('class' => 'card-body'));
    echo html_writer::tag('p', format_text($m->title, FORMAT_PLAIN), array('class' => 'card-text'));

    echo "<img src='$m->image' alt='Image' class='img-thumbnail' />";

    echo html_writer::tag('p', format_text($m->category_name, FORMAT_PLAIN), array('class' => 'card-text'));
    echo html_writer::start_tag('p', array('class' => 'card-text'));
    echo html_writer::tag('small', format_text($m->content, FORMAT_PLAIN), array('class' => 'text-muted'));
    echo html_writer::end_tag('p');
    
   

    echo html_writer::start_tag('p', array('class' => 'card-text'));
    echo html_writer::tag('small', userdate($m->timecreated), array('class' => 'text-muted '));
    echo html_writer::end_tag('p');
    echo html_writer::start_tag('p', array('class' => 'card-text'));
    echo html_writer::tag('small', format_text($m->categoryid, FORMAT_PLAIN), array('class' => 'text-muted '));
    echo html_writer::end_tag('p');
    echo html_writer::start_tag('p', array('class' => 'card-text'));
    echo html_writer::tag('small', format_text($m->id, FORMAT_PLAIN), array('class' => 'text-muted '));
    echo html_writer::end_tag('p');
    echo html_writer::start_tag('p', array('class' => 'card-footer text-center'));
    echo html_writer::link(
        new moodle_url(
            '/local/news/index.php',
            array('action' => 'del', 'id' => $m->id, 'sesskey' => sesskey())
        ),
        $OUTPUT->pix_icon('t/delete', '') . get_string('delete')
    );
    echo html_writer::end_tag('p');
    echo html_writer::start_tag('p', array('class' => 'card-footer text-center'));
    echo html_writer::link(
        new moodle_url(
            '/local/news/edit_news.php',
            array('action' => 'edit', 'id' => $m->id,'title' => $m->title,'content' => $m->content,'categoryid' => $m->categoryid,'category_name' => $m->category_name, 'sesskey' => sesskey())
        ),
        $OUTPUT->pix_icon('t/edit', '') . get_string('edit')
    );
    echo html_writer::end_tag('p');


    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
}
*/

print_r($news);
echo $OUTPUT->footer();