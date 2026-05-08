<?php

require_once ROOT . '/php/config-secret.php';

function db() : PDO {
    static $db = null;

    if ($db === null) {

        $db = new PDO(
            "mysql:host=" . DB_HOST . "; dbname=" . DB_NAME . "; charset=" . DB_CHAR, DB_USER, DB_PASS
        );
        
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    return $db;
}