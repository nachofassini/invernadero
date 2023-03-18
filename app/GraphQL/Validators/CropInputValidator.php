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
            'active' => [
                'boolean',
                Rule::unique(Crop::class, 'active')
                    ->where(fn ($query) => $query->when(
                        $this->arg('active') === true,
                        function ($query, $active) {
                            $query->where('active', 1)->where('id', '!=', $this->arg('id'));
                        }
                    ))
                    ->ignore($this->arg('id'), 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Ya existe un cultivo con ese nombre.',
            'name.min' => 'Debe ser mayor a 3 caracteres.',
            'name.max' => 'Debe ser menor a 50 caracteres.',
            'active.unique' => 'Ya existe un cultivo activo. No se puede activar.'
        ];
    }
}
