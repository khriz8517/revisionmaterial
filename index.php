<?php 

require_once(__DIR__ . '/../../config.php');
global $DB;

require_login();

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/mod/revisionmaterial/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Activities Qroma Plugin');
$PAGE->set_heading('Activities Qroma Plugin');


$templateContext = (object)[
    'sesskey' => sesskey(),
    'cursoid' => $cm->course
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_revisionmaterial/revisionmaterial', $templateContext);
echo $OUTPUT->footer();