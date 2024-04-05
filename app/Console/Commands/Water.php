<?php

namespace App\Console\Commands;

use Ballen\GPIO\GPIO;
use Illuminate\Console\Command;

class Water extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Water:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test turning on water pump';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Create a new instance of the GPIO class.
        $gpio = new GPIO();

        // Configure our 'Water' output...
        $led = $gpio->pin(26, GPIO::OUT);

        // Create a basic loop that runs continuously...
        while (true) {
            // Turn the LED on...
            $led->setValue(GPIO::HIGH);
            // Wait for one second...
            sleep(2);
            // Turn off the LED...
            $led->setValue(GPIO::LOW);
            // Wait for one second...
            sleep(3);
        }
    }
}