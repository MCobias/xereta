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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');

// Form to select start and end date ranges and session time.
class relatorio_local_selection_form extends moodleform {

    public function definition() {
        global $DB;
        $mform = & $this->_form;

        $mform->addElement('html', html_writer::tag('h2', get_string('title', 'local_relatorio')));

        $mform->addElement('html', html_writer::tag('p', get_string('formtext', 'local_relatorio')));

        $allusers = $DB->get_records('user');
        $options = array();
        foreach ($allusers as $user) {
                $options[$user->id] = $user->firstname;
        }
        $mform->addElement('select', 'userid', get_string('nameusers', 'local_relatorio'), $options);

        $mform->addElement('date_time_selector', 'mintime', get_string('mintime', 'local_relatorio'));
        $mform->addHelpButton('mintime', 'mintime', 'local_relatorio');

        $mform->addElement('date_time_selector', 'maxtime', get_string('maxtime', 'local_relatorio'));
        $mform->addHelpButton('maxtime', 'maxtime', 'local_relatorio');

        // Buttons.
        $this->add_action_buttons(false, get_string('submit', 'local_relatorio'));
    }

}
