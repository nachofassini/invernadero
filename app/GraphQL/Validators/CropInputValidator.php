<?php

namespace App\GraphQL\Validators;

use App\Models\Crop;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

final class CropInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            "id" => [],
            "name" => ['string', 'min:3', 'max:50', Rule::unique(Crop::class, 'name')->ignore($this->arg('id'), 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Ya existe un cultivo con ese nombre.',
            'name.min' => 'Debe ser mayor a 3 caracteres.',
            'name.max' => 'Debe ser menor a 50 caracteres.',
        ];
    }
}
