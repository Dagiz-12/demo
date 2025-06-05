<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
       
    if ($request->ajax()) {
        $categories = Category::select(['id', 'name', 'status'])
            ->get()
            ->map(function ($item) {
                // Ensure status has a value
                $item->status = $item->status === null ? 'Active' : $item->status;
                return $item;
            });

        return DataTables::of($categories)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                return ''; // Empty string - handled in JS
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }
    
    
    return view('default.category');
    }
    
    public function store(Request $request)
    {
        Log::info('Store request data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,'.$request->id,
            'status' => 'required|in:Active,Inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category = Category::updateOrCreate(
                ['id' => $request->id],
                $validator->validated()
            );

    
            return response()->json([
                'success' => true,
                'message' => $request->id ? 'Category updated successfully' : 'Category created successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function edit($id)
    {
        try {
            
            $category = Category::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
    }

    
public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:categories,name,'.$id,
        'status' => 'required|in:Active,Inactive'
    ]);

    try {
        $category = Category::findOrFail($id);
        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
    
    public function destroy(Category $category)
    {
        
    try {
       $category->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
    }
}