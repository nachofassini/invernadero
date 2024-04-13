<?php

namespace App\Console\Commands;

use App\Console\Traits\HasActivationCause;
use App\Models\Activation;
use Ballen\GPIO\GPIO;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class Led extends Command
{
    use HasActivationCause;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'led:switch 
                                {cause : Activation cause}
                                {measureId? : Measure that triggered the activation}
                                {--turn=on : Turn Water on or off} 
                                {--time=1 : Time the LED will be on or off in minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch Led light on or off';

    /**
     * The device to command interacts with.
     *
     * @var string
     */
    public $device = Activation::DEVICE_LIGHT;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $turnOn = $this->option('turn') === 'on';
        $time = $this->option('time');

        // Create a new instance of the GPIO class.
        $gpio = new GPIO();

        // Configure our 'LED' output...
        $led = $gpio->pin(Activation::DEVICE_PINS[$this->device], GPIO::OUT);

        if ($turnOn) {
            $activation = Activation::create([
                'activated_by' => $this->getActivationCause(),
                'measure_id' => $this->getActivationMeasureId(),
                'device' => $this->device,
                'amount' => $time,
                'measure_unit' => Activation::UNIT_MINUTES,
            ]);

            $led->setValue(GPIO::HIGH);
            $this->info('Led is on');
            logger('Turning on led for ' . $time . ' minutes');
            sleep($time * 60);
            $led->setValue(GPIO::LOW);
            $this->info('Led is off');
            logger('Turning off led');

            $activation->active_until = Date::now();
            $activation->save();
        } else {
            $led->setValue(GPIO::LOW);
            $this->info('Led is off');
            logger('Turning off led');
        }
    }
}
