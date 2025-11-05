<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\HideoutModuleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('inventory.index');
    })->name('dashboard');

    // Inventory routes
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/add', [InventoryController::class, 'add'])->name('inventory.add');
    Route::post('/inventory/update', [InventoryController::class, 'update'])->name('inventory.update');
    Route::post('/inventory/{itemId}/increment', [InventoryController::class, 'increment'])->name('inventory.increment');
    Route::post('/inventory/{itemId}/decrement', [InventoryController::class, 'decrement'])->name('inventory.decrement');
    Route::delete('/inventory/{itemId}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    // Items routes (database/wiki)
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/{gameId}', [ItemController::class, 'show'])->name('items.show');

    // Projects routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{projectId}', [ProjectController::class, 'show'])->name('projects.show');

    // Hideout modules routes
    Route::get('/hideout', [HideoutModuleController::class, 'index'])->name('hideout.index');
    Route::get('/hideout/{moduleId}', [HideoutModuleController::class, 'show'])->name('hideout.show');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
