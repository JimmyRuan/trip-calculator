<?php

namespace App;

class TripDataTransferObject {
    /**
     * @var string
     */
    public $driverName;

    /**
     * @var float
     */
    public $totalHours;

    /**
     * @var float
     */
    public $totalMilesDriven;

    public function __construct($driverName, $totalHours, $totalMilesDriven)
    {
        $this->driverName = $driverName;
        $this->totalHours = $totalHours;
        $this->totalMilesDriven = $totalMilesDriven;
    }
}