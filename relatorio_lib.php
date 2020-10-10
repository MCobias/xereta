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

/**
 * Params.
 */
class local_relatorio_manager {
    protected $course;
    protected $mintime;
    protected $maxtime;

    public function __construct($course, $mintime, $maxtime) {
        $this->course = $course;
        $this->mintime = $mintime;
        $this->maxtime = $maxtime;
    }

    public function get_user_dedication($user)
    {
        $where = 'courseid = :courseid AND userid = :userid AND timecreated >= :mintime AND timecreated <= :maxtime';
        $params = array(
            'courseid' => $this->course->id,
            'userid' => $user->id,
            'mintime' => $this->mintime,
            'maxtime' => $this->maxtime
        );
        $logs = local_relatorio_utils::get_events_select($where, $params);
        // Return user sessions with details.
        $rows = array();
        if ($logs) {
            foreach ($logs as $log) {
                $rows[] = (object)array('start_date' => $log->time, 'ips' => array($log->ip));
            }
        }
        return $rows;
    }
}

/**
 * Utils functions.
 */
class local_relatorio_utils {
    public static $logstores = array('logstore_standard', 'logstore_legacy');
    /**
     * Return formatted events from logstores.
     * @param string $selectwhere
     * @param array $params
     * @return array
     */
    public static function get_events_select($selectwhere, array $params) {
        $return = array();

        static $allreaders = null;

        if (is_null($allreaders)) {
            $allreaders = get_log_manager()->get_readers();
        }

        $processedreaders = 0;

        foreach (self::$logstores as $name) {
            if (isset($allreaders[$name])) {
                $reader = $allreaders[$name];
                $events = $reader->get_events_select($selectwhere, $params, 'timecreated ASC', 0, 0);
                foreach ($events as $event) {
                    // Note: see \core\event\base to view base class of event.
                    $obj = new stdClass();
                    $obj->time = $event->timecreated;
                    $obj->ip = $event->get_logextra()['ip'];
                    $return[] = $obj;
                }
                if (!empty($events)) {
                    $processedreaders++;
                }
            }
        }

        // Sort mixed array by time ascending again only when more of a reader has added events to return array.
        if ($processedreaders > 1) {
            usort($return, function($a, $b) {
                return $a->time > $b->time;
            });
        }

        return $return;
    }

    /**
     * @param string[] $ips
     * @return string
     */
    public static function format_ips($ips) {
        return implode(', ', array_map('local_relatorio_utils::link_ip', $ips));
    }

    /**
     * Generates a linkable ip.
     * @param string $ip
     * @return string
     */
    public static function link_ip($ip) {
        return html_writer::link("http://en.utrace.de/?query=$ip", $ip, array('target' => '_blank'));
    }

    /**
     * Return table styles based on current theme.
     * @return array
     */
    public static function get_table_styles() {
        return array(
            'table_class' => 'table table-bordered table-hover table-sm table-condensed table-dedication',
            'header_style' => 'background-color: #302E51; color: #fff;'
        );
    }
}
