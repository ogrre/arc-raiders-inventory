<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    private ?Collection $items = null;

    /**
     * Get all items from the JSON file.
     */
    public function all(): Collection
    {
        if ($this->items === null) {
            $this->loadItems();
        }

        return $this->items;
    }

    /**
     * Find an item by its game ID.
     */
    public function find(string $gameId): ?array
    {
        return $this->all()->firstWhere('id', $gameId);
    }

    /**
     * Search items by name or description.
     */
    public function search(string $query): Collection
    {
        $query = strtolower($query);

        return $this->all()->filter(function ($item) use ($query) {
            return str_contains(strtolower($item['name'] ?? ''), $query) ||
                   str_contains(strtolower($item['description'] ?? ''), $query);
        });
    }

    /**
     * Filter items by type.
     */
    public function filterByType(?string $type): Collection
    {
        if (!$type) {
            return $this->all();
        }

        return $this->all()->filter(function ($item) use ($type) {
            return ($item['type'] ?? null) === $type;
        });
    }

    /**
     * Filter items by rarity.
     */
    public function filterByRarity(?string $rarity): Collection
    {
        if (!$rarity) {
            return $this->all();
        }

        return $this->all()->filter(function ($item) use ($rarity) {
            return ($item['rarity'] ?? null) === $rarity;
        });
    }

    /**
     * Get all unique types.
     */
    public function getTypes(): Collection
    {
        return $this->all()
            ->pluck('type')
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Get all unique rarities.
     */
    public function getRarities(): Collection
    {
        return $this->all()
            ->pluck('rarity')
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Load items from JSON file.
     */
    private function loadItems(): void
    {
        $jsonPath = storage_path('app/arc-data/items.json');

        if (!file_exists($jsonPath)) {
            $this->items = collect([]);
            return;
        }

        $content = file_get_contents($jsonPath);
        $data = json_decode($content, true);

        $this->items = collect($data ?? []);
    }

    /**
     * Get items that recycle into a specific material.
     */
    public function getItemsThatRecycleInto(string $materialId): Collection
    {
        return $this->all()->filter(function ($item) use ($materialId) {
            $recyclesInto = $item['recyclesInto'] ?? [];
            return array_key_exists($materialId, $recyclesInto);
        });
    }
}
