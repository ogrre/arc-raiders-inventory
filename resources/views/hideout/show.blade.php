<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $module['name'] }}
            </h2>
            <a href="{{ route('hideout.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Hideout
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Current Level Selector --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('hideout.show', $module['id']) }}">
                    <label for="current_level" class="block text-sm font-medium text-gray-700 mb-2">Your Current Level:</label>
                    <select name="current_level" 
                            id="current_level" 
                            onchange="this.form.submit()"
                            class="mt-1 block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @for ($i = 0; $i <= ($module['maxLevel'] ?? 0); $i++)
                            <option value="{{ $i }}" {{ $currentLevel == $i ? 'selected' : '' }}>
                                Level {{ $i }}
                            </option>
                        @endfor
                    </select>
                </form>
            </div>

            {{-- Total Requirements from Current Level --}}
            @if ($totalRequirements->isNotEmpty())
                <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">📊 Materials Needed to Max Level</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        @foreach ($totalRequirements as $req)
                            @php
                                $item = $req['item'];
                                $bgColor = $req['completed'] ? 'bg-green-100 border-green-300' : 'bg-white border-gray-200';
                            @endphp
                            <div class="{{$bgColor}} border-2 rounded p-2 text-center">
                                @if (!empty($item['imageFilename']))
                                    <img src="{{ $item['imageFilename'] }}"
                                         alt="{{ $item['name'] }}"
                                         class="w-12 h-12 object-contain mx-auto mb-1">
                                @endif
                                <p class="text-xs font-semibold truncate" title="{{ $item['name'] ?? '' }}">
                                    {{ $item['name'] ?? 'Unknown' }}
                                </p>
                                <p class="text-xs {{ $req['completed'] ? 'text-green-700' : 'text-red-600' }} font-bold">
                                    {{ $req['owned'] }}/{{ $req['needed'] }}
                                </p>
                                @if (!$req['completed'])
                                    <p class="text-xs text-red-500">(need {{ $req['missing'] }})</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Levels --}}
            @foreach ($levelsWithRequirements as $levelData)
                @php
                    $level = $levelData['level'];
                    $requirements = $levelData['requirements'];
                    $completed = $levelData['completed'];
                    $isCurrentOrPast = $level['level'] <= $currentLevel;
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg {{ $isCurrentOrPast ? 'opacity-50' : '' }}">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">
                                Level {{ $level['level'] }}
                                @if ($isCurrentOrPast)
                                    <span class="ml-2 text-gray-500">✓ Already Owned</span>
                                @elseif ($completed)
                                    <span class="ml-2 text-green-600">✓ Ready to Upgrade</span>
                                @endif
                            </h3>
                        </div>

                        @if (!empty($level['otherRequirements']))
                            <div class="mb-4 bg-gray-100 border border-gray-300 rounded p-3">
                                <p class="text-sm text-gray-700">
                                    <strong>Other Requirements:</strong> {{ implode(', ', $level['otherRequirements']) }}
                                </p>
                            </div>
                        @endif

                        @if (!empty($requirements))
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                @foreach ($requirements as $req)
                                    @php
                                        $item = $req['item'];
                                        $bgColor = $req['completed'] ? 'bg-green-50' : 'bg-gray-50';
                                    @endphp
                                    <div class="{{ $bgColor }} rounded border p-3 text-center">
                                        @if (!empty($item['imageFilename']))
                                            <a href="{{ route('items.show', $item['id']) }}">
                                                <img src="{{ $item['imageFilename'] }}"
                                                     alt="{{ $item['name'] }}"
                                                     class="w-16 h-16 object-contain mx-auto mb-2">
                                            </a>
                                        @endif
                                        <p class="text-sm font-semibold truncate" title="{{ $item['name'] ?? '' }}">
                                            <a href="{{ route('items.show', $item['id']) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $item['name'] ?? 'Unknown' }}
                                            </a>
                                        </p>
                                        <p class="text-sm font-bold {{ $req['completed'] ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $req['owned'] }}/{{ $req['needed'] }}
                                        </p>
                                        @if (!$req['completed'])
                                            <p class="text-xs text-red-500 mt-1">Need {{ $req['missing'] }} more</p>
                                            <a href="{{ route('inventory.add') }}" class="text-xs text-blue-600 hover:underline">
                                                Add to inventory
                                            </a>
                                        @else
                                            <p class="text-xs text-green-600 mt-1">✓ Ready</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">No item requirements for this level.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
