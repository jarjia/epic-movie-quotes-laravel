<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user',
        'to_user',
        'quote_id',
        'friend_id',
        'notification',
        'seen',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user');
    }

    public function quotes(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    public function from(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user');
    }
}
