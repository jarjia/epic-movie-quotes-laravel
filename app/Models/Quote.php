<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public static function searchQuotes($search, $paginate)
    {
        $search = trim($search);
        $searchString = substr($search, 1);

        if (Str::startsWith($search, '@')) {
            return self::whereHas('movies', function ($query) use ($searchString) {
                $query->where('movie->en', 'like', $searchString . '%')
                    ->orWhere('movie->ka', 'like', $searchString . '%');
            })->offset(0)->limit($paginate)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif (Str::startsWith($search, '#')) {
            return self::where(function ($query) use ($searchString) {
                $query->where('quote->en', 'like', '%' . $searchString . '%')
                    ->orWhere('quote->ka', 'like', '%' . $searchString . '%');
            })->offset(0)
                ->limit($paginate)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            return self::whereHas('movies', function ($query) use ($search) {
                $query->where('movie->en', 'like', $search . '%')
                    ->orWhere('movie->ka', 'like', $search . '%');
            })->orWhere('quote->en', 'like', '%' . $search . '%')
                ->orWhere('quote->ka', 'like', '%' . $search . '%')
                ->offset(0)
                ->limit($paginate)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }
}
