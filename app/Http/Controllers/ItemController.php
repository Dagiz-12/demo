<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = Item::with('category', 'images')->select(['id', 'category_id', 'name', 'price', 'created_at', 'updated_at'])
                ->get()
                ->map(function ($item) {
                    // Ensure category is loaded
                    $item->category_name = $item->category ? $item->category->name : 'Uncategorized';
                    return $item;
                });
            
            return DataTables::of($items)
                ->addIndexColumn()
                ->addColumn('category', function($item) {
                    return $item->category->name;
                })
                ->addColumn('price', function($item) {
                    return $item->price; // Return raw value for DataTables to format
                })
               
               
                ->addColumn('action', function($item) {
                    return '<div class="d-flex justify-content-center">
                        <button class="btn btn-sm btn-primary mx-1 edit-btn" data-id="'.$item->id.'">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger mx-1 delete-btn" data-id="'.$item->id.'">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button class="btn btn-sm btn-info mx-1 view-btn" data-id="'.$item->id.'">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $categories = Category::where('status', 'Active')->get();
        return view('default.item', compact('categories'));
    }

    public function show($id)
{
    try {
        $item = Item::with(['category', 'images'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item,
            'html' => view('partials.item_details', compact('item'))->render()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Item not found'
        ], 404);
    }
}

    /**
     * Handle both store and update operations
     */

public function createOrUpdate(Request $request)
{
     $rules = [
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'quantities.*.quantity' => 'required|integer|min:1',
    ];

    if (!$request->id || $request->name != Item::find($request->id)?->name) {
        $rules['name'] = 'required|string|max:255|unique:items,name,'.$request->id;
    }

    $validator = Validator::make($request->all(), $rules, [
        'category_id.required' => 'Please select a category',
        'price.numeric' => 'Price must be a number',
        'images.*.image' => 'The file must be an image',
        'images.*.mimes' => 'Only JPEG, PNG, JPG, and GIF images are allowed',
        'images.*.max' => 'Image size must be less than 2MB'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Create/update the item
        $item = Item::updateOrCreate(
            ['id' => $request->id],
            $request->only(['category_id', 'name', 'price', 'memo'])
        );


        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            // Delete old images if editing
            if ($request->id) {
                $item->images()->delete();
            }

            // In ItemController.php, change the image storage code to:
                            foreach ($request->file('images') as $image) {
                    $path = $image->store('items', 'public'); // Stores in storage/app/public/items
                    $item->images()->create([
                        'image_path' => $path // Stores the relative path
                    ]);
                }
        }


          // Handle quantities
        if ($request->has('quantities')) {
            $quantitiesData = collect($request->quantities)->map(function ($quantity) use ($item) {
                return [
                    'quantity' => $quantity['quantity'],
                    'total_price' => $quantity['quantity'] * $item->price,
                ];
            })->toArray();

            $item->quantities()->delete();
            $item->quantities()->createMany($quantitiesData);
        }
        
        return response()->json([
            'success' => true,
            'message' => $request->id ? 'Item updated successfully' : 'Item created successfully',
            'data' => $item->load([ 'images', 'quantities'])
        ]);
        
    } catch (\Exception $e) {
        Log::error('Item save error:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Get item data for editing
     */
    public function edit($id)
{
    try {
        $item = Item::with(['category', 'images', 'quantities'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Item not found'
        ], 404);
    }
}

    /**
     * Delete an item
     */
 public function destroy($id)
{
    try {
        $item = Item::with('images')->findOrFail($id);
        
        // Delete all associated images
        foreach ($item->images as $image) {
            if (Storage::exists('public/' . $image->image_path)) {
                Storage::delete('public/' . $image->image_path);
            }
        }
        $item->images()->delete();
        
        $item->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Legacy store method that uses createOrUpdate
     */
    public function store(Request $request)
    {
        return $this->createOrUpdate($request);
    }

    /**
     * Legacy update method that uses createOrUpdate
     */
    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        return $this->createOrUpdate($request);
    }



    // Add these methods to ItemController

public function syncQuantities(Request $request, $itemId)
{
    $validator = Validator::make($request->all(), [
        'quantities' => 'required|array',
        'quantities.*.quantity' => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $item = Item::findOrFail($itemId);
        
        $quantitiesData = collect($request->quantities)->map(function ($quantity) use ($item) {
            return [
                'quantity' => $quantity['quantity'],
                'total_price' => $quantity['quantity'] * $item->price,
            ];
        })->toArray();

        // Use sync to update quantities
        $item->quantities()->delete();
        $item->quantities()->createMany($quantitiesData);

        return response()->json([
            'success' => true,
            'message' => 'Quantities updated successfully',
            'data' => $item->load('quantities')
        ]);

    } catch (\Exception $e) {
        Log::error('Quantity sync error:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

}