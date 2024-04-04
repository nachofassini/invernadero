<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use PiPHP\GPIO\GPIO;
use PiPHP\GPIO\Pin\PinInterface;

class Led2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'led2:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test turning on a Led';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Create a new instance of the GPIO class.
        $gpio = new GPIO();

        // Configure our 'LED' output...
        $led = $gpio->getOutputPin(23);

        // Create a basic loop that runs continuously...
        while (true) {
            // Turn the LED on...
            $led->setValue(PinInterface::VALUE_HIGH);
            // Wait for one second...
            sleep(1);
            // Turn off the LED...
            $led->setValue(PinInterface::VALUE_LOW);
            // Wait for one second...
            sleep(1);
        }
    }
}
