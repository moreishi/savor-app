<?php

use App\Http\Controllers\PriceImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\GroceryListController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/{slug}', [RecipeController::class, 'show'])->name('recipes.show');
Route::get('/grocery-list', [GroceryListController::class, 'index'])->name('grocery-list.index');
Route::post('/grocery-list/add/{recipe}', [GroceryListController::class, 'addRecipe'])->name('grocery-list.add');
Route::post('/grocery-list/remove/{recipe}', [GroceryListController::class, 'removeRecipe'])->name('grocery-list.remove');
Route::post('/grocery-list/clear', [GroceryListController::class, 'clear'])->name('grocery-list.clear');
Route::post('/grocery-list/branch', [GroceryListController::class, 'setBranch'])->name('grocery-list.branch');

Route::view('/offline', 'offline');

Route::get('/dashboard', function () {
    $stats = [
        'recipes' => \App\Models\Recipe::count(),
        'ingredients' => \App\Models\Ingredient::count(),
        'branches' => \App\Models\Branch::count(),
        'prices' => \App\Models\BranchPrice::count(),
    ];

    return view('dashboard', compact('stats'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('prices')->group(function () {
    Route::get('/import', [PriceImportController::class, 'create'])->name('prices.import');
    Route::post('/import', [PriceImportController::class, 'store']);
    Route::get('/import/{import}/status', [PriceImportController::class, 'status']);
    Route::post('/import/{import}/confirm', [PriceImportController::class, 'confirm']);
    Route::get('/template', [PriceImportController::class, 'downloadTemplate'])->name('prices.template');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('recipes', App\Http\Controllers\Admin\RecipeController::class);
    Route::resource('ingredients', App\Http\Controllers\Admin\IngredientController::class);
    Route::resource('branches', App\Http\Controllers\Admin\BranchController::class);
    Route::resource('tags', App\Http\Controllers\Admin\TagController::class);
});

require __DIR__.'/auth.php';
