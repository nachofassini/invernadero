<?php

namespace App\Console\Commands;

use App\Models\Measure;
use Illuminate\Console\Command;
use Volantus\MCP3008\Reader;
use Volantus\Pigpio\Client;
use Volantus\Pigpio\SPI\RegularSpiDevice;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


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

        // Reading value of ADC channel 4
        $tempRoof = $this->reader->read(0);
        //            $potRoof = $this->reader->read(7);
        // Getting the raw value, e.g. 789
        $temp = $tempRoof->getRawValue();
        //            $pot = $potRoof->getRawValue();

        $this->line($temp);;
    }
}
