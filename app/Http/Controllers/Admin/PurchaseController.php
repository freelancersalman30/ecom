<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Product;
use App\Models\ProductVariantPrice;
use App\Models\FundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $currentYear  = now()->year;
        $currentMonth = now()->month;

        // Summary
        $yearlyTotal = Purchase::whereYear('purchase_date', $currentYear)->sum('grand_total');
        $monthlyTotal = Purchase::whereYear('purchase_date', $currentYear)
                                ->whereMonth('purchase_date', $currentMonth)
                                ->sum('grand_total');
        $todayTotal = Purchase::whereDate('purchase_date', now()->toDateString())
                              ->sum('grand_total');

        $totalDue = Purchase::sum('due_amount');

        // Filtered list
        $query = Purchase::with('supplier')->latest();

        if ($request->year) {
            $query->whereYear('purchase_date', $request->year);
        }
        if ($request->month) {
            $query->whereMonth('purchase_date', $request->month);
        }
        if ($request->from_date) {
            $query->whereDate('purchase_date', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('purchase_date', '<=', $request->to_date);
        }

        $purchases = $query->paginate(20);

        $suppliers = Supplier::orderBy('name')->get();
        $products  = Product::orderBy('name')->get();

        return view('backEnd.purchases.index', compact(
            'currentYear','currentMonth',
            'yearlyTotal','monthlyTotal','todayTotal','totalDue',
            'purchases','suppliers','products'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'   => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'invoice_no'    => 'required|string|max:50',
            'product_id'    => 'required|exists:products,id',
            'qty'           => 'required|integer|min:1',
            'unit_cost'     => 'required|numeric|min:0',
            'discount'      => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'paid_amount'   => 'nullable|numeric|min:0',
        ]);

        $discount      = $request->discount ?? 0;
        $shipping_cost = $request->shipping_cost ?? 0;

        $qty        = (int) $request->qty;
        $unit_cost  = (float) $request->unit_cost;
        $subtotal   = $qty * $unit_cost;
        $grandTotal = $subtotal - $discount + $shipping_cost;

        $paid  = min($grandTotal, (float) ($request->paid_amount ?? 0));
        $due   = $grandTotal - $paid;

        // Purchase create
        $purchase = Purchase::create([
            'supplier_id'   => $request->supplier_id,
            'invoice_no'    => $request->invoice_no,
            'purchase_date' => $request->purchase_date,
            'total_qty'     => $qty,
            'subtotal'      => $subtotal,
            'discount'      => $discount,
            'shipping_cost' => $shipping_cost,
            'grand_total'   => $grandTotal,
            'paid_amount'   => $paid,
            'due_amount'    => $due,
            'note'          => $request->note,
            'status'        => 'completed',
            'created_by'    => Auth::id(),
        ]);

        // Purchase Item
        $item = PurchaseItem::create([
            'purchase_id'     => $purchase->id,
            'product_id'      => $request->product_id,
            'variant_price_id'=> $request->variant_price_id ?: null,
            'qty'             => $qty,
            'unit_cost'       => $unit_cost,
            'line_total'      => $subtotal,
        ]);

        // STOCK UPDATE
        $product = Product::findOrFail($request->product_id);
        $product->stock = $product->stock + $qty;
        $product->purchase_price = $unit_cost; // চাইলে avg হিসাব করতে পারো
        $product->save();

        if ($request->variant_price_id) {
            $variant = ProductVariantPrice::find($request->variant_price_id);
            if ($variant) {
                $variant->stock = $variant->stock + $qty;
                $variant->save();
            }
        }

        // Supplier due update
        $supplier = Supplier::findOrFail($request->supplier_id);
        $supplier->current_due = $supplier->current_due + $grandTotal - $paid;
        $supplier->save();

        // যদি সাথে সাথে ফান্ড থেকে পেমেন্ট করো
        if ($paid > 0) {
            $fundTrx = FundTransaction::create([
                'direction'  => 'out',
                'source'     => 'supplier_payment',
                'source_id'  => null,
                'amount'     => $paid,
                'note'       => 'Purchase payment: '.$purchase->invoice_no,
                'created_by' => Auth::id(),
            ]);

            $pay = SupplierPayment::create([
                'supplier_id'        => $supplier->id,
                'purchase_id'        => $purchase->id,
                'amount'             => $paid,
                'payment_date'       => $request->purchase_date,
                'method'             => 'fund',
                'note'               => 'Initial payment',
                'fund_transaction_id'=> $fundTrx->id,
                'created_by'         => Auth::id(),
            ]);

            $fundTrx->source_id = $pay->id;
            $fundTrx->save();
        }

        return back()->with('success','Purchase created & stock updated!');
    }

    // Supplier due payment
    public function payDue(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $request->validate([
            'amount'       => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);

        $amount = (float) $request->amount;
        if ($amount > $purchase->due_amount) {
            return back()->with('error','Pay amount cannot be greater than due.');
        }

        $fundTrx = FundTransaction::create([
            'direction'  => 'out',
            'source'     => 'supplier_payment',
            'source_id'  => null,
            'amount'     => $amount,
            'note'       => 'Due payment for purchase: '.$purchase->invoice_no,
            'created_by' => Auth::id(),
        ]);

        $pay = SupplierPayment::create([
            'supplier_id'        => $purchase->supplier_id,
            'purchase_id'        => $purchase->id,
            'amount'             => $amount,
            'payment_date'       => $request->payment_date,
            'method'             => 'fund',
            'note'               => $request->note,
            'fund_transaction_id'=> $fundTrx->id,
            'created_by'         => Auth::id(),
        ]);

        $fundTrx->source_id = $pay->id;
        $fundTrx->save();

        // update purchase + supplier
        $purchase->paid_amount += $amount;
        $purchase->due_amount  -= $amount;
        $purchase->save();

        $supplier = $purchase->supplier;
        $supplier->current_due = max(0, $supplier->current_due - $amount);
        $supplier->save();

        return back()->with('success','Due payment successful!');
    }

    // Simple Return: শুধু stock কমাবে
    public function returnItem(Request $request, $itemId)
    {
        $item = PurchaseItem::with('purchase','product','variant')->findOrFail($itemId);

        $request->validate([
            'return_qty' => 'required|integer|min:1',
        ]);

        $qty = (int) $request->return_qty;

        $available = $item->qty - $item->returned_qty;
        if ($qty > $available) {
            return back()->with('error','Return qty cannot be greater than remaining qty.');
        }

        // Update returned qty
        $item->returned_qty += $qty;
        $item->save();

        // Stock minus
        $product = $item->product;
        $product->stock = max(0, $product->stock - $qty);
        $product->save();

        if ($item->variant) {
            $variant = $item->variant;
            $variant->stock = max(0, $variant->stock - $qty);
            $variant->save();
        }

        return back()->with('success','Purchase return processed & stock updated.');
    }

    public function invoice($id)
    {
        $purchase = Purchase::with(['supplier','items.product','items.variant','payments'])->findOrFail($id);
        return view('backEnd.purchases.invoice', compact('purchase'));
    }

    // Export CSV
    public function export(Request $request)
    {
        $query = Purchase::with('supplier')->orderBy('purchase_date','asc');

        if ($request->year) {
            $query->whereYear('purchase_date',$request->year);
        }
        if ($request->month) {
            $query->whereMonth('purchase_date',$request->month);
        }
        if ($request->from_date) {
            $query->whereDate('purchase_date','>=',$request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('purchase_date','<=',$request->to_date);
        }

        $purchases = $query->get();

        $filename = 'purchases_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($purchases) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date','Invoice','Supplier','Total Qty','Grand Total','Paid','Due']);

            foreach ($purchases as $p) {
                fputcsv($handle, [
                    $p->purchase_date,
                    $p->invoice_no,
                    optional($p->supplier)->name,
                    $p->total_qty,
                    $p->grand_total,
                    $p->paid_amount,
                    $p->due_amount,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
