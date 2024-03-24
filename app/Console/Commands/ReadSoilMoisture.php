<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Volantus\MCP3008\Reader;
use Volantus\Pigpio\Client;
use Volantus\Pigpio\SPI\RegularSpiDevice;


class ReadSoilMoisture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:internal:soil-moisture';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads greenhouse soil moisture sensor';

    /**
     * @var Reader
     */
    protected Reader $reader;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $spiInterface = new RegularSpiDevice(new Client(), 1, 32000);
        $this->reader = new Reader($spiInterface, 3.3);

        // Reading value of ADC channel 0 and getting the raw value, e.g. 789.
        $soilMoisture = $this->reader->read(0)->getRawValue();

        // Readings dictionary
        // Dry in open air = 950
        // Dry soil needing water
        // Ideal soil moisture
        // Just watered soil
        // In cup of water = 350
        // Define the minimum and maximum readings according to the readings dictionary
        $minReading = 350; // In cup of water
        $maxReading = 950; // Max reading of what we consider too dry

        // Adjust the soil moisture reading to the range defined by the readings dictionary
        $adjustedSoilMoisture = max(min($soilMoisture, $maxReading), $minReading);

        // Convert the adjusted soil moisture reading to a percentage
        $percentage = (1 - (($adjustedSoilMoisture - $minReading) / ($maxReading - $minReading))) * 100;

        $this->line(sprintf("%.2f%%", $percentage));
    }
}
