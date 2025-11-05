<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hideout Modules') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <p class="text-gray-700">
                        Upgrade your hideout modules to unlock new capabilities. Track what materials you need for each level.
                    </p>
                </div>
            </div>

            @if ($modules->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">No modules available.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($modules as $module)
                        <a href="{{ route('hideout.show', $module['id']) }}"
                           class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $module['name'] }}</h3>

                                @if (!empty($module['maxLevel']))
                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        Max Level: {{ $module['maxLevel'] }}
                                    </div>
                                @endif

                                @if (!empty($module['levels']))
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        {{ count($module['levels']) }} upgrade levels
                                    </div>
                                @endif
                            </div>
                            <div class="bg-gray-50 px-6 py-3">
                                <span class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                                    View Upgrades →
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
