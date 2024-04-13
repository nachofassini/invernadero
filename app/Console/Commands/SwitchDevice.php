<?php

namespace App\Console\Commands;

use App\Console\Traits\HasActivationCause;
use App\Models\Activation;
use Ballen\GPIO\GPIO;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class SwitchDevice extends Command
{
    use HasActivationCause;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'device:switch 
                                {device : Device to switch}
                                {cause : Activation cause}
                                {measureId? : Measure that triggered the activation}
                                {--turn=on : Turn on or off} 
                                {--time=1 : Time the Extractor will be on or off in minutes}';

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
        $time = $this->option('time');

        $devicePin = Activation::DEVICE_PINS[$deviceName];

        if (!$devicePin) {
            return Command::FAILURE;
        }

        // Create a new instance of the GPIO class.
        $gpio = new GPIO();

        // Configure our device output...
        $device = $gpio->pin($devicePin, GPIO::OUT);

        if ($turnOn) {
            $activation = Activation::create([
                'activated_by' => $this->getActivationCause(),
                'measure_id' => $this->getActivationMeasureId(),
                'device' => $deviceName,
                'amount' => $time,
                'measure_unit' => Activation::UNIT_MINUTES,
            ]);


            logger("Turning on $deviceName for: $time minutes");
            $device->setValue(GPIO::LOW); // as the relay is active low
            $this->info("$deviceName is on");
            sleep($time * 60);
            logger("Turning off $deviceName");
            $device->setValue(GPIO::HIGH);
            $this->info("$deviceName is off");

            $activation->active_until = Date::now();
            $activation->save();
        } else {
            logger("Turning off $deviceName");
            $device->setValue(GPIO::HIGH);
            $this->info("$deviceName is off");
        }

        return Command::SUCCESS;
    }
}
