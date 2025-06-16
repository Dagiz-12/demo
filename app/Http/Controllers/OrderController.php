<?php
namespace App\Http\Controllers;

use App\Models\ParentOrder;
use App\Models\Pos;
use App\Models\Item;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ChildOrderItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {
        $data = ParentOrder::with(['pos1', 'items.item'])
            ->select('parent_orders.*')
            ->addSelect([
                'items_count' => ChildOrderItem::selectRaw('count(*)')
                    ->whereColumn('parent_order_id', 'parent_orders.id'),
                'total_quantity' => ChildOrderItem::selectRaw('sum(quantity)')
                    ->whereColumn('parent_order_id', 'parent_orders.id'),
                'total_price' => ChildOrderItem::selectRaw('sum(total_price)')
                    ->whereColumn('parent_order_id', 'parent_orders.id')
            ]);

        return DataTables::of($data)
            ->addColumn('action', function($row) {
                return '
                    <button class="btn btn-primary btn-sm edit-order" data-id="'.$row->id.'">Edit</button>
                    <button class="btn btn-danger btn-sm delete-order" data-id="'.$row->id.'">Delete</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    $posList = Pos::all();
    $items = Item::all();
    return view('orders.index', compact('posList', 'items'));
}

    public function createOrUpdate(Request $request)
{
    $validated = $request->validate([
        'pos1_id' => 'required|exists:pos,id',
        'order_date' => 'required|date',
        'memo' => 'nullable|string',
        'items' => 'required|array|min:1',
        'items.*.item_id' => 'required|exists:items,id',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    try {
        DB::beginTransaction();

        $order = ParentOrder::updateOrCreate(
            ['id' => $request->id],
            [
                'pos1_id' => $validated['pos1_id'],
                'order_date' => $validated['order_date'],
                'memo' => $validated['memo'],
                
            ]
        );

        // First delete all existing items
        $order->items()->delete();

        // Then create new items
        foreach ($validated['items'] as $itemData) {
            $item = Item::find($itemData['item_id']);
            ChildOrderItem::create([
                'parent_order_id' => $order->id,
                'item_id' => $itemData['item_id'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $item->price,
                'total_price' => $itemData['quantity'] * $item->price,
            ]);
        }

        $order->updateTotalPrice();
        
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => $request->id ? 'Order updated successfully' : 'Order created successfully'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Order save error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    // Keep the show method as is
    public function show($id)
    {
        $order = ParentOrder::with(['pos1', 'orderedItems'])->findOrFail($id);
        return response()->json(['order' => $order]);
    }

    // Update the edit method to match our new structure
public function edit($id)
{
    try {
        $order = ParentOrder::with(['pos1', 'items.item'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'pos1_id' => $order->pos1_id,
                'order_date' => $order->order_date ? $order->order_date->format('Y-m-d\TH:i') : '',
                'memo' => $order->memo,
                'items' => $order->items->map(function($item) {
                    return [
                        'item_id' => $item->item_id,
                        'quantity' => (int)$item->quantity,
                        'unit_price' => (float)$item->unit_price,
                        'total_price' => (float)$item->total_price,
                    ];
                })
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load order data: ' . $e->getMessage()
        ], 500);
    }
}

    // Keep the destroy method as is
    public function destroy($id)
    {
        $order = ParentOrder::findOrFail($id);
        $order->orderedItems()->detach();
        $order->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.'
        ]);
    }

    // Add these methods to maintain compatibility with existing routes
    public function store(Request $request)
    {
        return $this->createOrUpdate($request);
    }

    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        return $this->createOrUpdate($request);
    }
}