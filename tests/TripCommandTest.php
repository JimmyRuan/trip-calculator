<?php

namespace Tests;

use App\TripService;
use PHPUnit\Framework\TestCase;

class TripCommandTest extends TestCase
{
    /**
     * @var TripService;
     */
    protected $tripService;
    public function setUp(): void
    {
        parent::setUp();
        $this->tripService = new TripService(__DIR__ .'/testing.json');
        $this->tripService->clearStoreData();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->tripService->clearStoreData();
    }

    public function testAddDrivers(): void
    {
        $driverNames = ['Dan', 'Lauren', 'Kumi'];
        foreach ($driverNames as $driverName) {
            $argv = ['src/Command.php', 'Driver', $driverName];
            $this->tripService->updateInput($argv)->processCommand();
        }

        $actualResult = $this->tripService->getStoreData();

        $this->assertEquals($driverNames, $actualResult['drivers']);
    }

    public function testAddTrips()
    {
        $driverNames = ['Dan', 'Lauren', 'Kumi'];
        $commandFile = 'src/Command.php';
        foreach ($driverNames as $driverName) {
            $inputArr = [$commandFile, 'Driver', $driverName];
            $this->tripService->updateInput($inputArr)->processCommand();
        }

        $tripCommands = [
            [$commandFile, 'Trip', 'Dan', '07:15', '07:45', '17.3'],
            [$commandFile, 'Trip', 'Dan', '06:12', '06:32', '21.8'],
            [$commandFile, 'Trip', 'Lauren', '12:01', '13:16', '42.0']
        ];

        foreach ($tripCommands as $tripCommand) {
            $this->tripService->updateInput($tripCommand)->processCommand();
        }

        $actualResult = $this->tripService->getStoreData();
        $expectedTrips = [
            'Dan' => [
                [
                    "total_hours" => 0.5,
                    "total_miles" => 17.3
                ],
                [
                    "total_hours" => 0.3333333333333333,
                    "total_miles" => 21.8
                ]
            ],
            'Lauren' => [
                [
                    "total_hours" => 1.25,
                    "total_miles" => 42
                ]
            ]
        ];

        $calculatedResults = $this->tripService->getCalculatedResults();

        $this->assertEquals($driverNames, $actualResult['drivers']);
        $this->assertEquals($expectedTrips, $actualResult['trips']);
        $this->assertCalculatedResults($calculatedResults);
    }

    public function testUsingInputFile()
    {
        $this->tripService->processInputFile(__DIR__ .'/sampleInputFixture');

        $calculatedResults = $this->tripService->getCalculatedResults();

        $this->assertCalculatedResults($calculatedResults);
    }

    public function assertCalculatedResults(array $calculatedResults): void
    {
        $this->assertEquals([
            'driverName' => 'Lauren',
            'mph' => 34.0,
            'totalMilesDriven' => 42.0
        ], $calculatedResults[0]->toArray());

        $this->assertEquals([
            'driverName' => 'Dan',
            'mph' => 47.0,
            'totalMilesDriven' => 39.0
        ], $calculatedResults[1]->toArray());

        $this->assertEquals([
            'driverName' => 'Kumi',
            'mph' => 0,
            'totalMilesDriven' => 0
        ], $calculatedResults[2]->toArray());
    }

}