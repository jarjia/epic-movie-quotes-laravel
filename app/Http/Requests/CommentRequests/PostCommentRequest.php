<?php

namespace App\Http\Requests\CommentRequests;

use Illuminate\Foundation\Http\FormRequest;

class PostCommentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'comment' => 'required',
            'quote_id' => 'required',
            'to_user' => 'required'
        ];
    }
}
