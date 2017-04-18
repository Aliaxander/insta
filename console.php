#!/usr/bin/env php
<?php
// application.php
use Acme\Console\Command\BaseUploader;
use Acme\Console\Command\EditProfile;
use Acme\Console\Command\FreenomReger;
use Acme\Console\Command\Likes;
use Acme\Console\Command\Test;
use Ox\DataBase\DbConfig;
use Symfony\Component\Console\Application;


require __DIR__ . '/vendor/autoload.php';
$config = include("migrations-db.php");
DbConfig::$dbhost = $config["host"];
DbConfig::$dbname = $config["dbname"];
DbConfig::$dbuser = $config["user"];
DbConfig::$dbuserpass = $config["password"];


$application = new Application();
$application->add(new Test());
$application->add(new Likes());
$application->add(new EditProfile());
$application->add(new FreenomReger());
$application->add(new BaseUploader());
$application->addCommands(
    array(
        // Migrations Commands
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand(),
    )
);
$application->run();
