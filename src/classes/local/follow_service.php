<?php
namespace local_chuspasocial\local;

defined('MOODLE_INTERNAL') || die();

use moodle_exception;

class follow_service {

    /**
     * Sigue a un usuario.
     *
     * @param int $userid ID del usuario que se quiere seguir.
     * @throws moodle_exception Si el usuario intenta seguirse a si mismo.
     */
    public static function follow_user(int $userid): void {
        global $DB, $USER;

        // No se permite seguirse a si mismo.
        if ($userid === (int) $USER->id) {
            throw new moodle_exception('cannotfollowself', 'local_chuspasocial');
        }

        // Verificar que el usuario a seguir exista.
        $userfollowed = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', MUST_EXIST);

        $params = [
            'userid'   => $USER->id,
            'itemtype' => 'user',
            'itemid'   => $userid,
        ];

        // No se permite seguir dos veces al mismo usuario.
        if (!$DB->record_exists('local_chuspasocial_follow', $params)) {
            $record = (object) $params;
            $record->timecreated = time();
            $DB->insert_record('local_chuspasocial_follow', $record);

            $message = new \core\message\message();
            $message->component         = 'local_chuspasocial';
            $message->name              = 'newfollower';
            $message->userfrom          = $USER;
            $message->userto            = $userfollowed;
            $message->subject           = get_string('newfollowersubject', 'local_chuspasocial');
            $message->fullmessage       = get_string('newfollowerbody', 'local_chuspasocial');
            $message->fullmessageformat = FORMAT_MARKDOWN;
            $message->fullmessagehtml   = '';
            $message->smallmessage      = get_string('newfollowersmall', 'local_chuspasocial');
            $message->notification      = 1;

            message_send($message);
        }
    }

    /**
     * Deja de seguir a un usuario.
     *
     * @param int $userid ID del usuario que se quiere dejar de seguir.
     */
    public static function unfollow_user(int $userid): void {
        global $DB, $USER;

        $DB->delete_records('local_chuspasocial_follow', [
            'userid'   => $USER->id,
            'itemtype' => 'user',
            'itemid'   => $userid,
        ]);
    }
}
