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
        $this->driverName = $driverName;
        $this->mph = $mph;
        $this->totalMilesDriven = $totalMilesDriven;
    }
}