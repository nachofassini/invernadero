<?php

namespace App\Console\Commands;

use App\Console\Traits\HasActivationCause;
use App\Models\Activation;
use Ballen\GPIO\GPIO;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class Water extends Command
{
    use HasActivationCause;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'water:switch 
                                {cause : Activation cause}
                                {measureId? : Measure that triggered the activation}
                                {--turn=on : Turn Water on or off} 
                                {--time=1 : Time the Water will be on or off in minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch Water Pump on or off';

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

        // Configure our 'Water' output...
        $water = $gpio->pin(Activation::DEVICE_PINS['WATER'], GPIO::OUT);

        if ($turnOn) {
            $activation = Activation::create([
                'activated_by' => $this->getActivationCause(),
                'measure_id' => $this->getActivationMeasureId(),
                'device' => Activation::DEVICE_WATER,
                'amount' => $time,
                'measure_unit' => Activation::UNIT_MINUTES,
            ]);

            $water->setValue(GPIO::HIGH);
            $this->info('Water is on');
            logger('Turning on Water for ' . $time . ' minutes');
            sleep($time * 60);
            $water->setValue(GPIO::LOW);
            $this->info('Water is off');
            logger('Turning off Water');

            $activation->active_until = Date::now();
            $activation->save();
        } else {
            $water->setValue(GPIO::LOW);
            $this->info('Water is off');
            logger('Turning off Water');
        }
    }
}
