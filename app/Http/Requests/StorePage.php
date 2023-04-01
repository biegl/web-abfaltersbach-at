<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePage extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'seitentitel' => 'required|max:255',
            'inhalt' => '',
        ];
    }
}
