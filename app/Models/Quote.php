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

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function scopeWithMoviesLike($query, $search)
    {
        return $query->whereHas('movies', function ($query) use ($search) {
            $query->whereRaw('LOWER(JSON_EXTRACT(movie, "$.en")) like ?', ['"%' . strtolower($search) . '%"'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(movie, "$.ka")) like ?', ['"%' . strtolower($search) . '%"']);
        });
    }

    public function scopeWithQuotesLike($query, $search)
    {
        return $query->orWhere(function ($query) use ($search) {
            $query->whereRaw('LOWER(JSON_EXTRACT(quote, "$.en")) like ?', ['"%' . strtolower($search) . '%"'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(quote, "$.ka")) like ?', ['"%' . strtolower($search) . '%"']);
        });
    }

    public static function searchQuotes($search, $paginate)
    {
        $search = trim($search);
        $searchString = trim(substr($search, 1));

        $query = self::query();

        if (Str::startsWith($search, '@')) {
            $query->withMoviesLike($searchString);
        } elseif (Str::startsWith($search, '#')) {
            $query->withQuotesLike($searchString);
        } else {
            $query->withMoviesLike($search)
                ->withQuotesLike($search);
        }

        $quotes = $query->offset(0)
            ->limit($paginate)
            ->orderBy('created_at', 'desc')
            ->get();

        return $quotes;
    }
}
