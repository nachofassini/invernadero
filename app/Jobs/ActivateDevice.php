<?php

namespace App\Jobs;

use App\Console\Commands\SwitchDevice;
use App\Models\Activation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class ActivateDevice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $deviceName;
    private $cause;
    private $time;
    private $measureId;

    /**
     * Create a new job instance.
     * @param string $deviceName
     * @param string $cause
     * @param float $time
     * @param int|null $measureId
     * @return void
     */
    public function __construct($deviceName, $cause, $time, $measureId = null)
    {
        $this->deviceName = $deviceName;
        $this->cause = $cause;
        $this->time = $time;
        $this->measureId = $measureId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // TODO: If device is already active because of a weather correction, it should not create an activation record and the deactivate job should be delayed by the new time
        // if (Activation::where('device', $this->deviceName)->whereNotNull('measure_id')->active()->exists()) {}

        $activation = Activation::create([
            'activated_by' => $this->cause,
            'measure_id' => $this->measureId,
            'device' => $this->deviceName,
            'amount' => $this->time,
            'measure_unit' => Activation::UNIT_MINUTES,
        ]);

        Artisan::call(SwitchDevice::class, ['device' => $activation->device, '--turn' => 'on']);

        DeactivateDevice::dispatch($activation)->delay(now()->addSeconds($this->time * 60));
    }
}
