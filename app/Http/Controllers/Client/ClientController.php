<?php

namespace App\Http\Controllers\Client;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Validator;
use Str;

class ClientController extends Controller
{
    public function index()
    {

        // if (!Shop::exists()) {
        //     return redirect()->route('register');
        // }

        $data = [
            'shop' => Shop::first(),
            'product' => Product::all()->sortByDesc('id')->take(8),
            'category' => Category::all()->sortByDesc('id')->take(4),
            'title' => 'Home'
        ];

        return view('client.index', $data);
    }

    public function products()
    {
        $data = [
            'shop' => Shop::first(),
            'product' => Product::orderBy('id', 'DESC')->paginate(16),
            'category' => Category::all()->sortByDesc('id'),
            'title' => 'Products'
        ];

        return view('client.products', $data);
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Search products by name
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    /*******  08f56397-a699-467d-95d0-c81405254457  *******/
    public function searchProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('clientHome')->withErrors($validator)->withInput();
        } else {

            $search = str_replace(' ', '-', strtolower($request->product));

            $data = [
                'title' => 'Result',
                'shop' => Shop::first(),
                'product' => Product::where('title', 'LIKE', '%' . $search . '%')->orderBy('id', 'DESC')->paginate(20),
                'search' => $request->product
            ];

            return view('client.productSearch', $data);
        }
    }

    public function category()
    {
        $data = [
            'shop' => Shop::first(),
            'category' => Category::orderBy('id', 'DESC')->paginate(12),
            'title' => 'Products'
        ];

        return view('client.category', $data);
    }

    public function categoryProducts($category)
    {
        $data = [
            'shop' => Shop::first(),
            'category' => Category::where('name', $category)->first(),
            'title' => 'Category - ' . str_replace('-', ' ', ucwords($category))
        ];

        return view('client.categoryProducts', $data);
    }

    public function productDetail($product)
    {

        $product = Product::where('title', $product)->first();

        if ($product->category->product->count() > 1) {
            $recomendationProducts = $product->category->product->take(8);
        } else {
            $recomendationProducts = Product::all()->sortByDesc('id')->take(8);
        }

        $data = [
            'shop' => Shop::first(),
            'product' => $product,
            'recomendationProducts' => $recomendationProducts,
            'title' => str_replace('-', ' ', ucwords($product->title))
        ];

        return view('client.productDetail', $data);
    }

    public function checkout()
    {
        $data = [
            'shop' => Shop::first(),
            'title' => 'Checkout'
        ];

        return view('client.checkout', $data);
    }

    public function checkoutSave(Request $request)
    {
        // 1. Pastikan User Login
        if (!auth()->check()) {
            return response()->json(['error' => 'Silakan login terlebih dahulu'], 401);
        }

        $user = auth()->user();

        // 2. Validasi (Hapus 'payment' karena bayar pakai saldo)
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'document' => 'required|string', // Path dari temp upload
            'notes' => 'nullable|array',
            'notes.*' => 'string'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 3. Cek Kecukupan Kredit (Misal: 1 file = 1 credit)
        $cart = session('cart');
        // var_dump($cart);
        $totalCredits = reset($cart)['price']; // Sesuaikan jika dalam satu order bisa banyak file
        if ($user->credits < $totalCredits) {
            return response()->json(['error' => 'Saldo Kredit tidak cukup. Silakan Top-up.'], 402);
        }

        // 4. Proses Note (Tetap sama)
        $notes = $request->notes ?? [];
        if (in_array('Small Matches', $notes)) {
            $detailSmallMatch = [];
            if ($request->filled('small_match_word')) $detailSmallMatch[] = $request->small_match_word . " Words";
            if ($request->filled('small_match_percent')) $detailSmallMatch[] = $request->small_match_percent . "%";
            if (!empty($detailSmallMatch)) {
                $index = array_search('Small Matches', $notes);
                $notes[$index] = "Small Matches (" . implode(' & ', $detailSmallMatch) . ")";
            }
        }
        $noteString = !empty($notes) ? implode(', ', $notes) : null;

        $tempDocPath = $request->document;
        $finalDocPath = str_replace('temp/', 'documents/', $tempDocPath);

        if (Storage::disk('public')->exists($tempDocPath)) {
            Storage::disk('public')->move($tempDocPath, $finalDocPath);
        }

        $order_code = Str::random(3) . '-' . date('Ymd');

        // 6. Jalankan Transaksi (Potong Saldo & Simpan Order)
        try {
            DB::transaction(function () use ($user, $totalCredits, $order_code, $request, $noteString, $finalDocPath) {
                $user->decrement('credits', $totalCredits);
                // var_dump($user);
                $order = Order::create([
                    'user_id' => $user->id,
                    'shop_id' => Shop::first()->id,
                    'order_code' => $order_code,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'note' => $noteString,
                    'document_path' => $finalDocPath,
                    'payment_path' => 'PAID_WITH_CREDIT',
                    'total' => 0,
                    'status' => 1
                ]);

                // Jika ada detail item di cart
                if (session('cart')) {
                    foreach ((array) session('cart') as $id => $details) {
                        OrderDetail::create([
                            'order_code' => $order_code,
                            'title' => $details['title'],
                            'price' => $details['price'],
                            'quantity' => $details['quantity'],
                        ]);
                    }
                }
            });

            session()->forget('cart');

            return response()->json([
                'status' => 'success',
                'redirect_url' => route('clientOrderCode', $order_code)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses pesanan: ' . $e->getMessage()], 500);
        }
    }

    public function uploadTemporary(Request $request)
    {
        if ($request->hasFile('file')) {
            // Simpan ke folder temporary
            $path = $request->file('file')->store('temp', 'public');
            return response()->json(['path' => $path]);
        }
        return response()->json(['error' => 'No file'], 400);
    }

    public function uploadAmandement(Request $request, $order_code)
    {
        $request->validate([
            'amandement_file' => 'required|file|mimes:pdf,doc,docx|max:30720'
        ]);

        $order = Order::where('order_code', $order_code)->firstOrFail();

        if ($request->hasFile('amandement_file')) {
            $newPath = $request->file('amandement_file')->store('amandements', 'public');

            if ($order->amandement_path) {
                $order->amandement_path = $order->amandement_path . '|' . $newPath;
            } else {
                $order->amandement_path = $newPath;
            }

            $order->status = 2;
            $order->save();
        }
        return redirect()->route('clientCheckOrderStatus', ['order_code' => $order_code])
            ->with('success', 'File revisi berhasil dikirim ke Admin.');
    }

    public function successOrder($order_code)
    {

        $shop = Shop::first();
        $phone = trim($shop->phone);
        if (substr($phone, 0, 1) === '8') {
            $phone = '62' . $phone;
        } elseif (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $data = [
            'shop' => Shop::first(),
            'order_code' => $order_code,
            'title' => 'Checkout',
            'pesanWA' => "Halo Admin,%0A%0ASaya butuh bantuan untuk pesanan saya:%0A*Order ID: {$order_code}*%0A%0AMohon bantuannya, terima kasih.",
            'nomorAdmin' => $phone
        ];

        return view('client.success-order', $data);
    }


    public function checkOrder()
    {
        $data = [
            'shop' => Shop::first(),
            'title' => 'Check Order'
        ];

        return view('client.check-order', $data);
    }

    public function checkOrderStatus(Request $request)
    {
        $shop = Shop::first();
        $phone = trim($shop->phone);
        if (substr($phone, 0, 1) === '8') {
            $phone = '62' . $phone;
        } elseif (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }


        $order_code = $request->order_code;

        $order = null;
        $orderDetail = null;
        if ($order_code) {
            $order = Order::where('order_code', $order_code)->first();
            if ($order) {
                $orderDetail = OrderDetail::where('order_code', $order_code)->get();
            }
        }

        $data = [
            'shop' => $shop,
            'order' => $order,
            'orderDetail' => $orderDetail,
            'title' => 'Check Order',
            'pesanWA' => "Halo Admin,%0A%0ATolong segera konfirmasi pesanan saya:%0A*Order ID: {$order_code}*%0A%0AMohon bantuannya, terima kasih.",
            'nomorAdmin' => $phone
        ];

        return view('client.check-order', $data);
    }

    public function about()
    {
        $data = [
            'shop' => Shop::first(),
            'title' => 'About'
        ];

        return view('client.about', $data);
    }
}
