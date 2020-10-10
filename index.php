<?php

require_once(__DIR__ . '/../../config.php');

global $DB, $PAGE, $OUTPUT, $USER;

require_login();

$context = context_system::instance();
$PAGE->set_context($context);

$url = new moodle_url('/local/xereta/index.php');
$PAGE->set_url($url);
$html = '';

if (is_siteadmin($USER->id)) {

    // Load libraries.
    require_once('xerata_form.php');

    // Load calculate params from form, request or set default values.
    $mform = new xereta_local_selection_form($url, null, 'get');

    if ($mform->is_submitted()) {
        // Params from form post.
        $formdata = $mform->get_data();
        $mintime = $formdata->mintime;
        $maxtime = $formdata->maxtime;
    } else {
        // Params from request or default values.
        $mintime = optional_param('mintime', time(), PARAM_INT);
        $maxtime = optional_param('maxtime', time(), PARAM_INT);
        $mform->set_data(array('mintime' => $mintime, 'maxtime' => $maxtime));
    }
    echo $OUTPUT->header();

    // Form.
    $mform->display();

    echo $OUTPUT->box_start();

}

echo $OUTPUT->footer();
