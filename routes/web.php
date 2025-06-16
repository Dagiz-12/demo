<?php

use App\Http\Controllers\{CategoryController};
use App\Http\Controllers\ItemController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});


Route::resource('categories', CategoryController::class);

// routes for items
Route::resource('items', ItemController::class);

Route::get('/test-image', function() {
    $item = new App\Models\Item;
    $item->name = 'Test Item';
    $item->category_id = 1;
    $item->price = 10;
    $item->image_path = 'items/test.jpg';
    
    if ($item->save()) {
        return response()->json([
            'success' => true,
            'item' => $item,
            'image_url' => asset('storage/'.$item->image_path)
        ]);
    }
    
    return response()->json(['success' => false]);
});


Route::post('/items/{item}/quantities', [ItemController::class, 'syncQuantities'])
    ->name('items.quantities.sync');
// routes/web.php

Route::resource('orders', OrderController::class);
Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');