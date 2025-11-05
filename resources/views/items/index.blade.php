<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Items Database') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('items.index') }}" class="space-y-4">
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

                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                Filter
                            </button>

                            @if ($filters['search'] || $filters['type'] || $filters['rarity'])
                                <a href="{{ route('items.index') }}"
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Items Grid --}}
            @if ($items->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">No items found.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($items as $item)
                        @php
                            $rarityColors = [
                                'Common' => 'border-gray-400',
                                'Uncommon' => 'border-green-400',
                                'Rare' => 'border-blue-400',
                                'Epic' => 'border-purple-400',
                                'Legendary' => 'border-yellow-400',
                            ];
                            $borderColor = $rarityColors[$item['rarity'] ?? ''] ?? 'border-gray-300';
                        @endphp

                        <a href="{{ route('items.show', $item['id']) }}"
                           class="bg-white border-2 {{ $borderColor }} rounded-lg overflow-hidden hover:shadow-lg transition-shadow block">
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
                                @if (!empty($item['type']))
                                    <p class="text-xs text-gray-400">{{ $item['type'] }}</p>
                                @endif
                                @if (!empty($item['value']))
                                    <p class="text-xs text-yellow-600 font-semibold mt-1">{{ $item['value'] }} credits</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-4 text-center text-gray-600">
                    <p>Showing {{ $items->count() }} items</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
