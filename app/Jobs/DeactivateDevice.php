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

class DeactivateDevice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The activation instance.
     *
     * @var \App\Models\Activation
     */
    public $activation;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Activation
     * @return void
     */
    public function __construct(Activation $activation)
    {
        $this->activation = $activation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->activation->deactivate();
    }
}
