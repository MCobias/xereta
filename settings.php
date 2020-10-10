<?php

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $pagename = new lang_string('pluginname', 'local_xereta');
    $ADMIN->add('localplugins', new admin_externalpage('local_xereta',
                                $pagename,
                                new moodle_url('/local/xereta/index.php')));
}
