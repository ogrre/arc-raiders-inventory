<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Projects & Expeditions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <p class="text-gray-700">
                        Track your progress on major projects and expeditions. See what materials you need and what you already have in your inventory.
                    </p>
                </div>
            </div>

            @if ($projects->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">No projects available.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($projects as $project)
                        <a href="{{ route('projects.show', $project['id']) }}"
                           class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $project['name'] }}</h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $project['description'] ?? '' }}</p>

                                @if (!empty($project['phases']))
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        {{ count($project['phases']) }} phases
                                    </div>
                                @endif
                            </div>
                            <div class="bg-gray-50 px-6 py-3">
                                <span class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                                    View Requirements →
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
