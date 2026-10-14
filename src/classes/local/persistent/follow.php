<?php
namespace local_chuspasocial\local\persistent;

defined('MOODLE_INTERNAL') || die();

class follow extends \core\persistent {
    const TABLE = 'local_chuspasocial_follow';

    protected static function define_properties() {
        return [
            'id' => [
                'type' => \PARAM_INT,
                'null' => \NULL_NOT_ALLOWED,
            ],
            'user_id' => [
                'type' => \PARAM_INT,
                'null' => \NULL_NOT_ALLOWED,
            ],
            'following_id' => [
                'type' => \PARAM_INT,
                'null' => \NULL_NOT_ALLOWED,
            ],
            'status' => [
                'type' => \PARAM_ALPHA,
                'null' => \NULL_NOT_ALLOWED,
                'default' => 'active',
                'choices' => ['active', 'inactive', 'blocked'],
            ],
            'timecreated' => [
                'type' => \PARAM_INT,
                'null' => \NULL_NOT_ALLOWED,
            ],
        ];
    }
}
