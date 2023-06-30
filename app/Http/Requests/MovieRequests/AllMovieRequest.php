<?php

namespace App\Http\Requests\MovieRequests;

use Illuminate\Foundation\Http\FormRequest;

class AllMovieRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'locale' => 'required',
            'search' => 'nullable'
        ];
    }
}
