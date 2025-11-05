<?php

namespace App\Http\Controllers;

use App\Services\ItemService;
use App\Services\UserInventoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InventoryController extends Controller
{
    public function __construct(
        private ItemService $itemService,
        private UserInventoryService $inventoryService
    ) {}

    /**
     * Display user's inventory (grid view by default).
     */
    public function index(Request $request): View
    {
        $view = $request->get('view', 'grid'); // grid or list
        $search = $request->get('search');
        $type = $request->get('type');
        $rarity = $request->get('rarity');

        $inventory = $this->inventoryService->getUserInventory($request->user()->id);

        // Apply filters
        if ($search) {
            $inventory = $inventory->filter(function ($entry) use ($search) {
                $item = $entry['item'];
                $searchLower = strtolower($search);
                return str_contains(strtolower($item['name'] ?? ''), $searchLower) ||
                       str_contains(strtolower($item['description'] ?? ''), $searchLower);
            });
        }

        if ($type) {
            $inventory = $inventory->filter(function ($entry) use ($type) {
                return ($entry['item']['type'] ?? null) === $type;
            });
        }

        if ($rarity) {
            $inventory = $inventory->filter(function ($entry) use ($rarity) {
                return ($entry['item']['rarity'] ?? null) === $rarity;
            });
        }

        $types = $this->itemService->getTypes();
        $rarities = $this->itemService->getRarities();

        return view('inventory.index', [
            'inventory' => $inventory,
            'view' => $view,
            'types' => $types,
            'rarities' => $rarities,
            'filters' => [
                'search' => $search,
                'type' => $type,
                'rarity' => $rarity,
            ]
        ]);
    }

    /**
     * Show the form to add items to inventory.
     */
    public function add(Request $request): View
    {
        $search = $request->get('search');
        $type = $request->get('type');

        $items = $this->itemService->all();

        // Apply filters
        if ($search) {
            $items = $this->itemService->search($search);
        }

        if ($type) {
            $items = $this->itemService->filterByType($type);
        }

        $types = $this->itemService->getTypes();
        $userInventory = $this->inventoryService->getUserInventory($request->user()->id)
            ->pluck('quantity', 'item_id');

        return view('inventory.add', [
            'items' => $items,
            'types' => $types,
            'userInventory' => $userInventory,
            'filters' => [
                'search' => $search,
                'type' => $type,
            ]
        ]);
    }

    /**
     * Update item quantity in inventory.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'item_id' => 'required|string',
            'quantity' => 'required|integer|min:0',
        ]);

        $this->inventoryService->updateItemQuantity(
            $request->user()->id,
            $request->item_id,
            $request->quantity
        );

        return back()->with('success', 'Item quantity updated successfully!');
    }

    /**
     * Increment item quantity.
     */
    public function increment(Request $request, string $itemId): RedirectResponse
    {
        $amount = $request->get('amount', 1);

        $this->inventoryService->incrementItem(
            $request->user()->id,
            $itemId,
            $amount
        );

        return back()->with('success', 'Item added to inventory!');
    }

    /**
     * Decrement item quantity.
     */
    public function decrement(Request $request, string $itemId): RedirectResponse
    {
        $amount = $request->get('amount', 1);

        $this->inventoryService->decrementItem(
            $request->user()->id,
            $itemId,
            $amount
        );

        return back()->with('success', 'Item quantity decreased!');
    }

    /**
     * Remove item from inventory.
     */
    public function destroy(string $itemId): RedirectResponse
    {
        $this->inventoryService->removeItem(auth()->id(), $itemId);

        return back()->with('success', 'Item removed from inventory!');
    }
}
