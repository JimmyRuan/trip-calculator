<?php

require_once(__DIR__ . '/../vendor/autoload.php');

if(count($argv) <= 1){
    die('Please specify arguments for the command');
}

if(count($argv) >= 2){
    $firstArgument = $argv[1];
}

$t = time();
$storeFileName = 'store.json';
//$store =fopen($storeFileName,'r+');

//$fileSize = filesize($storeFileName);
//$fileSize = $fileSize >= 1 ? $fileSize : 1;
//$content = fread($store, $fileSize);
$content = file_get_contents($storeFileName);
$storeJson = [];
if(filesize($storeFileName) >= 1){
    $storeJson = json_decode($content, true);
}

if(! in_array($firstArgument, ['Driver', 'Trip'])){
    die('Please use either "Driver" or "Trip" command');
}


if($firstArgument == 'Driver'){
    if(count($argv) !== 3){
        die('Please specify the driver name ' . count($argv));
    }

    $driverName = $argv[2];
    if(! isset($storeJson['drivers']) ){
        $storeJson['drivers'] = [];
    }

    if(! in_array($driverName, $storeJson['drivers'])){
        $storeJson['drivers'][] = $driverName;
        //die('I am here at 52');
    }
}

if($firstArgument == 'Trip'){
    if(count($argv) !== 6){
        die('Please specify "Trip" arguments');
    }
    //Trip Dan 07:15 07:45 17.3
    //  1  2     3     4     5
    $driverName = $argv[2];
    $startTime = $argv[3];
    $endTime = $argv[4];
    $totalMilesDriven = $argv[5];

    if(! isset($storeJson['drivers']) ){
        $storeJson['drivers'] = [];
    }

    if(! in_array($driverName, $storeJson['drivers'])){
       die("Please register the driver \"$driverName\" first");
    }

    if(! isset($storeJson['trips'])){
        $storeJson['trips'] = [];
    }

    $totalHours =  (strtotime($endTime) - strtotime($startTime))/(60*60);

    if(! isset($storeJson['trips'][$driverName])){
        $storeJson['trips'][$driverName] = [];
    }

    $storeJson['trips'][$driverName][] = [
        'total_hours' => $totalHours,
        'total_miles' => $totalMilesDriven
    ];
}

$storeJsonEncoded = json_encode($storeJson, JSON_PRETTY_PRINT);


file_put_contents($storeFileName, $storeJsonEncoded);
//fwrite($store,$storeJsonEncoded);
//fclose($store);


if(count($argv) == 1 && !empty($storeJson)){

}



//
//
//
//$newContent = "\n" . $content . '-bananana';
//
//fwrite($store,'new content: sssss' . date("Y-m-d", $t));
//fclose($store);
//
//
//
//
//var_dump($argv);