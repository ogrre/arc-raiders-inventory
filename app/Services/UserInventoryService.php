<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class UserInventoryService
{
    private ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * Get user's inventory with item details.
     */
    public function getUserInventory(int $userId): Collection
    {
        $inventory = $this->loadUserInventory($userId);

        return $inventory->map(function ($entry) {
            $item = $this->itemService->find($entry['item_id']);
            return [
                'item_id' => $entry['item_id'],
                'quantity' => $entry['quantity'],
                'item' => $item,
            ];
        })->filter(function ($entry) {
            return $entry['item'] !== null;
        });
    }

    /**
     * Add or update an item in user's inventory.
     */
    public function updateItemQuantity(int $userId, string $itemId, int $quantity): void
    {
        $inventory = $this->loadUserInventory($userId);

        $existingIndex = $inventory->search(function ($entry) use ($itemId) {
            return $entry['item_id'] === $itemId;
        });

        if ($quantity <= 0) {
            // Remove item if quantity is 0 or less
            if ($existingIndex !== false) {
                $inventory->forget($existingIndex);
            }
        } else {
            // Update or add item
            if ($existingIndex !== false) {
                $inventory[$existingIndex]['quantity'] = $quantity;
            } else {
                $inventory->push([
                    'item_id' => $itemId,
                    'quantity' => $quantity,
                ]);
            }
        }

        $this->saveUserInventory($userId, $inventory->values());
    }

    /**
     * Increment item quantity in user's inventory.
     */
    public function incrementItem(int $userId, string $itemId, int $amount = 1): void
    {
        $inventory = $this->loadUserInventory($userId);
        $existingIndex = $inventory->search(function ($entry) use ($itemId) {
            return $entry['item_id'] === $itemId;
        });

        if ($existingIndex !== false) {
            $newQuantity = $inventory[$existingIndex]['quantity'] + $amount;
            $inventory[$existingIndex]['quantity'] = max(0, $newQuantity);
        } else {
            $inventory->push([
                'item_id' => $itemId,
                'quantity' => max(0, $amount),
            ]);
        }

        $this->saveUserInventory($userId, $inventory->values());
    }

    /**
     * Decrement item quantity in user's inventory.
     */
    public function decrementItem(int $userId, string $itemId, int $amount = 1): void
    {
        $this->incrementItem($userId, $itemId, -$amount);
    }

    /**
     * Remove an item from user's inventory.
     */
    public function removeItem(int $userId, string $itemId): void
    {
        $this->updateItemQuantity($userId, $itemId, 0);
    }

    /**
     * Get total item count in user's inventory.
     */
    public function getTotalItemCount(int $userId): int
    {
        return $this->loadUserInventory($userId)->count();
    }

    /**
     * Get total quantity of all items.
     */
    public function getTotalQuantity(int $userId): int
    {
        return $this->loadUserInventory($userId)->sum('quantity');
    }

    /**
     * Check if user has an item.
     */
    public function hasItem(int $userId, string $itemId): bool
    {
        $inventory = $this->loadUserInventory($userId);
        return $inventory->contains(function ($entry) use ($itemId) {
            return $entry['item_id'] === $itemId && $entry['quantity'] > 0;
        });
    }

    /**
     * Get item quantity in user's inventory.
     */
    public function getItemQuantity(int $userId, string $itemId): int
    {
        $inventory = $this->loadUserInventory($userId);
        $entry = $inventory->firstWhere('item_id', $itemId);
        return $entry['quantity'] ?? 0;
    }

    /**
     * Load user inventory from JSON file.
     */
    private function loadUserInventory(int $userId): Collection
    {
        $path = $this->getUserInventoryPath($userId);

        if (!Storage::exists($path)) {
            return collect([]);
        }

        $content = Storage::get($path);
        $data = json_decode($content, true);

        return collect($data ?? []);
    }

    /**
     * Save user inventory to JSON file.
     */
    private function saveUserInventory(int $userId, Collection $inventory): void
    {
        $path = $this->getUserInventoryPath($userId);
        Storage::put($path, json_encode($inventory->toArray(), JSON_PRETTY_PRINT));
    }

    /**
     * Get user inventory file path.
     */
    private function getUserInventoryPath(int $userId): string
    {
        return "user-inventories/{$userId}.json";
    }

    /**
     * Clear user's entire inventory.
     */
    public function clearInventory(int $userId): void
    {
        $path = $this->getUserInventoryPath($userId);
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
