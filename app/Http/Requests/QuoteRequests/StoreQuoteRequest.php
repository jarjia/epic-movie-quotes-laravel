<?php

namespace App\Http\Requests\QuoteRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class StoreQuoteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        App::setLocale($this->input('locale'));

        return [
            'quote' => 'required',
            'movieId' => 'required',
        ];
    }
}
