<?php
namespace local_chuspasocial\local;

defined('MOODLE_INTERNAL') || die();

use moodle_exception;

class follow_service {

    public static function follow_user(int $userfollowedid): void {
        global $DB, $USER;

        if ($userfollowedid === (int) $USER->id) {
            throw new moodle_exception('cannotfollowself', 'local_chuspasocial');
        }

        $params = [
            'userid'       => $USER->id,
            'userfollowed' => $userfollowedid,
            'itemtype'     => 'user'
        ];

        if (!$DB->record_exists('local_chuspasocial_follows', $params)) {
            $record = (object)$params;
            $record->timecreated = time();
            $DB->insert_record('local_chuspasocial_follows', $record);

            $userfollowed = $DB->get_record('user', ['id' => $userfollowedid], '*', MUST_EXIST);

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
     * Convierte registros de usuario en arreglos con nombre y foto.
     *
     * @param array $records Registros de usuario obtenidos de la base de datos.
     * @return array Lista de usuarios con id, fullname y profileimageurl.
     */
    private static function format_users(array $records): array {
        global $PAGE;

        $users = [];
        foreach ($records as $user) {
            $userpicture = new \user_picture($user);
            $userpicture->size = 1;

            $users[] = [
                'id'              => (int) $user->id,
                'fullname'        => fullname($user),
                'profileimageurl' => $userpicture->get_url($PAGE)->out(false),
            ];
        }
        return $users;
    }

    /**
     * Obtiene los seguidores de un usuario, paginados.
     *
     * @param int $userid ID del usuario del que se quieren ver los seguidores.
     * @param int $page Numero de pagina, empezando en 0.
     * @param int $perpage Cantidad de usuarios por pagina.
     * @return array Lista de usuarios con id, fullname y profileimageurl.
     */
    public static function get_followers(int $userid, int $page, int $perpage): array {
        global $DB;

        $sql = "SELECT u.id, u.firstname, u.lastname, u.firstnamephonetic,
                       u.lastnamephonetic, u.middlename, u.alternatename,
                       u.picture, u.imagealt, u.email
                  FROM {local_chuspasocial_follow} f
                  JOIN {user} u ON u.id = f.userid
                 WHERE f.itemtype = :itemtype
                   AND f.itemid = :itemid
                   AND u.deleted = 0
              ORDER BY f.id DESC";

        $params = [
            'itemtype' => 'user',
            'itemid'   => $userid,
        ];

        $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

        return self::format_users($records);
    }

    /**
     * Obtiene los usuarios que sigue un usuario, paginados.
     *
     * @param int $userid ID del usuario del que se quieren ver los seguidos.
     * @param int $page Numero de pagina, empezando en 0.
     * @param int $perpage Cantidad de usuarios por pagina.
     * @return array Lista de usuarios con id, fullname y profileimageurl.
     */
    public static function get_following(int $userid, int $page, int $perpage): array {
        global $DB;

        $sql = "SELECT u.id, u.firstname, u.lastname, u.firstnamephonetic,
                       u.lastnamephonetic, u.middlename, u.alternatename,
                       u.picture, u.imagealt, u.email
                  FROM {local_chuspasocial_follow} f
                  JOIN {user} u ON u.id = f.itemid
                 WHERE f.itemtype = :itemtype
                   AND f.userid = :userid
                   AND u.deleted = 0
              ORDER BY f.id DESC";

        $params = [
            'itemtype' => 'user',
            'userid'   => $userid,
        ];

        $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

        return self::format_users($records);
    }
}
