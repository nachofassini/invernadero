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
        /* $spiInterface = new RegularSpiDevice(new Client(), 1, 32000);
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
        } */

        $measure = new Measure();

        // 'inside_temperature' => 'double', => DONE
        // 'outside_temperature' => 'double', => DONE
        // 'inside_humidity' => 'double', => DONE
        // 'outside_humidity' => 'double', => DONE
        // 'soil_humidity' => 'double',
        // 'co2' => 'integer',
        // 'lighting' => 'double',

        $output = new BufferedOutput();
        $this->runCommand('read:external:temp-and-humidity', [], $output);
        preg_match('/Temp=([\-0-9.]+)\*C\s+Humidity=([0-9.]+)%/', $output->fetch(), $matches);
        $measure->outside_temperature = $matches[1];
        $measure->outside_humidity = $matches[2];

        $output = new BufferedOutput();
        $this->runCommand('read:internal:temp-and-humidity', [], $output);
        preg_match('/Temp=([\-0-9.]+)\*C\s+Humidity=([0-9.]+)%/', $output->fetch(), $matches);
        $measure->inside_temperature = $matches[1];
        $measure->inside_humidity = $matches[2];

        logger($measure->toArray());
    }
}
