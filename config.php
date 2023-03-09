<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'chat-alpine-js');


$conn2 = new PDO("mysql:dbname=" . DB_NAME . ";host=" . DB_SERVER, DB_USERNAME, DB_PASSWORD);
$conn2->exec("set names utf8");

setlocale (LC_TIME, 'fr_FR.utf8','fra');
date_default_timezone_set('Europe/Paris');