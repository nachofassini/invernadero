<?php

namespace App\Rules;

use App\Models\Activation;
use Illuminate\Contracts\Validation\Rule;

class IsValidDevice implements Rule
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
        return in_array($device, Activation::DEVICES);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Device does not exists or is unavailable at this moment.';
    }
}
