<?php

namespace App\Console\Commands\Sensors;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class ReadTempAndHumidityInternal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:internal:temp-and-humidity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads internal temperature and humidity sensor';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $process = new Process(['/usr/bin/python3', __DIR__ . '/lib/dht-22-internal.py']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $this->line('Internal: ' . $output);
        // do not output anything else to console
        // $this->output->writeln($output);
    }
}
