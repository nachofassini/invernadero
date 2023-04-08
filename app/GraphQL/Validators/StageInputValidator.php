<?php

namespace App\GraphQL\Validators;

use App\Models\Crop;
use App\Models\Stage;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

final class StageInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'cropId' => ['required', Rule::exists(Crop::class, 'id')],
            'id' => [fn () => $this->arg('id') ? Rule::exists(Crop::class, 'id') : ''],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique(Stage::class, 'name')
                    ->where(fn ($query) => $query->where('crop_id', $this->arg('cropId')))
                    ->ignore($this->arg('id'), 'id')
            ],
            'days' => ['required', 'integer', "min:1", "max:365"],
            'minTemperature' => ['required', 'decimal:0,2', "min:0", "max:50",  "lte:maxTemperature" ],
            'maxTemperature' => ['required', 'decimal:0,2', "min:0", "max:50",  "gte:minTemperature" ],
            'minHumidity' => ['required', 'decimal:0,2', "min:0", "max:100",  "lte:maxHumidity" ],
            'maxHumidity' => ['required', 'decimal:0,2', "min:0", "max:100",  "gte:minHumidity" ],
            'minCo2' => ['required', 'integer', "min:400", "max:1200",  "lte:maxCo2" ],
            'maxCo2' => ['required', 'integer', "min:400", "max:1200",  "gte:minCo2" ],
            'irrigation' => ['required', 'integer', "min:0", "max:2000"],
            'lightHours' => ['required', 'decimal:0,2', "min:0", "max:24"],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'data.name' => 'name',
        ];
    }

    public function messages(): array
    {
        return [
            'cropId.exists' => 'No se puedo asociar a un cultivo.',
            'name.unique' => 'El cultivo ya posee una etapa con ese nombre.',
            'name.min' => 'Debe ser mayor a 3 caracteres.',
            'name.max' => 'Debe ser menor a 50 caracteres.',
            'days.min' => 'Debe ser mayor a 1 día',
            'days.max' => 'Debe ser menor a 365 días.',
            "minTemperature.min" => "La temperatura minima debe ser entre 0º y 50º.",
            "minTemperature.max" => "La temperatura minima debe ser entre 0º y 50º.",
            "maxTemperature.min" => "La temperatura maxima debe ser entre 0º y 50º.",
            "maxTemperature.max" => "La temperatura maxima debe ser entre 0º y 50º.",
            "minTemperature.lt" => "Debe ser menor a la temperatura maxima.",
            "maxTemperature.gt" => "Debe ser mayor a la temperatura minima.",
            "minHumidity.min" => "La humedad minima debe ser entre 1% y 100%.",
            "minHumidity.max" => "La humedad minima debe ser entre 1% y 100%.",
            "maxHumidity.min" => "La humedad maxima debe ser entre 1% y 100%.",
            "maxHumidity.max" => "La humedad maxima debe ser entre 1% y 100%.",
            "minHumidity.lt" => "Debe ser menor a la humedad maxima.",
            "maxHumidity.gt" => "Debe ser mayor a la humedad minima.",
            "minCo2.min" => "La concentración de CO2 minima debe ser entre 400 y 1200ppm.",
            "minCo2.max" => "La concentración de CO2 minima debe ser entre 400 y 1200ppm",
            "maxCo2.min" => "La concentración de CO2 maxima debe ser entre 400 y 1200ppm.",
            "maxCo2.max" => "La concentración de CO2 maxima debe ser entre 400 y 1200ppm.",
            "minCo2.lt" => "Debe ser menor a la concentración de CO2 maxima.",
            "maxCo2.gt" => "Debe ser mayor a la concentración de CO2 minima.",
            "irrigation.min" => "El volumen de riego debe ser un valor entre 0 y 2000ppm.",
            "irrigation.max" => "El volumen de riego debe ser un valor entre 0 y 2000ppm.",
            "lightHours.min" => "Las horas de iluminación debe ser un valor entre 0 y 24.",
            "lightHours.max" => "Las horas de iluminación debe ser un valor entre 0 y 24.",
        ];
    }
}
