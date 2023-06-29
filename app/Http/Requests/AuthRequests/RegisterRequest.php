<?php

namespace App\Http\Requests\AuthRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $locale = $this->input('locale');

        App::setLocale($locale);

        return [
            'name'      => 'required|unique:users,name',
            'email'     => 'required|unique:users,email',
            'password'  => 'required',
            'locale' => 'required'
        ];
    }
}
