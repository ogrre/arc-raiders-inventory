<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ARC Raiders Inventory Manager</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-100">
        <div class="min-h-screen flex flex-col items-center justify-center">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-6xl font-bold text-gray-900 mb-4">
                        ARC Raiders
                    </h1>
                    <h2 class="text-3xl font-semibold text-gray-700 mb-8">
                        Inventory Manager
                    </h2>
                    <p class="text-xl text-gray-600 mb-12">
                        Manage your in-game inventory, track items, and organize your resources efficiently.
                    </p>

                    @if (Route::has('login'))
                        <div class="flex justify-center gap-4">
                            @auth
                                <a href="{{ route('inventory.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition-colors">
                                    Go to My Inventory
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition-colors">
                                    Log in
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition-colors">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>

                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="text-blue-600 text-4xl mb-4">📦</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Manage Inventory</h3>
                        <p class="text-gray-600">
                            Keep track of all your in-game items with quantities, categories, and filters.
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="text-blue-600 text-4xl mb-4">🔍</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Items Database</h3>
                        <p class="text-gray-600">
                            Browse the complete database of ARC Raiders items with details and locations.
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="text-blue-600 text-4xl mb-4">📊</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Grid & List Views</h3>
                        <p class="text-gray-600">
                            Switch between grid and list views to visualize your inventory the way you prefer.
                        </p>
                    </div>
                </div>

                <div class="mt-12 text-center text-gray-500 text-sm">
                    <p>Data powered by <a href="https://github.com/RaidTheory/arcraiders-data" target="_blank" class="text-blue-600 hover:text-blue-800 underline">RaidTheory/arcraiders-data</a></p>
                    <p class="mt-2">Inspired by <a href="https://arctracker.io/" target="_blank" class="text-blue-600 hover:text-blue-800 underline">arctracker.io</a></p>
                </div>
            </div>
        </div>
    </body>
</html>
