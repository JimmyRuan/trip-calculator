<?php

namespace App;

Class Trip {

    protected $store;

    public const ADD_DRIVER= 'Driver';
    public const ADD_TRIP = 'Trip';
    public const CALCULATE = 'Calculate';
    public const RESET_STORE = 'Reset';

    public function __construct(?array $store)
    {
        $this->store = $store ?? [];
    }

    public function addDriver($driverName) : self
    {
        if(! isset($this->store['drivers']) ){
            $this->store['drivers'] = [];
        }

        if(! in_array($driverName, $this->store['drivers'])){
            $this->store['drivers'][] = trim($driverName);
        }

        return $this;
    }

    public function addTrip(TripDataTransferObject $dataTransferObject) : self
    {
        if(! isset($this->store['trips'])){
            $this->store['trips'] = [];
        }

        if(! isset($this->store['trips'][$dataTransferObject->driverName])){
            $this->store['trips'][$dataTransferObject->driverName] = [];
        }

        $this->store['trips'][$dataTransferObject->driverName][] = [
            'total_hours' => $dataTransferObject->totalHours,
            'total_miles' => $dataTransferObject->totalMilesDriven
        ];

        return $this;
    }

    public function isValidCommand($actionName)
    {
        return in_array($actionName, $this->availableCommands());
    }

    public function availableCommands()
    {
        return [
            self::ADD_DRIVER,
            self::ADD_TRIP,
            self::CALCULATE,
            self::RESET_STORE,
        ];
    }

    public function toArray() : array
    {
        return $this->store;
    }

    public function toJson() : string
    {
        return json_encode($this->store, JSON_PRETTY_PRINT);
    }

    protected static function printResult(TripResultDataTransferObject $result)
    {
        if($result->totalMilesDriven === 0 || $result->mph === 0){
            print("$result->driverName: $result->totalMilesDriven miles\n");
        }else{
            print("$result->driverName: $result->totalMilesDriven miles @ $result->mph mph\n");
        }
    }

    public static function printAllResult(array $results)
    {
        foreach($results as $result){
            self::printResult($result);
        }
    }

    protected function getDriveTrip($driverName, $totalMiles, $mph)
    {
        return new TripResultDataTransferObject($driverName, $mph, $totalMiles);
    }

    public function extractResults(): array
    {
        if (!isset($this->store['trips']) ||
            empty($this->store['trips']) ||
            !is_array($this->store['trips'])) {
            die('There is no trips specified so far');
        }

        $result = [];

        foreach ($this->store['drivers'] as $driverName) {
            $totalTime = 0;
            $totalMiles = 0;
            $trips = $this->store['trips'][$driverName] ?? null;

            if (!$trips) {
                $result[] = $this->getDriveTrip($driverName, 0, 0);

                continue;
            }

            foreach ($trips as $trip) {
                $totalTime = $totalTime + $trip['total_hours'];
                $totalMiles = round($totalMiles + $trip['total_miles']);
            }

            $mph = round($totalMiles / $totalTime);
            $result[] = $this->getDriveTrip($driverName, $totalMiles, $mph);
        }


        if (!empty($result)) {
            usort($result, function (TripResultDataTransferObject $left, TripResultDataTransferObject $right) {
                return $left->totalMilesDriven > $right->totalMilesDriven ? -1 : 1;
            });
        }
        return $result;
    }
}