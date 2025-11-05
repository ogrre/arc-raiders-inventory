<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $item['name'] }}
            </h2>
            <a href="{{ route('items.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Database
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Item Image and Basic Info --}}
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="aspect-square bg-gray-100 flex items-center justify-center mb-4 rounded">
                            @if (!empty($item['imageFilename']))
                                <img src="{{ $item['imageFilename'] }}"
                                     alt="{{ $item['name'] }}"
                                     class="w-full h-full object-contain">
                            @else
                                <span class="text-gray-400 text-6xl">?</span>
                            @endif
                        </div>

                        <h3 class="text-2xl font-bold mb-2">{{ $item['name'] }}</h3>

                        @if (!empty($item['rarity']))
                            <p class="text-sm mb-1">
                                <span class="font-semibold">Rarity:</span>
                                <span class="px-2 py-1 rounded text-sm
                                    @if($item['rarity'] === 'Common') bg-gray-200
                                    @elseif($item['rarity'] === 'Uncommon') bg-green-200
                                    @elseif($item['rarity'] === 'Rare') bg-blue-200
                                    @elseif($item['rarity'] === 'Epic') bg-purple-200
                                    @elseif($item['rarity'] === 'Legendary') bg-yellow-200
                                    @endif">
                                    {{ $item['rarity'] }}
                                </span>
                            </p>
                        @endif

                        @if (!empty($item['type']))
                            <p class="text-sm mb-1"><span class="font-semibold">Type:</span> {{ $item['type'] }}</p>
                        @endif

                        @if (!empty($item['value']))
                            <p class="text-sm mb-1"><span class="font-semibold">Value:</span> {{ $item['value'] }} credits</p>
                        @endif

                        @if (!empty($item['weightKg']))
                            <p class="text-sm mb-1"><span class="font-semibold">Weight:</span> {{ $item['weightKg'] }} kg</p>
                        @endif

                        @if (!empty($item['stackSize']))
                            <p class="text-sm mb-1"><span class="font-semibold">Stack Size:</span> {{ $item['stackSize'] }}</p>
                        @endif
                    </div>
                </div>

                {{-- Item Details --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Description --}}
                    @if (!empty($item['description']))
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-lg font-semibold mb-2">Description</h4>
                            <p class="text-gray-700">{{ $item['description'] }}</p>
                        </div>
                    @endif

                    {{-- Where to Find --}}
                    @if (!empty($item['foundIn']))
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-lg font-semibold mb-2">Where to Find</h4>
                            <p class="text-gray-700">{{ $item['foundIn'] }}</p>
                        </div>
                    @endif

                    {{-- Effects (for consumables) --}}
                    @if (!empty($item['effects']))
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-lg font-semibold mb-3">Effects</h4>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($item['effects'] as $key => $value)
                                    <div class="bg-gray-50 p-2 rounded">
                                        <span class="font-semibold">{{ $key }}:</span>
                                        <span class="text-gray-700">{{ $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Recycles Into --}}
                    @if (!empty($item['recyclesInto']))
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-lg font-semibold mb-3">Recycles Into</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($item['recyclesInto'] as $materialId => $quantity)
                                    <a href="{{ route('items.show', $materialId) }}"
                                       class="bg-gray-50 hover:bg-gray-100 p-3 rounded border border-gray-200 transition-colors">
                                        <p class="font-semibold text-sm">{{ ucfirst(str_replace('_', ' ', $materialId)) }}</p>
                                        <p class="text-gray-600 text-xs">Quantity: {{ $quantity }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Items that recycle into this material --}}
                    @if ($recycledFrom->isNotEmpty())
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-lg font-semibold mb-3">Can be obtained by recycling</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @foreach ($recycledFrom as $sourceItem)
                                    <a href="{{ route('items.show', $sourceItem['id']) }}"
                                       class="bg-gray-50 hover:bg-gray-100 p-2 rounded border border-gray-200 transition-colors">
                                        @if (!empty($sourceItem['imageFilename']))
                                            <div class="aspect-square bg-white flex items-center justify-center mb-2 rounded">
                                                <img src="{{ $sourceItem['imageFilename'] }}"
                                                     alt="{{ $sourceItem['name'] }}"
                                                     class="w-full h-full object-contain">
                                            </div>
                                        @endif
                                        <p class="font-semibold text-xs text-center truncate" title="{{ $sourceItem['name'] }}">
                                            {{ $sourceItem['name'] }}
                                        </p>
                                        <p class="text-gray-600 text-xs text-center">
                                            → {{ $sourceItem['recyclesInto'][$item['id']] }}x
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Add to Inventory Button --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <form method="POST" action="{{ route('inventory.increment', $item['id']) }}">
                            @csrf
                            <div class="flex items-center gap-4">
                                <input type="number"
                                       name="amount"
                                       value="1"
                                       min="1"
                                       class="w-24 px-3 py-2 border rounded">
                                <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                    Add to Inventory
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
