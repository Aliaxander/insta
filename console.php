#!/usr/bin/env php
<?php
// application.php

require __DIR__ . '/vendor/autoload.php';

use Acme\Console\Command\CronCommands;
use Acme\Console\Command\InstallCommands;
use Acme\Console\Command\InstallCourcesCommands;
use Acme\Console\Command\InstallDocs;
use Acme\Console\Command\InstallNewCourceActionsCommands;
use Acme\Console\Command\InstallUsers;
use Acme\Console\Command\OffersPhotosCommands;
use Acme\Console\Command\Test;
use Acme\Console\Command\TestDocs;
use Acme\Console\Command\UserPhotosCommands;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new Test());
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
