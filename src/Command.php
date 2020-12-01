<?php

use App\TripService;

require_once(__DIR__ . '/../vendor/autoload.php');

$tripService = new TripService();
$tripService->updateInput($argv)->processCommand();



die('I am done here');