<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonListRequest extends FormRequest
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
            // ponytail: exists was on the whole array (errors landed on `order`, not
            // per index); moved to `order.*` so a bad id reports as `order.0` etc.
            'order' => ['required', 'array', 'distinct'],
            'order.*' => ['exists:App\Models\Person,id'],
        ];
    }
}
