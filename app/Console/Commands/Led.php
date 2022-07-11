<?php

namespace App\Console\Commands;

use Ballen\GPIO\GPIO;
use Illuminate\Console\Command;

class Led extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'led:test';

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
        $led = $gpio->pin(23, GPIO::OUT);

        // Create a basic loop that runs continuously...
        while (true) {
            // Turn the LED on...
            $led->setValue(GPIO::HIGH);
            // Wait for one second...
            sleep(1);
            // Turn off the LED...
            $led->setValue(GPIO::LOW);
            // Wait for one second...
            sleep(1);
        }
    }
}
