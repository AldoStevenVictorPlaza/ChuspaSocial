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

/**
 * Event observers of ChuspaSocial.
 *
 * @package    local_chuspasocial
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_chuspasocial\local;

use moodle_url;

/**
 * Event observers of ChuspaSocial, registered in db/events.php.
 *
 * @package    local_chuspasocial
 */
class observer {
    /**
     * Create an activity post in the wall of the course when a new assignment is created.
     *
     * The post links to the assignment. Nothing is created when the new module is not
     * an assignment, when the assignment is hidden, or when the post already exists.
     *
     * @param \core\event\course_module_created $event The event.
     * @return void
     */
    public static function assign_created(\core\event\course_module_created $event): void {
        global $DB;

        if (($event->other['modulename'] ?? '') !== 'assign') {
            return;
        }

        $cmid = (int) $event->objectid;
        $cm = $DB->get_record('course_modules', ['id' => $cmid], 'id, course, visible');
        if (!$cm || empty($cm->visible)) {
            return;
        }

        $url = new moodle_url('/mod/assign/view.php', ['id' => $cmid]);
        $link = \html_writer::link($url, s($event->other['name'] ?? ''));

        // Only one post per assignment: the link to the assignment is unique.
        $like = $DB->sql_like('content', ':link');
        $exists = $DB->record_exists_select(
            'local_chuspasocial_post',
            "courseid = :courseid AND type = :type AND $like",
            [
                'courseid' => (int) $cm->course,
                'type' => 'activity',
                'link' => '%' . $DB->sql_like_escape($url->out(false) . '"') . '%',
            ]
        );
        if ($exists) {
            return;
        }

        $now = time();
        $DB->insert_record('local_chuspasocial_post', (object) [
            'userid' => (int) $event->userid,
            'courseid' => (int) $cm->course,
            'type' => 'activity',
            'content' => $link,
            'contentformat' => FORMAT_HTML,
            'hidden' => 0,
            'timecreated' => $now,
            'timemodified' => $now,
        ]);
    }
}
