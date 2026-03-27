<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topup;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Tampilkan halaman pilih paket top up
     */
    public function index()
    {
        // Ambil data shop (sesuaikan dengan logic di controller lain)
        $shop = \App\Models\Shop::first();
        $title = "Top Up Credits";

        $packages = [
            ['credits' => 5, 'price' => 5000],
            ['credits' => 10, 'price' => 10000],
            ['credits' => 50, 'price' => 45000],
            ['credits' => 100, 'price' => 85000],
        ];

        return view('client.payment.index', compact('packages', 'shop', 'title'));
    }
    /**
     * Proses pembuatan pesanan Top Up (Pending)
     */
    public function store(Request $request)
    {
        $request->validate([
            'credits' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        // Generate Reference Code Unik
        // Hasilnya: TOP-20260321-ABC
        $reference_code = 'TOP-' . date('Ymd') . '-' . strtoupper(Str::random(3));

        Topup::create([
            'user_id' => Auth::id(),
            'reference_code' => $reference_code,
            'amount_credits' => $request->credits,
            'price_total' => $request->price,
            'status' => 0, // 0 = Pending
        ]);

        return redirect()->route('topup.pay', $reference_code)
            ->with('success', 'Pesanan top up dibuat. Silakan selesaikan pembayaran.');
    }

    /**
     * Halaman instruksi pembayaran & upload bukti
     */
    public function pay($reference_code)
    {
        $shop = \App\Models\Shop::first();
        $topup = Topup::where('reference_code', $reference_code)->firstOrFail();
        $title = "Pembayaran Top Up";

        return view('client.payment.pay', compact('topup', 'shop', 'title'));
    }

    /**
     * Proses Upload Bukti Bayar
     */
    public function uploadProof(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_code' => 'required|exists:topups,reference_code',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $topup = Topup::where('reference_code', $request->reference_code)
            ->where('user_id', Auth::id())
            ->first();

        if ($request->hasFile('payment_proof')) {
            // Hapus foto lama jika ada (biar gak menuhin Render)
            if ($topup->payment_proof) {
                Storage::disk('public')->delete($topup->payment_proof);
            }

            $path = $request->file('payment_proof')->store('payments/topups', 'public');

            $topup->update([
                'payment_proof' => $path
            ]);
        }

        return redirect()->route('topup.history')->with('success', 'Bukti bayar berhasil diunggah. Menunggu verifikasi admin.');
    }

    /**
     * Lihat riwayat top up user
     */
    public function history()
    {

        $shop = Shop::first();
        $phone = trim($shop->phone);
        if (substr($phone, 0, 1) === '8') {
            $phone = '62' . $phone;
        } elseif (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Gunakan paginate saja, tidak perlu panggil get() lagi (biar hemat memori)
        $topups = Topup::where('user_id', auth()->id())->latest()->paginate(10);

        // Ambil record terbaru untuk pesan WA, tapi cek dulu ada datanya gak
        $latestTopup = $topups->first();
        $refCode = $latestTopup ? $latestTopup->reference_code : '-';

        $data = [
            'shop' => $shop,
            'topups' => $topups,
            'historys' => Topup::where('user_id', auth()->id())->latest()->get(),
            'title' => "Riwayat Top Up",
            'pesanWA' => "Halo Admin,%0A%0ASaya butuh bantuan untuk pesanan saya:%0A*Order ID: {$refCode}*%0A%0AMohon bantuannya, terima kasih.",
            'nomorAdmin' => $phone
        ];

        return view('client.payment.history', $data);
    }
}
