<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Inventory') }}
            </h2>
            <a href="{{ route('inventory.add') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Items
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Inventory Statistics --}}
            @if ($inventory->isNotEmpty())
                @php
                    $totalValue = $inventory->sum(function($entry) {
                        return ($entry['item']['value'] ?? 0) * $entry['quantity'];
                    });
                    $totalWeight = $inventory->sum(function($entry) {
                        return ($entry['item']['weightKg'] ?? 0) * $entry['quantity'];
                    });
                    $uniqueItems = $inventory->count();
                    $totalQuantity = $inventory->sum('quantity');
                @endphp

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-blue-700">{{ $uniqueItems }}</div>
                        <div class="text-sm text-blue-600">Unique Items</div>
                    </div>
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-green-700">{{ $totalQuantity }}</div>
                        <div class="text-sm text-green-600">Total Quantity</div>
                    </div>
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-yellow-700">{{ number_format($totalValue) }}</div>
                        <div class="text-sm text-yellow-600">Total Value (credits)</div>
                    </div>
                    <div class="bg-purple-50 border-2 border-purple-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-purple-700">{{ number_format($totalWeight, 1) }}</div>
                        <div class="text-sm text-purple-600">Total Weight (kg)</div>
                    </div>
                </div>
            @endif

            {{-- Filters and View Toggle --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('inventory.index') }}" class="space-y-4">
                        <div class="flex flex-wrap gap-4 items-end">
                            {{-- Search --}}
                            <div class="flex-1 min-w-[200px]">
                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                <input type="text"
                                       name="search"
                                       id="search"
                                       value="{{ $filters['search'] ?? '' }}"
                                       placeholder="Search items..."
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            {{-- Type Filter --}}
                            <div class="flex-1 min-w-[200px]">
                                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                <select name="type"
                                        id="type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Types</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type }}" {{ ($filters['type'] ?? '') === $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Rarity Filter --}}
                            <div class="flex-1 min-w-[200px]">
                                <label for="rarity" class="block text-sm font-medium text-gray-700">Rarity</label>
                                <select name="rarity"
                                        id="rarity"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Rarities</option>
                                    @foreach ($rarities as $rarity)
                                        <option value="{{ $rarity }}" {{ ($filters['rarity'] ?? '') === $rarity ? 'selected' : '' }}>
                                            {{ $rarity }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- View Toggle --}}
                            <div class="flex-1 min-w-[150px]">
                                <label for="view" class="block text-sm font-medium text-gray-700">View</label>
                                <select name="view"
                                        id="view"
                                        onchange="this.form.submit()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="grid" {{ $view === 'grid' ? 'selected' : '' }}>Grid</option>
                                    <option value="list" {{ $view === 'list' ? 'selected' : '' }}>List</option>
                                </select>
                            </div>

                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                Filter
                            </button>

                            @if ($filters['search'] || $filters['type'] || $filters['rarity'])
                                <a href="{{ route('inventory.index', ['view' => $view]) }}"
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Inventory Display --}}
            @if ($inventory->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">Your inventory is empty.</p>
                        <a href="{{ route('inventory.add') }}" class="text-blue-500 hover:text-blue-700 underline mt-2 inline-block">
                            Add your first items
                        </a>
                    </div>
                </div>
            @else
                @if ($view === 'grid')
                    {{-- Grid View --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach ($inventory as $entry)
                            @php
                                $item = $entry['item'];
                                $rarityColors = [
                                    'Common' => 'border-gray-400',
                                    'Uncommon' => 'border-green-400',
                                    'Rare' => 'border-blue-400',
                                    'Epic' => 'border-purple-400',
                                    'Legendary' => 'border-yellow-400',
                                ];
                                $borderColor = $rarityColors[$item['rarity'] ?? ''] ?? 'border-gray-300';
                            @endphp

                            <div class="bg-white border-2 {{ $borderColor }} rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                <a href="{{ route('items.show', $item['id']) }}" class="block">
                                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                                        @if (!empty($item['imageFilename']))
                                            <img src="{{ $item['imageFilename'] }}"
                                                 alt="{{ $item['name'] }}"
                                                 class="w-full h-full object-contain">
                                        @else
                                            <span class="text-gray-400 text-4xl">?</span>
                                        @endif
                                    </div>
                                    <div class="p-2">
                                        <h3 class="font-semibold text-sm truncate" title="{{ $item['name'] }}">
                                            {{ $item['name'] }}
                                        </h3>
                                        @if (!empty($item['rarity']))
                                            <p class="text-xs text-gray-500">{{ $item['rarity'] }}</p>
                                        @endif
                                    </div>
                                </a>
                                <div class="px-2 pb-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <form method="POST" action="{{ route('inventory.decrement', $item['id']) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold px-2 py-1 rounded text-sm">
                                                -
                                            </button>
                                        </form>

                                        <span class="font-bold text-lg">{{ $entry['quantity'] }}</span>

                                        <form method="POST" action="{{ route('inventory.increment', $item['id']) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold px-2 py-1 rounded text-sm">
                                                +
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- List View --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rarity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($inventory as $entry)
                                    @php $item = $entry['item']; @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if (!empty($item['imageFilename']))
                                                        <img src="{{ $item['imageFilename'] }}"
                                                             alt="{{ $item['name'] }}"
                                                             class="h-10 w-10 object-contain">
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <a href="{{ route('items.show', $item['id']) }}"
                                                       class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                                        {{ $item['name'] }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item['type'] ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item['rarity'] ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item['value'] ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <strong>{{ $entry['quantity'] }}</strong>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <form method="POST" action="{{ route('inventory.decrement', $item['id']) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold px-3 py-1 rounded">
                                                    -
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('inventory.increment', $item['id']) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold px-3 py-1 rounded">
                                                    +
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('inventory.destroy', $item['id']) }}" class="inline" onsubmit="return confirm('Remove this item from inventory?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold px-3 py-1 rounded">
                                                    Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="mt-4 text-center text-gray-600">
                    <p>Total items: {{ $inventory->count() }} | Total quantity: {{ $inventory->sum('quantity') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
