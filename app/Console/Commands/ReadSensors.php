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
        $measure = new Measure();

        // 'inside_temperature' => 'double', => DONE
        // 'outside_temperature' => 'double', => DONE
        // 'inside_humidity' => 'double', => DONE
        // 'outside_humidity' => 'double', => DONE
        // 'soil_humidity' => 'double',
        // 'co2' => 'integer',
        // 'lighting' => 'double',

        // external temperature and humidity
        $output = new BufferedOutput();
        $this->runCommand('read:external:lightning', [], $output);
        preg_match('/([0-9.]+)%/', $output->fetch(), $matches);
        $measure->lighting = $matches[1];

        // external temperature and humidity
        $output = new BufferedOutput();
        $this->runCommand('read:external:temp-and-humidity', [], $output);
        preg_match('/Temp=([\-0-9.]+)\*C\s+Humidity=([0-9.]+)%/', $output->fetch(), $matches);
        $measure->outside_temperature = $matches[1] ?? 0;
        $measure->outside_humidity = $matches[2] ?? 0;

        // internal temperature and humidity
        $output = new BufferedOutput();
        $this->runCommand('read:internal:temp-and-humidity', [], $output);
        preg_match('/Temp=([\-0-9.]+)\*C\s+Humidity=([0-9.]+)%/', $output->fetch(), $matches);
        $measure->inside_temperature = $matches[1] ?? 0;
        $measure->inside_humidity = $matches[2] ?? 0;

        // soil moisture
        $output = new BufferedOutput();
        $this->runCommand('read:internal:soil-moisture', [], $output);
        preg_match('/([0-9.]+)%/', $output->fetch(), $matches);
        $measure->soil_humidity = $matches[1];

        logger($measure->toArray());
    }
}
