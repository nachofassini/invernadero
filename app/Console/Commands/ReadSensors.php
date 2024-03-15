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
        $spiInterface = new RegularSpiDevice(new Client(), 1, 32000);
        $this->reader = new Reader($spiInterface, 3.3);

        while (true) {
            // Reading value of ADC channel 4
            $tempRoof = $this->reader->read(0);
            //            $potRoof = $this->reader->read(7);
            // Getting the raw value, e.g. 789
            $temp = $tempRoof->getRawValue();
            //            $pot = $potRoof->getRawValue();

            $this->line($temp);
            //            $this->line($pot);

            // Wait for 0,1 second...
            usleep(1000000);

            // Execute python scripts and get return value
            // $command = escapeshellcmd('/usr/bin/python3 /path/to/your/script.py');
            // $output = shell_exec($command);
            // echo $output;
        }
    }
}
