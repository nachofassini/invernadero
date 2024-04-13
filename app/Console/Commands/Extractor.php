<?php

namespace App\Console\Commands;

use App\Console\Traits\HasActivationCause;
use App\Models\Activation;
use Ballen\GPIO\GPIO;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class Extractor extends Command
{
    use HasActivationCause;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extractor:switch 
                                {cause : Activation cause}
                                {measureId? : Measure that triggered the activation}
                                {--turn=on : Turn on or off} 
                                {--time=1 : Time the Extractor will be on or off in minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch Extractor on or off';

    /**
     * The device to command interacts with.
     *
     * @var string
     */
    public $device = Activation::DEVICE_EXTRACTOR;

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

        // Configure our 'Extractor' output...
        $extractor = $gpio->pin(Activation::DEVICE_PINS[$this->device], GPIO::OUT);

        if ($turnOn) {
            $activation = Activation::create([
                'activated_by' => $this->getActivationCause(),
                'measure_id' => $this->getActivationMeasureId(),
                'device' => $this->device,
                'amount' => $time,
                'measure_unit' => Activation::UNIT_MINUTES,
            ]);

            $extractor->setValue(GPIO::HIGH);
            $this->info('Extractor is on');
            logger('Turning on Extractor for ' . $time . ' minutes');
            sleep($time * 60);
            $extractor->setValue(GPIO::LOW);
            $this->info('Extractor is off');
            logger('Turning off Extractor');

            $activation->active_until = Date::now();
            $activation->save();
        } else {
            $extractor->setValue(GPIO::LOW);
            $this->info('Extractor is off');
            logger('Turning off Extractor');
        }
    }
}
