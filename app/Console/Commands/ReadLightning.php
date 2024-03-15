<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Volantus\MCP3008\Reader;
use Volantus\Pigpio\Client;
use Volantus\Pigpio\SPI\RegularSpiDevice;


class ReadLightning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:external:lightning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads external lightning sensor';

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

        // Reading value of ADC channel 1 and getting the raw value, e.g. 789.
        $lightning = $this->reader->read(1)->getRawValue();

        $minReading = 0; // Max reading of what we consider daylight
        $maxReading = 1023; // Max reading of what we consider night

        // Adjust the lightning reading to the range defined by the readings dictionary
        $adjustedLightning = max(min($lightning, $maxReading), $minReading);

        // Convert the adjusted lightning reading to a percentage
        $percentage = (1 - (($adjustedLightning - $minReading) / ($maxReading - $minReading))) * 100;

        $this->line(sprintf("%.2f%%", $percentage));
    }
}
