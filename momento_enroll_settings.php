<?php
global $CFG;

require_once($CFG->dirroot."/enrol/momentoenroll/momento_form.php");

echo $OUTPUT->header();

$mform = new momento_enroll_form(null, array());
$mform->display();

echo $OUTPUT->footer();