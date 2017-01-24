<?php
use Ox\DataBase\DbConfig;

$config = include("migrations-db.php");

DbConfig::$dbhost = $config["host"];
DbConfig::$dbname = $config["dbname"];
DbConfig::$dbuser = $config["user"];
DbConfig::$dbuserpass = $config["password"];
