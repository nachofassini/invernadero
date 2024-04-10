<?php

namespace App\Console\Commands;

use App\Console\Traits\HasActivationCause;
use App\Models\Activation;
use Ballen\GPIO\GPIO;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class Fan extends Command
{
    use HasActivationCause;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fan:switch
                                {cause : Activation cause}
                                {measureId? : Measure that triggered the activation}
                                {--turn=on : Turn Fan on or off} 
                                {--time=1 : Time the Fan will be on or off in minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch Fan on or off';

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

        // Configure our 'Fan' output...
        $fan = $gpio->pin(Activation::DEVICE_PINS['FAN'], GPIO::OUT);

        if ($turnOn) {
            $activation = Activation::create([
                'activated_by' => $this->getActivationCause(),
                'measure_id' => $this->getActivationMeasureId(),
                'device' => Activation::DEVICE_FAN,
                'amount' => $time,
                'measure_unit' => Activation::UNIT_MINUTES,
            ]);

            $fan->setValue(GPIO::HIGH);
            $this->info('Fan is on');
            logger('Turning on Fan for ' . $time . ' minutes');
            sleep($time * 60);
            $fan->setValue(GPIO::LOW);
            $this->info('Fan is off');
            logger('Turning off Fan');

            $activation->active_until = Date::now();
            $activation->save();
        } else {
            $fan->setValue(GPIO::LOW);
            $this->info('Fan is off');
            logger('Turning off Fan');
        }
    }
}
