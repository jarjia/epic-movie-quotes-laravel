<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function accessQuote(User $user, Quote $quote)
    {
        return $user->id === $quote->movies->user_id;
    }
}
