<?php

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
$PAGE->set_context($context);

$url = new moodle_url('/local/xereta/index.php');
$PAGE->set_url($url);
$html = '';

if (is_siteadmin($USER->id)) {





}

echo $OUTPUT->header();
echo $html;
echo $OUTPUT->footer();
