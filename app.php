#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use App\ExpenseTracker;
use App\Utility\Utility;
use Symfony\Component\Console\Application;

$application = new Application();
$utility = new Utility();
$application->add(new ExpenseTracker($utility));

$application->run();