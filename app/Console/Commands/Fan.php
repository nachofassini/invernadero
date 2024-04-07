<?php

namespace App\Console\Commands;

use App\Models\Activation;
use Ballen\GPIO\GPIO;
use Illuminate\Console\Command;

class Fan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Fan:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test turning on Fan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Create a new instance of the GPIO class.
        $gpio = new GPIO();

        // Configure our 'Fan' output...
        $led = $gpio->pin(Activation::DEVICE_PINS['FAN'], GPIO::OUT);

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
