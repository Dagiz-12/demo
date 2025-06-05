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
            $items = Item::with('category')->select(['id', 'category_id', 'name', 'price', 'image_path', 'created_at', 'updated_at'])
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
               
                ->addColumn('image', function($item) {
                    return $item->image_path 
                        ? '<img src="'.asset($item->image_path).'" alt="Item Image" style="max-height: 50px;">'
                        : 'No Image';
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
                ->rawColumns(['image', 'action'])
                ->make(true);
        }
        
        $categories = Category::where('status', 'Active')->get();
        return view('default.item', compact('categories'));
    }

    public function show($id)
    {
        try {
            $item = Item::with('category')->findOrFail($id);
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
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    // Only add unique validation for name if it's a new item or the name has changed
    if (!$request->id || $request->name != Item::find($request->id)->name) {
        $rules['name'] = 'required|string|max:255|unique:items,name,'.$request->id;
    }

    $validator = Validator::make($request->all(), $rules, [
        'category_id.required' => 'Please select a category',
        'price.numeric' => 'Price must be a number',
        'image.image' => 'The file must be an image',
        'image.mimes' => 'Only JPEG, PNG, JPG, and GIF images are allowed',
        'image.max' => 'Image size must be less than 2MB'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $data = $request->except(['image', '_token']);
        
        if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($request->id && $item = Item::find($request->id)) {
                    if ($item->image_path && Storage::exists('public/' . $item->image_path)) {
                        Storage::delete('public/' . $item->image_path);
                    }
                }

                $filename = time().'_'.Str::slug($request->file('image')->getClientOriginalName());
                $path = $request->file('image')->storeAs('public/items', $filename);
                $data['image_path'] = 'items/'.$filename; // Store relative path without 'public/'
            } else if ($request->id) {
            // Keep the existing image if no new image is uploaded
            $existingItem = Item::find($request->id);
            if ($existingItem && $existingItem->image_path) {
                $data['image_path'] = $existingItem->image_path;
            }
        }

        $item = Item::updateOrCreate(
            ['id' => $request->id],
            $data
        );
        
        return response()->json([
            'success' => true,
            'message' => $request->id ? 'Item updated successfully' : 'Item created successfully',
            'data' => $item
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
            $item = Item::findOrFail($id);
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
            $item = Item::findOrFail($id);
            
            // Delete associated image
            if ($item->image_path && Storage::exists(str_replace('/storage', 'public', $item->image_path))) {
                Storage::delete(str_replace('/storage', 'public', $item->image_path));
            }
            
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
}