<?php

namespace App\Http\Requests\QuoteRequests;

use Illuminate\Foundation\Http\FormRequest;

class AllQuotesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable',
            'paginate' => 'required',
            'locale' => 'required'
        ];
    }
}
