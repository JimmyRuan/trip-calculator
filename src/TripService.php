<?php

namespace App;

Class TripService {
    protected $arguments;
    protected $storeName;

    /**
     * @var Trip;
     */
    protected $trip;

    public function __construct($storeName='store.json')
    {
        $this->storeName = $storeName;
        $this->setupStore();
    }

    public function getActionName()
    {
        if(count($this->arguments) < 2){
            die('Please specify arguments for the command');
        }

        $actionName = $this->arguments[1];

        //it is an input file
        if(count($this->arguments) == 2){
            return $actionName;
        }

        if(! $this->trip->isValidTripCommand($actionName)){
            die('Please use valid command');
        }

        return $actionName;
    }

    protected function setupStore()
    {
        if(!file_exists($this->storeName)){
           return '';
        }

        $content = file_get_contents($this->storeName);
        $decodedContent = json_decode($content, true);
        $this->trip = new Trip($decodedContent);

        return $this;
    }

    public function processCommand()
    {
        $actionName = $this->getActionName();

        switch ($actionName) {
            case Trip::ADD_DRIVER:
                $this->processAddDriverCommand();
                break;
            case Trip::ADD_TRIP:
                $this->processTripCommand();
                break;
            case Trip::CALCULATE:
                $this->trip->showAllResult();
                break;
            default:
                $this->processInputFile($actionName);
        }

        $this->clearInput();



        return $this;
    }

    public function getDriverName()
    {
        if(count($this->arguments) !== 3){
            die('Please specify the driver name ');
        }

        return $this->arguments[2];
    }

    public function validateTripCommandArguments($arguments) : ?TripDataTransferObject
    {
        //Trip Dan 07:15 07:45 17.3
        //  1  2     3     4     5
        $driverName = $arguments[2];
        $startTime = strtotime($arguments[3]);
        $endTime = strtotime($arguments[4]);
        $totalMilesDriven = floatval($arguments[5]);

        if($driverName && $startTime && $endTime && $totalMilesDriven){
            $totalHours = $this->getTotalHours($startTime, $endTime);
            $mph = round($totalMilesDriven)/$totalHours;
            if($mph >= 5 && $mph <= 100){
                return new TripDataTransferObject($driverName, $totalHours, $totalMilesDriven);
            }
        }

        return null;
    }

    protected function getTotalHours($startTimestamp, $endTimestamp) : float
    {
        return ($endTimestamp - $startTimestamp)/(60*60);
    }

    public function storeData()
    {
        file_put_contents($this->storeName, $this->trip->toJson());
    }

    public function clearStoreData()
    {
        file_put_contents($this->storeName, '');
        $this->trip = new Trip([]);
    }

    public function clearInput() : self
    {
        $this->arguments = null;
        return $this;
    }


    public function updateInput(array $arguments) : self
    {
        $this->arguments = $arguments;
        return $this;
    }

    protected function processAddDriverCommand(): void
    {
        $driverName = $this->getDriverName();
        $this->trip->addDriver($driverName);
        $this->storeData();
    }

    protected function processTripCommand(): void
    {
        if (count($this->arguments) !== 6) {
            die('Please specify "Trip" arguments');
        }

        $tripData = $this->validateTripCommandArguments($this->arguments);
        if (empty($tripData)) {
            die('The "Trip" arguments are invalid');
        }

        $this->trip->addTrip($tripData);
        $this->storeData();
    }

    protected function processInputFile($filePath)
    {
        if(! file_exists($filePath)){
            die('Cannot find the input file na');
        }

        $handle = fopen($filePath, "r");
        if ($handle) {
            $this->clearStoreData();
            while (($line = fgets($handle)) !== false) {
                // process the line read.
                $line = "oneFile " . $line;
                $arguments = explode(' ', $line);
                $this->updateInput($arguments)->processCommand();
            }

            $this->trip->showAllResult();

            fclose($handle);
        } else {
            die('Cannot read the input file');
        }
    }


}