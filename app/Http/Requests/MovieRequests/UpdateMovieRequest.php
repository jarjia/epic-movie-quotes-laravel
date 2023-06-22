<?php

namespace App\Http\Requests\MovieRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class UpdateMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        App::setLocale($this->input('locale'));

        return [
            'movie' => 'required',
            'director' => 'required',
            'description' => 'required',
            'releaseDate' => 'required',
            'genres' => 'required',
            'locale' => 'required'
        ];
    }
}
