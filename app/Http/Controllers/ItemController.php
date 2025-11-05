<?php

namespace App\Http\Controllers;

use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        private ItemService $itemService
    ) {}

    /**
     * Display all items with search and filters.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $type = $request->get('type');
        $rarity = $request->get('rarity');

        $items = $this->itemService->all();

        // Apply filters
        if ($search) {
            $items = $this->itemService->search($search);
        }

        if ($type) {
            $items = $this->itemService->filterByType($type);
        }

        if ($rarity) {
            $items = $this->itemService->filterByRarity($rarity);
        }

        $types = $this->itemService->getTypes();
        $rarities = $this->itemService->getRarities();

        return view('items.index', [
            'items' => $items,
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
     * Show item details.
     */
    public function show(string $gameId): View
    {
        $item = $this->itemService->find($gameId);

        if (!$item) {
            abort(404, 'Item not found');
        }

        // Find items that recycle into this material
        $recycledFrom = $this->itemService->getItemsThatRecycleInto($gameId);

        return view('items.show', [
            'item' => $item,
            'recycledFrom' => $recycledFrom,
        ]);
    }
}
