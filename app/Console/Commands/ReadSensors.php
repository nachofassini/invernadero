<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Volantus\MCP3008\Reader;
use Volantus\Pigpio\Client;
use Volantus\Pigpio\SPI\RegularSpiDevice;


class ReadSensors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sensors:read';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads greenhouse environment sensors';

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
        $spiInterface = new RegularSpiDevice(new Client(), 1, 32000, 0);
        $this->reader = new Reader($spiInterface, 3.3);
        
        // Reading value of ADC channel 4
        $tempRoof = $this->reader->read(4);
        // Getting the raw value, e.g. 789
        $temp = $tempRoof->getRawValue();

        return $temp;
    }
}
