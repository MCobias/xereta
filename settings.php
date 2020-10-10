<?php

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $pagename = new lang_string('pluginname', 'local_relatorio');
    $ADMIN->add('localplugins', new admin_externalpage('local_relatorio',
                                $pagename,
                                new moodle_url('/local/relatorio/index.php')));
}
