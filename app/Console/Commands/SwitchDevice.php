<?php

namespace App\Console\Commands;

use App\Models\Activation;
use Ballen\GPIO\GPIO;
use Illuminate\Console\Command;

class SwitchDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'device:switch {device : Device to switch} {--turn=on : Turn device on or off}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch devices on or off';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $deviceName = $this->argument('device');
        $turnOn = $this->option('turn') === 'on';

        $devicePin = Activation::DEVICE_PINS[$deviceName];

        if (!$devicePin) {
            return Command::FAILURE;
        }

        // Create a new instance of the GPIO class.
        $gpio = new GPIO();

        // Configure our device output...
        $device = $gpio->pin($devicePin, GPIO::OUT);

        if ($turnOn) {
            logger("Turning on $deviceName");
            $device->setValue(GPIO::LOW); // as the relay is active low
            $this->info("$deviceName is on");
        } else {
            logger("Turning off $deviceName");
            $device->setValue(GPIO::HIGH);
            $this->info("$deviceName is off");
        }

        return Command::SUCCESS;
    }
}
