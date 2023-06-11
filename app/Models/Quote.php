<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'quote',
        'thumbnail'
    ];

    /**
     * The attributes that are casted as array.
     *
     * @var array
     */
    protected $casts = ['quote' => 'array'];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['quote'];

    public function movies(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }
}
