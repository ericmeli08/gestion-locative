<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppartementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $appartementId = $this->route('appartement') ? $this->route('appartement')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:appartements,name,' . $appartementId
            ],
            'description' => 'nullable|string|max:1000',
            'address' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:50',
            'surface' => 'nullable|numeric|min:0|max:999999.99',
            'active' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('validation.appartement.name.required'),
            'name.unique' => __('validation.appartement.name.unique'),
            'name.max' => __('validation.appartement.name.max'),
            'description.max' => __('validation.appartement.description.max'),
            'address.required' => __('validation.appartement.address.required'),
            'address.max' => __('validation.appartement.address.max'),
            'capacity.required' => __('validation.appartement.capacity.required'),
            'capacity.integer' => __('validation.appartement.capacity.integer'),
            'capacity.min' => __('validation.appartement.capacity.min'),
            'capacity.max' => __('validation.appartement.capacity.max'),
            'surface.numeric' => __('validation.appartement.surface.numeric'),
            'surface.min' => __('validation.appartement.surface.min'),
            'surface.max' => __('validation.appartement.surface.max'),
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('active')) {
            $this->merge([
                'active' => $this->boolean('active')
            ]);
        }
    }
}
