<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;

class MoviePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function accessMovie(User $user, Movie $movie)
    {
        return $user->id === $movie->user_id;
    }
}
