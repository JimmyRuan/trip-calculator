<?php

namespace App;

class TripResultDataTransferObject {
    /**
     * @var string
     */
    public $driverName;

    /**
     * @var float
     */
    public $mph;

    /**
     * @var float
     */
    public $totalMilesDriven;

    public function __construct($driverName, $mph, $totalMilesDriven)
    {
        $this->driverName = trim($driverName);
        $this->mph = $mph;
        $this->totalMilesDriven = $totalMilesDriven;
    }

    public function toArray()
    {
        return [
            'driverName' => $this->driverName,
            'mph' => $this->mph,
            'totalMilesDriven' => $this->totalMilesDriven
        ];
    }
}