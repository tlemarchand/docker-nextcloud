<?php

$autoconfig_enabled = false;

if (getenv('MYSQL_DATABASE') && getenv('MYSQL_USER') && getenv('MYSQL_PASSWORD') && getenv('MYSQL_HOST')) {
    $AUTOCONFIG["dbtype"] = "mysql";
    $AUTOCONFIG["dbname"] = getenv('MYSQL_DATABASE');
    $AUTOCONFIG["dbuser"] = getenv('MYSQL_USER');
    $AUTOCONFIG["dbpass"] = getenv('MYSQL_PASSWORD');
    $AUTOCONFIG["dbhost"] = getenv('MYSQL_HOST');
    $autoconfig_enabled = true;
} 
if ($autoconfig_enabled) {
    if (getenv('NEXTCLOUD_TABLE_PREFIX')) {
        $AUTOCONFIG["dbtableprefix"] = getenv('NEXTCLOUD_TABLE_PREFIX');
    }

    $AUTOCONFIG["directory"] = getenv('NEXTCLOUD_DATA_DIR') ?: "/var/www/data";
}
