<?php

namespace App\Http\Controllers;

use App\Models\Purchases;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Item;
use App\Models\PurchaseLine;
use App\Models\Stock;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::pluck('name', 'id');
        $purchases = Purchases::with('supplier')->latest()->paginate(15);
        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::pluck('name', 'id');
        return view('purchases.create', compact('suppliers'));
    }

    public function createFull(Request $request)
    {
        $suppliers = Supplier::pluck('name', 'id');
        $search = $request->get('search');
        $items = Item::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })->latest()->paginate(15)->appends(['search' => $search]);
        return view('purchases.create-full', compact('suppliers', 'items', 'search'));
    }

    public function editFull(Purchases $purchase)
    {
        $suppliers = Supplier::pluck('name', 'id');
        $items = Item::latest()->paginate(15);
        return view('purchases.index', compact('purchase', 'suppliers', 'items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            // 'date' => 'nullable|date',
            'total' => 'nullable|numeric',
            // 'status' => 'nullable|string',
        ]);

        $lastPurchase = Purchases::orderBy('id', 'desc')->first();
        $nextId = $lastPurchase ? $lastPurchase->id + 1 : 1;
        $purchaseNumber = 'PUR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        Purchases::create([
            'reference_id' => $purchaseNumber,
            'supplier_id' => $request->supplier_id,
            'date' => now(),
        ]);

        return redirect()->route('purchases.index')->with('success', 'تم الاضافه بنجاح .');
    }

    // public function storeFull(Request $request)
    // {
    //     // dd($request->all()); // للاختبار
    //     $validator = \Illuminate\Support\Facades\Validator::make($request->all(), []);
    //     if ($validator->fails()) {
    //         return response()->json(['error' => false, 'message' => 'خطأ في بيانات الطلب', 'errors' => $validator->errors()], 422);
    //     }

    //     try {
    //         $total_price = 0;
    //         $lastPurchase = Purchases::orderBy('id', 'desc')->first();
    //         $nextId = $lastPurchase ? $lastPurchase->id + 1 : 1;
    //         $purchaseNumber = 'PUR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    //         $purchase = Purchases::create([
    //             'reference_id' => $purchaseNumber,
    //             'supplier_id' => $request->supplier_id,
    //             'date' => now(),
    //             'status' => 'draft',
    //         ]);

    //         // Create purchase lines
    //         foreach ($request->products as $id => $quantities) {
    //             $item = Item::findOrFail($id);
    //             $lineTotal = $quantities['unit_price'] * $quantities['quantity'];
    //             $total_price += $lineTotal;
    //              $line = PurchaseLine::create([
    //                 'purchase_id' => $purchase->id,
    //                 'item_id' => $id,
    //                 'quantity' => $quantities['quantity'],
    //                 'unit_price' => $quantities['unit_price'],
    //                 'total' => $lineTotal,
    //             ]);
    //            Stock::create([
    //             'purchase_id'=>$purchase->id,
    //             'item_id' => $item->id,
    //             'change' => $quantities['quantity'],
    //             'type' => 'مشتريات',
    //             'reference_id' => $line->id,
    //             'note' => '   تم شراء  '.$item->name,
    //         ]);
    //         }
    //         $purchase->update([
    //             'total' => $total_price,
    //         ]);

    //         return redirect()->route('purchases.index', $purchase)->with('success', 'تم إضافة المشتريات بنجاح');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
    //     }
    // }

    // public function updateFull(Request $request, Purchases $purchase)
    // {
    //     try {
    //         // Delete existing lines
    //         $purchase->lines()->delete();

    //         $total_price = 0;
    //         $purchase->update([
    //             'supplier_id' => $request->supplier_id,
    //         ]);

    //         // Create new purchase lines
    //         foreach ($request->products as $id => $quantities) {
    //             $item = Item::findOrFail($id);
    //             $lineTotal = $quantities['unit_price'] * $quantities['quantity'];
    //             $total_price += $lineTotal;
    //             \App\Models\PurchaseLine::create([
    //                 'purchase_id' => $purchase->id,
    //                 'item_id' => $id,
    //                 'quantity' => $quantities['quantity'],
    //                 'unit_price' => $quantities['unit_price'],
    //                 'total' => $lineTotal,
    //             ]);
    //         }
    //         $purchase->update([
    //             'total' => $total_price,
    //         ]);

    //         return redirect()->route('purchases.index', $purchase)->with('success', 'تم تحديث المشتريات بنجاح');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
    //     }
    // }

    public function show(Purchases $purchase)
    {
        $items = \App\Models\Item::pluck('name', 'id');
        return view('purchases.show', compact('purchase', 'items'));
    }

    public function getPurchaseData(Purchases $purchase)
    {
        $lines = $purchase->purchaseLines->map(function ($line) {
            return [
                'item_id' => $line->item_id,
                'item_name' => optional($line->item)->name,
                'quantity' => $line->quantity,
                'unit_price' => $line->unit_price,
                'total' => $line->total,
            ];
        });

        return response()->json([
            'reference_id' => $purchase->reference_id,
            'supplier_id' => $purchase->supplier_id,
            'supplier' => optional($purchase->supplier)->name,
            'date' => $purchase->date ? $purchase->date->format('Y-m-d') : now()->format('Y-m-d'),
            'total' => $purchase->total,
            'lines' => $lines,
        ]);
    }

    public function edit(Purchases $purchase)
    {
        $suppliers = Supplier::pluck('name', 'id');
        return view('purchases.edit', compact('purchase', 'suppliers'));
    }

    public function update(Request $request, Purchases $purchase)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            // 'date' => 'nullable|date',
            'total' => 'nullable|numeric',
            // 'status' => 'nullable|string',
        ]);

        $purchase->update($data);
        return redirect()->route('purchases.index')->with('success', 'تم التحديث بنجاح .');
    }

    public function destroy(Purchases $purchase)
    {
        // optionally reduce stock based on lines
        foreach ($purchase->lines as $line) {
            $item = $line->item;
            if ($item) {
                $item->decrement('stock', $line->quantity);
            }
        }

        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', ' تم الحذف  .');
    }
}
