<?php

namespace App\Rules;

use App\Models\Activation;
use Illuminate\Contracts\Validation\Rule;

class DeviceIsNotActive implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $device
     * @return bool
     */
    public function passes($attribute, $device)
    {
        return Activation::where('device', $device)->whereNull('active_until')->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Device is already active.';
    }
}
