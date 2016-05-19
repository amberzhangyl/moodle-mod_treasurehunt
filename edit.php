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
 * Prints a particular instance of treasurehunt
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_treasurehunt
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Replace treasurehunt with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once("$CFG->dirroot/mod/treasurehunt/locallib.php");
require_once ($CFG->libdir . '/formslib.php');

GLOBAL $USER;

$id = required_param('id', PARAM_INT);
list ($course, $cm) = get_course_and_cm_from_cmid($id, 'treasurehunt');
$treasurehunt = $DB->get_record('treasurehunt', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/treasurehunt:view', $context);
require_capability('mod/treasurehunt:gettreasurehunt', $context);
require_capability('mod/treasurehunt:managescavenger', $context);

//Poner evento de edicion o algo asi
/* $event = \mod_treasurehunt\event\course_module_viewed::create(array(
  'objectid' => $PAGE->cm->instance,
  'context' => $PAGE->context,
  ));
  $event->add_record_snapshot('course', $PAGE->course);
  $event->add_record_snapshot($PAGE->cm->modname, $treasurehunt);
  $event->trigger(); */

// Print the page header.

$PAGE->set_url('/mod/treasurehunt/edit.php', array('id' => $cm->id));
$PAGE->set_title(format_string($treasurehunt->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_pagelayout('standard');

// Si no hay ningún camino redirijo para crearlo.
if(get_total_roads($treasurehunt->id)==0){
    $roadurl = new moodle_url('/mod/treasurehunt/editroad.php', array('cmid' => $id));
    redirect($roadurl);
}
/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('treasurehunt-'.$somevar);
 */
if (!$lock = is_edition_loked($cm->instance,$USER->id)) {
    $idLock = renew_edition_lock($cm->instance,$USER->id);
    $PAGE->requires->js_call_amd('mod_treasurehunt/renewlock', 'renew_edition_lock', array($cm->instance, $idLock));
    $PAGE->requires->jquery();
    $PAGE->requires->jquery_plugin('ui');
    $PAGE->requires->jquery_plugin('ui-css');
    $PAGE->requires->js_call_amd('mod_treasurehunt/edit', 'edittreasurehunt', array($id, $cm->instance, get_strings_edit(), $idLock));
    $PAGE->requires->css('/mod/treasurehunt/css/ol.css');
}
// Output starts here.
echo $OUTPUT->header();
// Replace the following lines with you own code.
if ($lock) {
    $returnurl = new moodle_url('/mod/treasurehunt/view.php', array('id' => $id));
    print_error('treasurehuntislocked', 'treasurehunt', $returnurl,get_username_blocking_edition($treasurehunt->id));
} else {
    echo $OUTPUT->heading(format_string($treasurehunt->name));
    // Conditions to show the intro can change to look for own settings or whatever.
    if ($treasurehunt->intro) {
        echo $OUTPUT->box(format_module_intro('treasurehunt', $treasurehunt, $cm->id), 'generalbox mod_introbox', 'treasurehuntintro');
    }
    echo $OUTPUT->container_start("treasurehunt_editor");
    echo $OUTPUT->box(null, null, 'controlPanel');
    echo $OUTPUT->container_start(null, 'riddleListPanel_global');
    echo $OUTPUT->box(null, null, 'riddleListPanel');
    echo $OUTPUT->container_end();
    echo $OUTPUT->container_start(null, 'map_global');
    echo $OUTPUT->box(null, null, 'map');
    echo $OUTPUT->container_end();
    echo $OUTPUT->box(null, null, 'roadListPanel');
    echo $OUTPUT->container_end();
}
// Finish the page.
echo $OUTPUT->footer();
