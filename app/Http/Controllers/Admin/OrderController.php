<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;

class OrderController extends Controller
{
    public function index()
    {
        $data = [
            'orders' => Order::all()->sortByDesc('id'),
            'title' => 'Orders'
        ];

        return view('admin.order.index', $data);
    }

    public function detail($order_code)
    {
        $data = [
            'order' => Order::where('order_code', $order_code)->first(),
            'orderDetail' => OrderDetail::where('order_code', $order_code)->get(),
            'title' => 'Order Detail - ' . $order_code
        ];

        return view('admin.order.detail', $data);
    }

    public function updateStatus(Request $request, $order_code)
    {
        $order = Order::where('order_code', $order_code)->firstOrFail();

        // Validasi input status dan file hasil
        $rules = [
            'status' => 'required|integer'
        ];

        if ($request->status == 5 && !$order->turnitin_result) {
            $rules['result_file'] = 'required|file|mimes:pdf,doc,docx|max:10240';
        } else {
            $rules['result_file'] = 'nullable|file|mimes:pdf,doc,docx|max:10240';
        }

        $request->validate($rules);

        // Proses upload file hasil pengecekan (Turnitin Result)
        if ($request->hasFile('result_file')) {
            if ($order->turnitin_result && \Storage::disk('public')->exists($order->turnitin_result)) {
                \Storage::disk('public')->delete($order->turnitin_result);
            }
            $order->turnitin_result = $request->file('result_file')->store('results', 'public');
        }

        // --- LOGIKA PEMBERSIHAN AMANDEMEN JIKA COMPLETED (Status 5) ---
        if ($request->status == 5) {
            if ($order->amandement_path) {
                $amandementFiles = explode('|', $order->amandement_path);

                foreach ($amandementFiles as $filePath) {
                    if (!empty($filePath) && \Storage::disk('public')->exists($filePath)) {
                        \Storage::disk('public')->delete($filePath);
                    }
                }
                // Kosongkan kolom di database agar bersih
                $order->amandement_path = null;
            }
        }

        // --- LOGIKA STOK DIHAPUS DARI SINI ---

        $order->status = $request->status;
        $order->save();

        return redirect()->route('orderDetail', $order_code)->with('success', 'Order Status & Data Updated');
    }
    public function delete($order_code)
    {
        $order = Order::where('order_code', $order_code)->first();

        if ($order) {
            $filesToDelete = [
                $order->document_path,
                $order->payment_path,
                $order->turnitin_result,
                $order->amandement_path
            ];

            foreach ($filesToDelete as $path) {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            $order->delete();
            OrderDetail::where('order_code', $order_code)->delete();
        }

        return redirect()->route('orders')->with('danger', 'Order deleted');
    }
}
