<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTreeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Or add auth check
    }

    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('trees', 'id') // Ensure unique ID from React Native
            ],
            'description' => 'required|string|max:2000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'status' => 'required|string|in:active,inactive,removed',
            'is_synced' => 'boolean',
            'type' => 'required|string|max:100',
            'image_url' => 'required|string|url|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'id.unique' => 'This tree ID already exists in the database',
            'latitude.between' => 'Latitude must be between -90 and 90',
            'image_url.url' => 'Image URL must be a valid URL',
        ];
    }

    protected function prepareForValidation(): void
    {
      
        
        // Ensure is_synced is true for mobile-synced data
        $this->merge(['is_synced' => true]);
    }
}