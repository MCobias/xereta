<?php

require_once(__DIR__ . '/../../config.php');

global $DB, $PAGE, $OUTPUT, $USER;

require_login();

require_once('xereta_lib.php');

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

    // Url with params for links inside tables.
    $url->params(array(
        'mintime' => $mintime,
        'maxtime' => $maxtime
    ));

    // Object to store view data.
    $view = new stdClass();
    $view->header = array();

    $tablestyles = local_xereta_utils::get_table_styles();
    $view->table = new html_table();
    $view->table->attributes = array('class' => $tablestyles['table_class']);

    echo $OUTPUT->header();

    // Form.
    $mform->display();

    echo $OUTPUT->box_start();






    // Format table headers if they exists.
    if (!empty($view->table->head)) {
        $headers = array();
        foreach ($view->table->head as $header) {
            $cell = new html_table_cell($header);
            $cell->style = $tablestyles['header_style'];
            $headers[] = $cell;
        }
        $view->table->head = $headers;
    }
    echo html_writer::table($view->table);

    // END PAGE.
    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();
