<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'description',
        'type',
        'rarity',
        'value',
        'image_url',
        'weight_kg',
        'stack_size',
        'found_in',
        'effects',
        'recycles_into',
    ];

    protected $casts = [
        'effects' => 'array',
        'recycles_into' => 'array',
        'weight_kg' => 'decimal:2',
        'value' => 'integer',
        'stack_size' => 'integer',
    ];

    /**
     * Get the user inventories for this item.
     */
    public function userInventories(): HasMany
    {
        return $this->hasMany(UserInventory::class);
    }
}
