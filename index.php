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
        $userid = $formdata->userid;
    } else {
        // Params from request or default values.
        $mintime = optional_param('mintime', time(), PARAM_INT);
        $maxtime = optional_param('maxtime', time(), PARAM_INT);
        $userid = optional_param('userid', 0, PARAM_INT);
        $mform->set_data(array('mintime' => $mintime, 'maxtime' => $maxtime));
    }

    echo $OUTPUT->header();

    // Form.
    $mform->display();
    echo $OUTPUT->box_start();
    $courses = enrol_get_users_courses($userid, true);

    foreach ($courses as $course) {
        echo html_writer::tag('h4','DISCIPLINA: ' . $course->fullname, array('class' => 'titlecourse'));
        // Object to store view data.
        $view = new stdClass();
        $view->header = array();

        $tablestyles = local_xereta_utils::get_table_styles();
        $view->table = new html_table();
        $view->table->attributes = array('class' => $tablestyles['table_class']);

        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        $course = $DB->get_record("course", array("id" => $course->id), '*', MUST_EXIST);

        $dm = new local_xereta_manager($course, $mintime, $maxtime);

        // Table formatting & total count.
        $rows = $dm->get_user_dedication($user);

        foreach ($rows as $index => $row) {
            $rows[$index] = array(
                userdate($row->start_date),
                local_xereta_utils::format_ips($row->ips)
            );
        }

        $rows = array_map('json_encode', $rows);
        $rows = array_unique($rows);
        $rows = array_map('json_decode', $rows);

        $view->table->head = array(get_string('sessionstart', 'local_xereta'), 'IP');
        $view->table->data = $rows;

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
    }
    // END PAGE.
    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();
