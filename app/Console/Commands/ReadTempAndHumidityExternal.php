<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class ReadTempAndHumidityExternal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:external:temp-and-humidity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads external temperature and humidity sensor';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $process = new Process(['/usr/bin/python3', __DIR__ . '/lib/dht-22-external.py']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $this->line('External: ' . $output);
        // do not output anything else to console
        // $this->output->writeln($output);
    }
}
