<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'local/chuspasocial:report' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW,
        ),
        'riskbitmask' => RISK_SPAM,
    ),
);
