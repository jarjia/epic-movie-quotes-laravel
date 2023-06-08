<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie',
        'director',
        'description',
        'releaseDate',
        'thumbnail',
        'user_id'
    ];

    /**
     * The attributes that are casted as array.
     *
     * @var array
     */
    protected $casts = ['movie' => 'array', 'director' => 'array', 'description' => 'array'];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['movie', 'director', 'description'];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'genre_movie');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
