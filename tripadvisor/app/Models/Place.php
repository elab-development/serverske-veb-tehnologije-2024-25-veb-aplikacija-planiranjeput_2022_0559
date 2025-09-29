<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    use HasFactory;    use HasFactory;

    protected $fillable = [
        'destination_id',
        'name',
        'type',
        'slug',
        'address',
        'latitude',
        'longitude',
        'price_level',
        'rating_avg',
        'reviews_count'
    ];

    public const TYPES = ['attraction', 'restaurant', 'hotel'];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}